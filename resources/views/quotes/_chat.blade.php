@if ($quote->messages->isEmpty())
    <p class="muted small">No messages yet. Ask a question or request a change below.</p>
@else
    <div class="chat">
        @foreach ($quote->messages as $m)
            @php($mine = $m->user_id === auth()->id())
            <div class="bubble {{ $mine ? 'me' : ($m->user->isAdmin() ? 'shop' : '') }}">
                <small>{{ $mine ? 'You' : ($m->user->isAdmin() ? config('business.name') : $m->user->displayName()) }} · {{ $m->created_at->format('d M, g:i a') }}</small>
                <p>{{ $m->body }}</p>
            </div>
        @endforeach
    </div>
@endif
<form method="POST" action="{{ route($routePrefix.'.quotes.message', $quote) }}" class="chat-form">
    @csrf
    <textarea class="input" name="body" rows="2" placeholder="Write a message…" required maxlength="2000"></textarea>
    <button class="btn btn-primary btn-sm">Send</button>
</form>
