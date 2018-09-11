@extends(config('resource-controller.app-layout', 'layouts.app'))

@section('content')
    @includeFirst(['vendor.resource-controller.include.table.index', 'resource-controller::include.table.index'], ['fields' => $instance->displayFields])
@endsection
