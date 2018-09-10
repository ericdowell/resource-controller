<h1>{{ $typeName }}</h1>
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        @includeFirst(['vendor.resource-controller.include.table.thead', 'resource-controller::include.table.thead'])
        <tbody>
        @if( $models->count() != 0 )
            @foreach($models as $model)
                @include($type.'.tbody')
            @endforeach
        @else
            @includeFirst(['vendor.resource-controller.include.table.empty_tbody', 'resource-controller::include.table.empty_tbody'])
        @endif
        </tbody>
    </table>
</div>
{{ $models }}
@includeFirst(['vendor.resource-controller.include.btn.create', 'resource-controller::include.btn.create'])
