<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Quotation {{ $quote->reference }} — {{ config('business.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="sheet-page">
<div class="sheet-actions no-print">
    <button class="btn btn-primary btn-sm" onclick="window.print()">@include('partials.icon', ['name' => 'print']) Print / Save as PDF</button>
    <button class="btn btn-sm" onclick="history.length > 1 ? history.back() : window.close()">Back</button>
</div>

<div class="sheet">
    <div class="sheet-head">
        <div>
            <div class="brand" style="font-size:1.6rem">@include('partials.brand-mark')<span>{{ config('business.name') }}</span></div>
            <div class="muted small mt-1">{{ config('business.address') }}<br>{{ config('business.phone') }} · {{ config('business.email') }}</div>
        </div>
        <div class="right">
            <h1>QUOTATION</h1>
            <div class="muted">{{ $quote->reference }}</div>
            <div class="muted small">Issued {{ ($quote->quoted_at ?? $quote->created_at)->format('d M Y') }}</div>
            @if ($quote->quote_valid_until)<div class="small"><b>Valid until {{ $quote->quote_valid_until->format('d M Y') }}</b></div>@endif
        </div>
    </div>

    <div class="grid grid-2 mb-3" style="gap:2rem">
        <div>
            <div class="label muted small">PREPARED FOR</div>
            <b style="font-size:1.1rem">{{ $quote->user->displayName() }}</b><br>
            @if ($quote->user->organization){{ $quote->user->name }}<br>@endif
            {{ $quote->user->phone }}<br>{{ $quote->user->email }}
        </div>
        <div>
            <div class="label muted small">PROJECT</div>
            <b style="font-size:1.1rem">{{ $quote->title }}</b><br>
            @if ($quote->needed_by)Needed by {{ $quote->needed_by->format('d M Y') }}<br>@endif
            Deliver to: {{ $quote->delivery_address ?: 'Collect from shop' }}
        </div>
    </div>

    @include('quotes._lines', ['quote' => $quote])

    @if ($quote->quote_notes)
        <div class="mt-3"><div class="label">Notes</div><p style="white-space:pre-line">{{ $quote->quote_notes }}</p></div>
    @endif

    <div class="sheet-foot">Prices are in {{ config('business.currency') }}. To accept this quotation, log in to your {{ config('business.name') }} account or contact us. Thank you for your business!</div>
</div>
</body>
</html>
