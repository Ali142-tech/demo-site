@if (session('status'))
    <div class="flash ok" role="status">
        @include('partials.icon', ['name' => 'check'])
        <span>{{ session('status') }}</span>
    </div>
@endif
@if ($errors->any())
    <div class="flash bad" role="alert">
        <div>
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
