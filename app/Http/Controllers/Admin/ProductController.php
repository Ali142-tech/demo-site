<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public const ICONS = ['copies', 'sheet', 'tie', 'badge', 'tape', 'card'];

    public function index()
    {
        return view('admin.products.index', ['products' => Product::orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(['is_active' => true, 'price_unit' => 'piece', 'min_quantity' => 1]), 'icons' => self::ICONS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->payload($request));

        return redirect()->route('admin.products.index')->with('status', "{$product->name} added.");
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product, 'icons' => self::ICONS]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->payload($request, $product));

        return redirect()->route('admin.products.index')->with('status', "{$product->name} updated.");
    }

    private function payload(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'icon' => ['required', Rule::in(self::ICONS)],
            'tagline' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:3000'],
            'features' => ['nullable', 'string', 'max:1500'],
            'options' => ['nullable', 'string', 'max:1500'],
            'starting_price' => ['nullable', 'numeric', 'min:0'],
            'price_unit' => ['required', 'string', 'max:30'],
            'min_quantity' => ['required', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Features: one per line.  Options: "Label: choice, choice" per line.
        $features = collect(preg_split('/\r?\n/', (string) ($data['features'] ?? '')))->map('trim')->filter()->values()->all();

        $options = [];
        foreach (preg_split('/\r?\n/', (string) ($data['options'] ?? '')) as $line) {
            if (str_contains($line, ':')) {
                [$label, $choices] = explode(':', $line, 2);
                $choices = collect(explode(',', $choices))->map('trim')->filter()->values()->all();
                if (trim($label) && $choices) {
                    $options[trim($label)] = $choices;
                }
            }
        }

        $slug = $product?->slug ?? Str::slug($data['name']);
        if (! $product) {
            $base = $slug;
            for ($i = 2; Product::where('slug', $slug)->exists(); $i++) {
                $slug = "{$base}-{$i}";
            }
        }

        return [
            ...collect($data)->except(['features', 'options'])->all(),
            'slug' => $slug,
            'features' => $features,
            'options' => $options,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
