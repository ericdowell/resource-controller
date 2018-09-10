@if(Auth::user())
    <a class="btn btn-primary" href="{{ route($type.'.create') }}">Create {{ $typeName }}</a>
@endif