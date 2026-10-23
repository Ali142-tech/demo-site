@extends('layouts.panel')
@section('title', 'New request')

@php
    $productsData = $products->map(fn ($p) => ['id' => $p->id, 'slug' => $p->slug, 'name' => $p->name, 'options' => $p->options ?: new stdClass, 'min_quantity' => $p->min_quantity, 'price_unit' => $p->price_unit])->values();
@endphp

@section('content')
<div class="main-top">
    <div>
        <h1>New quotation request</h1>
        <p class="muted mb-0">Tell us what you need — we will reply with an itemised price.</p>
    </div>
</div>

<form method="POST" action="{{ route('customer.quotes.store') }}" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="cols">
        <div>
            <div class="card">
                <h3>1. Request details</h3>
                <div class="field">
                    <label for="title">Give this request a name</label>
                    <input class="input @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. New session ID cards &amp; ties 2027" required>
                    @error('title')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="needed_by">Needed by <span class="muted">(optional)</span></label>
                        <input class="input @error('needed_by') is-invalid @enderror" type="date" id="needed_by" name="needed_by" value="{{ old('needed_by') }}" min="{{ now()->toDateString() }}">
                        @error('needed_by')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="delivery_address">Delivery address</label>
                        <input class="input" id="delivery_address" name="delivery_address" value="{{ old('delivery_address', $user->address) }}" placeholder="Leave blank if you will collect">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head"><h3>2. What do you need printed?</h3></div>
                <div id="items"
                     data-preselect="{{ $preselect }}"
                     data-old="{{ json_encode(collect(old('items', []))->values()->all()) }}"></div>
                @error('items')<div class="error mb-2">{{ $message }}</div>@enderror
                <button type="button" class="btn btn-sm" id="add-item">@include('partials.icon', ['name' => 'plus']) Add another item</button>
            </div>

            <div class="card">
                <h3>3. Files &amp; notes</h3>
                <div class="field">
                    <label class="drop">
                        @include('partials.icon', ['name' => 'upload'])
                        <b>Click to upload or drag &amp; drop</b>
                        <div class="muted small">Designs, logos, student lists, photos — PDF, Word, Excel, images, ZIP, AI/PSD/CDR · up to 20&nbsp;MB each</div>
                        <input type="file" name="files[]" multiple>
                    </label>
                    <ul class="file-list picked"></ul>
                    @error('files')<div class="error">{{ $message }}</div>@enderror
                    @error('files.*')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field mb-0">
                    <label for="notes">Anything else we should know?</label>
                    <textarea class="input" id="notes" name="notes" rows="4" placeholder="Colours, deadlines, special instructions…">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div>
            <div class="card sticky" style="box-shadow:6px 6px 0 var(--yellow)">
                <h3>Ready to send?</h3>
                <p class="muted small">We will review your request and send a quotation here and by email. There is no obligation to accept.</p>
                <button class="btn btn-accent btn-block">Send request @include('partials.icon', ['name' => 'arrow'])</button>
                <p class="hint center mt-2 mb-0">Sending as <b>{{ $user->displayName() }}</b></p>
            </div>
        </div>
    </div>
</form>

<script type="application/json" id="products-data">{!! json_encode($productsData, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

<template id="item-template">
    <div class="item-row">
        <div class="row-top">
            <div class="field mb-0">
                <label>Product</label>
                <select class="input product-select" name="items[__I__][product_id]">
                    <option value="">Something else (describe below)…</option>
                    @foreach ($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                </select>
            </div>
            <div class="field mb-0 qty">
                <label>Quantity</label>
                <input class="input qty-input" type="number" min="1" name="items[__I__][quantity]" value="1" required>
            </div>
            <button type="button" class="rm" aria-label="Remove item">×</button>
        </div>
        <div class="field mb-0 mt-2"><input class="input other-name" name="items[__I__][name]" placeholder="What is it? e.g. Custom wristbands"></div>
        <div class="hint min-hint"></div>
        <div class="opt-chips-box"></div>
        <div class="field mb-0 mt-1"><textarea class="input specs-input" name="items[__I__][specs]" rows="3" placeholder="Size, colour, finish, wording… (tap the options above to fill this in)"></textarea></div>
    </div>
</template>
@endsection
