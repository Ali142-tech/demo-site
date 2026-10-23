@extends('layouts.panel')
@section('title', $quote->reference)

@section('content')
@php($editable = in_array($quote->status, ['pending', 'quoted', 'declined'], true))
<div class="main-top">
    <div>
        <a class="link small" href="{{ route('admin.quotes.index') }}">← All requests</a>
        <h1 class="mt-1">{{ $quote->title }}</h1>
        <div class="flex items-center gap-2 wrap mt-1">
            <span class="muted">{{ $quote->reference }}</span>
            <span class="badge {{ $quote->statusTone() }}">{{ $quote->statusLabel() }}</span>
        </div>
    </div>
    @if ($quote->hasQuote())
        <a class="btn btn-sm" href="{{ route('admin.quotes.print', $quote) }}" target="_blank">@include('partials.icon', ['name' => 'print']) Print quotation</a>
    @endif
</div>

<div class="card">@include('quotes._tracker')</div>

<div class="cols">
    <div>
        {{-- ===== Quotation builder ===== --}}
        @if ($editable && $quote->hasQuote())
            <div class="flash ok" role="status">
                @include('partials.icon', ['name' => 'check'])
                <span><b>Quotation sent</b> {{ $quote->quoted_at?->diffForHumans() }} · {{ money($quote->quoted_total) }} ·
                     ($quote->status === 'declined') the customer <b>declined</b> it.  ($quote->isQuoteExpired()) it has <b>expired</b>.  waiting for the customer's reply. </span>
            </div>
            <div class="quote-box mb-3">
                <div class="qh"><b>Quotation sent to customer</b><span style="opacity:.8">{{ $quote->quote_valid_until ? 'Valid until '.$quote->quote_valid_until->format('d M Y') : '' }}</span></div>
                <div class="qb">@include('quotes._lines', ['quote' => $quote])
                    @if ($quote->quote_notes)<div class="mt-3"><div class="label">Note</div><p class="muted mb-0" style="white-space:pre-line">{{ $quote->quote_notes }}</p></div>@endif
                </div>
            </div>
            <details class="revise mb-3" @if ($errors->any()) open @endif>
                <summary class="btn btn-sm">@include('partials.icon', ['name' => 'settings']) Revise quotation</summary>
        @endif

        @if ($editable)
            <form method="POST" action="{{ route('admin.quotes.quotation', $quote) }}" id="quotation-form" data-currency="{{ config('business.currency') }}" class="card flush" style="box-shadow:6px 6px 0 var(--cyan)">
                @csrf @method('PUT')
                <div class="card-head"><h3>{{ $quote->hasQuote() ? 'Revise quotation' : 'Prepare quotation' }}</h3></div>
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Item</th><th class="num">Qty</th><th style="width:150px">Unit price ({{ config('business.currency') }})</th><th class="num">Amount</th></tr></thead>
                    <tbody>
                    @foreach ($quote->items as $item)
                        <tr data-line data-qty="{{ $item->quantity }}">
                            <td><b>{{ $item->name }}</b>@if ($item->specs)<br><span class="muted small" style="white-space:pre-line">{{ $item->specs }}</span>@endif
                                @if ($item->product?->starting_price)<br><span class="muted small">List price from {{ money($item->product->starting_price) }}/{{ $item->product->price_unit }}</span>@endif</td>
                            <td class="num">{{ number_format($item->quantity) }}</td>
                            <td><input class="input price-input" type="number" step="0.01" min="0" name="prices[{{ $item->id }}]" value="{{ old('prices.'.$item->id, $item->unit_price ?? $item->product?->starting_price) }}" required></td>
                            <td class="num line-total">—</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
                <div style="padding:1.2rem 1.5rem">
                    <div class="form-row form-row-3">
                        <div class="field"><label for="delivery_fee">Delivery fee</label><input class="input" type="number" step="0.01" min="0" id="delivery_fee" name="delivery_fee" value="{{ old('delivery_fee', $quote->delivery_fee) }}"></div>
                        <div class="field"><label for="discount">Discount</label><input class="input" type="number" step="0.01" min="0" id="discount" name="discount" value="{{ old('discount', $quote->discount) }}"></div>
                        <div class="field"><label for="quote_valid_until">Valid until</label><input class="input" type="date" id="quote_valid_until" name="quote_valid_until" value="{{ old('quote_valid_until', ($quote->quote_valid_until ?? now()->addDays(config('business.quote_validity_days')))->toDateString()) }}"></div>
                    </div>
                    <div class="field"><label for="quote_notes">Note to customer <span class="muted">(payment terms, delivery time…)</span></label><textarea class="input" id="quote_notes" name="quote_notes" rows="3">{{ old('quote_notes', $quote->quote_notes) }}</textarea></div>
                    <div class="totals" style="margin-bottom:1rem">
                        <div><span>Subtotal</span><span id="t-sub">—</span></div>
                        <div class="grand"><span>Total</span><span id="t-total">—</span></div>
                    </div>
                    <button class="btn btn-accent">{{ $quote->hasQuote() ? 'Send revised quotation' : 'Send quotation to customer' }} @include('partials.icon', ['name' => 'arrow'])</button>
                </div>
            </form>
            @if ($quote->hasQuote())
            </details>
            @endif
        @else
            <div class="quote-box mb-3">
                <div class="qh"><b>Quotation</b><span style="opacity:.8">{{ $quote->quote_valid_until ? 'Valid until '.$quote->quote_valid_until->format('d M Y') : '' }}</span></div>
                <div class="qb">@include('quotes._lines', ['quote' => $quote])
                    @if ($quote->quote_notes)<div class="mt-3"><div class="label">Note</div><p class="muted mb-0" style="white-space:pre-line">{{ $quote->quote_notes }}</p></div>@endif
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-head"><h3>Conversation</h3></div>
            @include('quotes._chat', ['routePrefix' => 'admin'])
        </div>
    </div>

    <div>
        <div class="card">
            <h3>Update status</h3>
            <form method="POST" action="{{ route('admin.quotes.status', $quote) }}">
                @csrf @method('PUT')
                <div class="field">
                    <select class="input" name="status">
                        @foreach (\App\Models\QuoteRequest::STATUSES as $key => $s)
                            <option value="{{ $key }}" @selected($quote->status === $key)>{{ $s['label'] }}</option>
                        @endforeach
                    </select>
                    <div class="hint">The customer is emailed when the status changes.</div>
                </div>
                <button class="btn btn-primary btn-sm btn-block">Save status</button>
            </form>
        </div>

        <div class="card">
            <h3>Customer</h3>
            <dl class="dl">
                <dt>Name</dt><dd><a class="link" href="{{ route('admin.customers.show', $quote->user) }}">{{ $quote->user->displayName() }}</a></dd>
                @if ($quote->user->organization)<dt>Contact</dt><dd>{{ $quote->user->name }}</dd>@endif
                <dt>Type</dt><dd>{{ $quote->user->accountTypeLabel() }}</dd>
                <dt>Phone</dt><dd><a href="tel:{{ $quote->user->phone }}">{{ $quote->user->phone }}</a> · <a class="link" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $quote->user->phone) }}">WhatsApp</a></dd>
                <dt>Email</dt><dd>{{ $quote->user->email }}</dd>
            </dl>
        </div>

        <div class="card">
            <h3>Request details</h3>
            <dl class="dl">
                <dt>Submitted</dt><dd>{{ $quote->created_at->format('d M Y, g:i a') }}</dd>
                <dt>Needed by</dt><dd>{{ $quote->needed_by?->format('d M Y') ?? 'Not specified' }}</dd>
                <dt>Delivery</dt><dd>{{ $quote->delivery_address ?: 'Collect from shop' }}</dd>
                @if ($quote->notes)<dt>Notes</dt><dd style="white-space:pre-line">{{ $quote->notes }}</dd>@endif
            </dl>
        </div>

        <div class="card">
            <h3>Customer files</h3>
            @include('quotes._files', ['routePrefix' => 'admin'])
        </div>
    </div>
</div>
@endsection
