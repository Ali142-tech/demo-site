@if ($quotes->isEmpty())
    <div class="empty">
        @include('partials.product-art', ['icon' => 'sheet'])
        <h3>No requests yet</h3>
        <p>Tell us what you need printed and we will send you a quotation.</p>
        <a href="{{ route('customer.quotes.create') }}" class="btn btn-accent">Create your first request</a>
    </div>
@else
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Request</th><th>Items</th><th>Needed by</th><th>Status</th><th class="num">Quotation</th><th></th></tr></thead>
            <tbody>
            @foreach ($quotes as $q)
                <tr>
                    <td><a class="row-link" href="{{ route('customer.quotes.show', $q) }}">{{ $q->title }}</a><br><span class="muted small">{{ $q->reference }} · {{ $q->created_at->format('d M Y') }}</span></td>
                    <td>{{ $q->items_count }}</td>
                    <td>{{ $q->needed_by?->format('d M Y') ?? '—' }}</td>
                    <td><span class="badge {{ $q->statusTone() }}">{{ $q->statusLabel() }}</span></td>
                    <td class="num">{{ $q->hasQuote() ? money($q->quoted_total) : '—' }}</td>
                    <td class="right"><a class="btn btn-sm" href="{{ route('customer.quotes.show', $q) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
