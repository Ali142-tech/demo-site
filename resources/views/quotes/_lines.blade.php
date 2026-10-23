{{-- Read-only priced lines + totals, shared by customer, admin and print views --}}
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Item</th><th class="num">Qty</th><th class="num">Unit price</th><th class="num">Amount</th></tr></thead>
        <tbody>
        @foreach ($quote->items as $item)
            <tr>
                <td><b>{{ $item->name }}</b>@if ($item->specs)<br><span class="muted small" style="white-space:pre-line">{{ $item->specs }}</span>@endif</td>
                <td class="num">{{ number_format($item->quantity) }}</td>
                <td class="num">{{ $item->unit_price !== null ? money($item->unit_price, 2) : '—' }}</td>
                <td class="num"><b>{{ $item->lineTotal() !== null ? money($item->lineTotal()) : '—' }}</b></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="totals">
    <div><span>Subtotal</span><span>{{ money($quote->itemsSubtotal()) }}</span></div>
    @if ($quote->delivery_fee > 0)<div><span>Delivery</span><span>{{ money($quote->delivery_fee) }}</span></div>@endif
    @if ($quote->discount > 0)<div><span>Discount</span><span>− {{ money($quote->discount) }}</span></div>@endif
    <div class="grand"><span>Total</span><span>{{ money($quote->quoted_total) }}</span></div>
</div>
