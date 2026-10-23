@extends('layouts.panel')
@section('title', $quote->reference)

@section('content')
<div class="main-top">
    <div>
        <a class="link small" href="{{ route('customer.quotes.index') }}">← All orders</a>
        <h1 class="mt-1">{{ $quote->title }}</h1>
        <div class="flex items-center gap-2 wrap mt-1">
            <span class="muted">{{ $quote->reference }}</span>
            <span class="badge {{ $quote->statusTone() }}">{{ $quote->statusLabel() }}</span>
        </div>
    </div>
    <div class="flex gap-1 wrap">
        <form method="POST" action="{{ route('customer.quotes.reorder', $quote) }}">@csrf
            <button class="btn btn-sm">@include('partials.icon', ['name' => 'repeat']) Order again</button>
        </form>
        @if ($quote->hasQuote())
            <a class="btn btn-sm" href="{{ route('customer.quotes.print', $quote) }}" target="_blank">@include('partials.icon', ['name' => 'print']) Print quotation</a>
        @endif
    </div>
</div>

<div class="card">@include('quotes._tracker')</div>

<div class="cols">
    <div>
        @if ($quote->hasQuote())
            <div class="quote-box mb-3">
                <div class="qh">
                    <b>Quotation</b>
                    <span>
                        @if ($quote->isQuoteExpired())
                            <span class="badge bad">Expired {{ $quote->quote_valid_until->format('d M Y') }}</span>
                        @elseif ($quote->quote_valid_until)
                            <span style="opacity:.8">Valid until {{ $quote->quote_valid_until->format('d M Y') }}</span>
                        @endif
                    </span>
                </div>
                <div class="qb">
                    @include('quotes._lines', ['quote' => $quote])
                    @if ($quote->quote_notes)
                        <div class="mt-3"><div class="label">Note from us</div><p class="muted mb-0" style="white-space:pre-line">{{ $quote->quote_notes }}</p></div>
                    @endif
                </div>
                @if ($quote->canCustomerRespond())
                    <div class="decision">
                        <p>Happy with this quotation? Accept it and we will start work.</p>
                        <form method="POST" action="{{ route('customer.quotes.respond', $quote) }}" class="flex gap-1">@csrf
                            <button name="decision" value="decline" class="btn btn-sm btn-danger" data-confirm="You can still message us afterwards if you would like a revised price." data-confirm-title="Decline this quotation?" data-confirm-ok="Yes, decline">Decline</button>
                            <button name="decision" value="accept" class="btn btn-sm btn-ok">Accept quotation</button>
                        </form>
                    </div>
                @elseif ($quote->isQuoteExpired())
                    <div class="decision"><p>This quotation has expired. Send us a message and we will refresh the prices.</p></div>
                @endif
            </div>
        @else
            <div class="card alert-card">
                <h3>⏳ We are preparing your quotation</h3>
                <p class="mb-0">We have your request and will send an itemised price soon — you will get an email as soon as it is ready.</p>
            </div>
        @endif

        <div class="card flush">
            <div class="card-head"><h3>Items requested</h3></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Item</th><th class="num">Qty</th><th>Details</th></tr></thead>
                    <tbody>
                    @foreach ($quote->items as $item)
                        <tr>
                            <td><b>{{ $item->name }}</b></td>
                            <td class="num">{{ number_format($item->quantity) }}</td>
                            <td class="muted small" style="white-space:pre-line">{{ $item->specs ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-head"><h3>Messages</h3></div>
            @include('quotes._chat', ['routePrefix' => 'customer'])
        </div>
    </div>

    <div>
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
            <h3>Files</h3>
            @include('quotes._files', ['routePrefix' => 'customer'])
            <form method="POST" action="{{ route('customer.quotes.files.add', $quote) }}" enctype="multipart/form-data" class="mt-2">
                @csrf
                <label class="drop" style="padding:1rem"><b>+ Add more files</b><input type="file" name="files[]" multiple></label>
                <ul class="file-list picked"></ul>
                <button class="btn btn-sm btn-block mt-1">Upload</button>
            </form>
        </div>

        @if ($quote->canCustomerCancel())
            <form method="POST" action="{{ route('customer.quotes.cancel', $quote) }}" data-confirm="We will stop work on this request. You can always place a new one." data-confirm-title="Cancel this request?" data-confirm-ok="Yes, cancel it" data-confirm-cancel="Keep it">@csrf
                <button class="btn btn-danger btn-sm btn-block">Cancel this request</button>
            </form>
        @endif
    </div>
</div>
@endsection
