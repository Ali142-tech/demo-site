@extends('layouts.panel')
@section('title', 'Enquiries')

@section('content')
<div class="main-top"><h1>Website enquiries</h1></div>

@if ($messages->isEmpty())
    <div class="card empty"><h3>No enquiries yet</h3><p class="mb-0">Messages sent from the Contact page appear here.</p></div>
@endif

@foreach ($messages as $m)
    <div class="card" style="{{ $m->read_at ? 'opacity:.75' : 'box-shadow:5px 5px 0 var(--yellow)' }}">
        <div class="card-head">
            <div>
                <h3>{{ $m->subject ?: 'Enquiry from '.$m->name }} @unless ($m->read_at)<span class="badge warn plain">New</span>@endunless</h3>
                <span class="muted small">{{ $m->name }} · {{ $m->created_at->format('d M Y, g:i a') }}</span>
            </div>
            <div class="flex gap-1">
                <form method="POST" action="{{ route('admin.messages.read', $m) }}">@csrf @method('PUT')<button class="btn btn-sm">{{ $m->read_at ? 'Mark unread' : 'Mark read' }}</button></form>
                <form method="POST" action="{{ route('admin.messages.destroy', $m) }}" data-confirm="This enquiry will be permanently deleted." data-confirm-title="Delete this message?" data-confirm-ok="Yes, delete">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
            </div>
        </div>
        <p style="white-space:pre-line">{{ $m->message }}</p>
        <div class="flex gap-2 wrap small">
            @if ($m->email)<a class="link" href="mailto:{{ $m->email }}">{{ $m->email }}</a>@endif
            @if ($m->phone)<a class="link" href="tel:{{ $m->phone }}">{{ $m->phone }}</a>@endif
        </div>
    </div>
@endforeach
@include('partials.pager', ['paginator' => $messages])
@endsection
