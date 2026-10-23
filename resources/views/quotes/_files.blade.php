@if ($quote->files->isEmpty())
    <p class="muted small mb-0">No files attached.</p>
@else
    @php($images = $quote->files->filter->isImage())
    @if ($images->isNotEmpty())
        <div class="thumbs">
            @foreach ($images as $f)
                <a href="{{ route($routePrefix.'.quotes.files.download', [$quote, $f, 'view' => 1]) }}" target="_blank" rel="noopener" title="{{ $f->original_name }}">
                    <img src="{{ route($routePrefix.'.quotes.files.download', [$quote, $f, 'view' => 1]) }}" alt="{{ $f->original_name }}" loading="lazy">
                </a>
            @endforeach
        </div>
    @endif
    <ul class="file-list">
        @foreach ($quote->files as $f)
            <li>
                @include('partials.icon', ['name' => 'file'])
                <a href="{{ route($routePrefix.'.quotes.files.download', [$quote, $f] + ($f->isViewable() ? ['view' => 1] : [])) }}" @if ($f->isViewable()) target="_blank" rel="noopener" @endif title="{{ $f->original_name }}">{{ $f->original_name }}</a>
                <small>{{ $f->humanSize() }}</small>
                <a href="{{ route($routePrefix.'.quotes.files.download', [$quote, $f]) }}" title="Download" aria-label="Download {{ $f->original_name }}" style="flex:none">@include('partials.icon', ['name' => 'upload'])</a>
            </li>
        @endforeach
    </ul>
@endif
