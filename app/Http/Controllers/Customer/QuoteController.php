<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Concerns\HandlesQuoteFiles;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\QuoteFile;
use App\Models\QuoteRequest;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    use HandlesQuoteFiles;

    public function index(Request $request)
    {
        $status = $request->query('status');

        $quotes = $request->user()->quoteRequests()
            ->withCount('items')
            ->when($status && isset(QuoteRequest::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->latest()->paginate(10)->withQueryString();

        return view('customer.quotes.index', compact('quotes', 'status'));
    }

    public function create(Request $request)
    {
        return view('customer.quotes.create', [
            'products' => Product::active()->get(),
            'preselect' => $request->query('product'),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.name' => ['nullable', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'items.*.specs' => ['nullable', 'string', 'max:1500'],
            ...$this->fileRules(),
        ], [
            'items.required' => 'Add at least one item to your request.',
            'items.*.quantity.required' => 'Each item needs a quantity.',
        ]);

        $quote = DB::transaction(function () use ($request, $data) {
            $quote = $request->user()->quoteRequests()->create([
                'title' => $data['title'],
                'needed_by' => $data['needed_by'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? $request->user()->address,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $row) {
                $product = ! empty($row['product_id']) ? Product::find($row['product_id']) : null;
                $name = $product?->name ?? ($row['name'] ?? null);
                abort_if(! $name, 422, 'Pick a product or describe the item.');

                $quote->items()->create([
                    'product_id' => $product?->id,
                    'name' => $name,
                    'quantity' => $row['quantity'],
                    'specs' => $row['specs'] ?? null,
                ]);
            }

            $this->storeFiles($request, $quote);

            return $quote;
        });

        Notifier::newRequest($quote->load('user'));

        return redirect()->route('customer.quotes.show', $quote)
            ->with('status', "Request {$quote->reference} sent! We will prepare your quotation shortly.");
    }

    public function show(Request $request, QuoteRequest $quote)
    {
        $this->authorizeOwner($request, $quote);

        return view('customer.quotes.show', [
            'quote' => $quote->load(['items.product', 'files', 'messages']),
        ]);
    }

    public function print(Request $request, QuoteRequest $quote)
    {
        $this->authorizeOwner($request, $quote);
        abort_unless($quote->hasQuote(), 404);

        return view('quotes.print', ['quote' => $quote->load(['items', 'user'])]);
    }

    public function respond(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $this->authorizeOwner($request, $quote);
        $decision = $request->validate(['decision' => ['required', 'in:accept,decline']])['decision'];

        if (! $quote->canCustomerRespond()) {
            return back()->withErrors(['decision' => 'This quotation can no longer be accepted. Please message us for a fresh one.']);
        }

        $quote->update([
            'status' => $decision === 'accept' ? 'accepted' : 'declined',
            'responded_at' => now(),
        ]);

        Notifier::customerResponded($quote->load('user'));

        return back()->with('status', $decision === 'accept'
            ? 'Quotation accepted — we will start work and keep you updated here.'
            : 'Quotation declined. You can message us if you would like it revised.');
    }

    public function cancel(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $this->authorizeOwner($request, $quote);
        abort_unless($quote->canCustomerCancel(), 403);

        $quote->update(['status' => 'cancelled', 'responded_at' => now()]);

        return redirect()->route('customer.quotes.index')->with('status', "Request {$quote->reference} cancelled.");
    }

    public function message(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $this->authorizeOwner($request, $quote);
        $body = $request->validate(['body' => ['required', 'string', 'max:2000']])['body'];

        $quote->messages()->create(['user_id' => $request->user()->id, 'body' => $body]);
        Notifier::newMessage($quote->load('user'), $request->user(), $body);

        return back()->with('status', 'Message sent.');
    }

    public function addFiles(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $this->authorizeOwner($request, $quote);
        $request->validate($this->fileRules() + ['files' => ['required', 'array', 'max:10']]);

        $this->storeFiles($request, $quote);

        return back()->with('status', 'Files uploaded.');
    }

    public function download(Request $request, QuoteRequest $quote, QuoteFile $file)
    {
        $this->authorizeOwner($request, $quote);

        return $this->downloadFile($request, $quote, $file);
    }

    /** Copy an earlier request into a new one — handy for yearly school orders. */
    public function reorder(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $this->authorizeOwner($request, $quote);

        $copy = DB::transaction(function () use ($request, $quote) {
            $copy = $request->user()->quoteRequests()->create([
                'title' => $quote->title.' (repeat)',
                'delivery_address' => $quote->delivery_address,
                'notes' => $quote->notes,
            ]);

            foreach ($quote->items as $item) {
                $copy->items()->create($item->only(['product_id', 'name', 'quantity', 'specs']));
            }

            return $copy;
        });

        Notifier::newRequest($copy->load('user'));

        return redirect()->route('customer.quotes.show', $copy)
            ->with('status', 'We copied your earlier order. Attach any updated files and message us about changes.');
    }

    private function authorizeOwner(Request $request, QuoteRequest $quote): void
    {
        abort_unless($quote->user_id === $request->user()->id, 404);
    }
}
