@extends('layouts.panel')
@section('title', 'Products')

@section('content')
<div class="main-top">
    <h1>Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-accent">@include('partials.icon', ['name' => 'plus']) Add product</a>
</div>

<div class="card flush">
    <div class="table-wrap"><table class="table">
        <thead><tr><th>Product</th><th class="num">Starting price</th><th class="num">Min. qty</th><th>Visible</th><th></th></tr></thead>
        <tbody>
        @foreach ($products as $p)
            <tr>
                <td><a class="row-link" href="{{ route('admin.products.edit', $p) }}">{{ $p->name }}</a><br><span class="muted small">{{ $p->tagline }}</span></td>
                <td class="num">{{ $p->starting_price ? money($p->starting_price).' / '.$p->price_unit : '—' }}</td>
                <td class="num">{{ number_format($p->min_quantity) }}</td>
                <td><span class="badge {{ $p->is_active ? 'ok' : 'done' }}">{{ $p->is_active ? 'Shown on site' : 'Hidden' }}</span></td>
                <td class="right"><a class="btn btn-sm" href="{{ route('admin.products.edit', $p) }}">Edit</a></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endsection
