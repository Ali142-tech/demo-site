@php
    $steps = ['Submitted', 'Quoted', 'Accepted', 'In production', 'Ready', 'Completed'];
    $map = ['pending' => 0, 'quoted' => 1, 'accepted' => 2, 'in_production' => 3, 'ready' => 4, 'completed' => 5, 'declined' => 1, 'cancelled' => $quote->hasQuote() ? 1 : 0];
    $at = $map[$quote->status] ?? 0;
    $stopped = in_array($quote->status, ['declined', 'cancelled'], true);
@endphp
<div class="tracker {{ $stopped ? 'stopped' : '' }}" role="list" aria-label="Order progress">
    @foreach ($steps as $i => $label)
        <div class="t {{ $i < $at || ($i === $at && $quote->status === 'completed') ? 'done' : ($i === $at ? 'now' : '') }}" role="listitem">
            <i>{{ ($i < $at || ($i === $at && $quote->status === 'completed')) ? '✓' : ($stopped && $i === $at ? '✕' : $i + 1) }}</i>
            {{ $label }}
        </div>
    @endforeach
</div>
