@extends('layouts.panel')
@section('title', $product->exists ? 'Edit '.$product->name : 'Add product')

@php
    $featuresText = old('features', implode("\n", $product->features ?? []));
    $optionsText = old('options', collect($product->options ?? [])->map(fn ($c, $l) => $l.': '.implode(', ', $c))->implode("\n"));
@endphp

@section('content')
<div class="main-top">
    <div><a class="link small" href="{{ route('admin.products.index') }}">← Products</a><h1 class="mt-1">{{ $product->exists ? 'Edit '.$product->name : 'Add a product' }}</h1></div>
</div>

<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="card" novalidate>
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <div class="form-row">
        <div class="field"><label for="name">Name</label><input class="input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>@error('name')<div class="error">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="icon">Illustration</label>
            <select class="input" id="icon" name="icon">@foreach ($icons as $i)<option value="{{ $i }}" @selected(old('icon', $product->icon) === $i)>{{ ucfirst($i) }}</option>@endforeach</select>
        </div>
    </div>
    <div class="field"><label for="tagline">Short tagline</label><input class="input @error('tagline') is-invalid @enderror" id="tagline" name="tagline" value="{{ old('tagline', $product->tagline) }}" required>@error('tagline')<div class="error">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="description">Description</label><textarea class="input @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea>@error('description')<div class="error">{{ $message }}</div>@enderror</div>

    <div class="form-row form-row-3">
        <div class="field"><label for="starting_price">Starting price</label><input class="input" type="number" step="0.01" min="0" id="starting_price" name="starting_price" value="{{ old('starting_price', $product->starting_price) }}"></div>
        <div class="field"><label for="price_unit">Price per (unit)</label><input class="input" id="price_unit" name="price_unit" value="{{ old('price_unit', $product->price_unit) }}" required><div class="hint">piece, page, roll, card…</div></div>
        <div class="field"><label for="min_quantity">Minimum quantity</label><input class="input" type="number" min="1" id="min_quantity" name="min_quantity" value="{{ old('min_quantity', $product->min_quantity) }}" required></div>
    </div>

    <div class="form-row">
        <div class="field"><label for="features">Highlights <span class="muted">(one per line)</span></label><textarea class="input" id="features" name="features" rows="5">{{ $featuresText }}</textarea></div>
        <div class="field"><label for="options">Options customers can pick <span class="muted">(Label: choice, choice)</span></label><textarea class="input" id="options" name="options" rows="5" placeholder="Size: A4, A3&#10;Colour: Black &amp; white, Full colour">{{ $optionsText }}</textarea></div>
    </div>

    <div class="form-row">
        <div class="field"><label for="sort_order">Sort order</label><input class="input" type="number" min="0" id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}"><div class="hint">Lower numbers appear first.</div></div>
        <div class="field"><span class="label">Visibility</span><label class="check" style="margin-top:.7rem"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> Show on the website</label></div>
    </div>

    <button class="btn btn-primary">{{ $product->exists ? 'Save changes' : 'Add product' }}</button>
</form>
@endsection
