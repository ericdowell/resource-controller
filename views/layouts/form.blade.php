@extends(config('resource-controller.app-layout', 'layouts.app'))

@section('content')
    <div class="card">
        <div class="card-header">{{ $formHeader }}</div>
        <div class="card-body">
            @yield('form')
        </div>
    </div>
@endsection
