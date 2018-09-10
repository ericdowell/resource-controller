@extends(config('resource-controller.app-layout', 'layouts.app'))

@section('content')
    <div class="card">
        <div class="card-header">
            <strong>{{ $instance->title }}</strong> |
            @if(Auth::user() && Auth::user()->id == $instance->user->id)
                <a href="{{ route($type.'.edit', $instance->id) }}" title="Edit {{ $instance->title }}">Edit</a>
            @endif
        </div>
        <div class="card-body">
            @yield('show')
        </div>
    </div>
@endsection
