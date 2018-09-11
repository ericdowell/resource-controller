@if(Auth::user() && Auth::user()->id == $model->user->id)
    <td>
        <small>
            <a class="btn btn-info btn-sm" href="{{ route($type.'.edit', $model->id) }}">Edit</a>
        </small>
    </td>
    <td>
        <small>@includeFirst(['vendor.resource-controller.include.btn.delete', 'resource-controller::include.btn.delete'])</small>
    </td>
@else
    <td></td>
    <td></td>
@endif
