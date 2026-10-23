<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesQuoteFiles;
use App\Http\Controllers\Controller;
use App\Models\QuoteFile;
use App\Models\QuoteRequest;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuoteController extends Controller
{
    use HandlesQuoteFiles;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q'));

        $quotes = QuoteRequest::with('user')->withCount('items')
            ->when($status && isset(QuoteRequest::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                        ->orWhere('organization', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            }))
            ->latest()->paginate(15)->withQueryString();

        $counts = QuoteRequest::select('status', DB::raw('count(*) as c'))->groupBy('status')->pluck('c', 'status');

        return view('admin.quotes.index', compact('quotes', 'status', 'search', 'counts'));
    }

    public function show(QuoteRequest $quote)
    {
        return view('admin.quotes.show', [
            'quote' => $quote->load(['user', 'items.product', 'files', 'messages']),
        ]);
    }

    public function saveQuotation(Request $request, QuoteRequest $quote): RedirectResponse
    {
        abort_unless(in_array($quote->status, ['pending', 'quoted', 'declined'], true), 403, 'This order is already in progress.');

        $data = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'quote_valid_until' => ['nullable', 'date', 'after_or_equal:today'],
            'quote_notes' => ['nullable', 'string', 'max:3000'],
        ], ['prices.*.required' => 'Enter a unit price for every item.']);

        DB::transaction(function () use ($quote, $data) {
            foreach ($quote->items as $item) {
                $item->update(['unit_price' => $data['prices'][$item->id] ?? 0]);
            }

            $quote->delivery_fee = $data['delivery_fee'] ?? 0;
            $quote->discount = $data['discount'] ?? 0;
            $quote->load('items');

            $quote->update([
                'delivery_fee' => $quote->delivery_fee,
                'discount' => $quote->discount,
                'quoted_total' => $quote->computeTotal(),
                'quote_notes' => $data['quote_notes'] ?? null,
                'quote_valid_until' => $data['quote_valid_until'] ?? now()->addDays(config('business.quote_validity_days')),
                'quoted_at' => now(),
                'responded_at' => null,
                'status' => 'quoted',
            ]);
        });

        Notifier::quoteReady($quote->load('user'));

        return back()->with('status', 'Quotation sent to the customer.');
    }

    public function updateStatus(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $status = $request->validate([
            'status' => ['required', Rule::in(array_keys(QuoteRequest::STATUSES))],
        ])['status'];

        if ($status === 'quoted' && ! $quote->hasQuote()) {
            return back()->withErrors(['status' => 'Prepare the quotation first.']);
        }

        if ($status !== $quote->status) {
            $quote->update(['status' => $status]);
            Notifier::statusChanged($quote->load('user'));
        }

        return back()->with('status', 'Status updated.');
    }

    public function message(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $body = $request->validate(['body' => ['required', 'string', 'max:2000']])['body'];

        $quote->messages()->create(['user_id' => $request->user()->id, 'body' => $body]);
        Notifier::newMessage($quote->load('user'), $request->user(), $body);

        return back()->with('status', 'Message sent.');
    }

    public function download(Request $request, QuoteRequest $quote, QuoteFile $file)
    {
        return $this->downloadFile($request, $quote, $file);
    }

    public function print(QuoteRequest $quote)
    {
        abort_unless($quote->hasQuote(), 404);

        return view('quotes.print', ['quote' => $quote->load(['items', 'user'])]);
    }
}
