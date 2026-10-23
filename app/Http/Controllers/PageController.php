<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', ['products' => Product::active()->get()]);
    }

    public function services()
    {
        return view('pages.services', ['products' => Product::active()->get()]);
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('pages.product', [
            'product' => $product,
            'others' => Product::active()->where('id', '!=', $product->id)->get(),
        ]);
    }

    public function howItWorks()
    {
        return view('pages.how');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function sendContact(Request $request): RedirectResponse
    {
        // Hidden "website" field is a honeypot: real people never fill it in.
        if ($request->filled('website')) {
            return back()->with('status', 'Thanks! We will get back to you shortly.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ], ['email.required_without' => 'Please give us an email or a phone number so we can reply.']);

        ContactMessage::create($data);

        return back()->with('status', 'Thanks! We will get back to you shortly.');
    }
}
