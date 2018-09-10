<script type="text/javascript">
    if (typeof confirmDelete !== 'function') {
        window.confirmDelete = function(link) {
            var message = confirm('Are you sure you want to delete the "'+ link.dataset.title +'" item?');
            if(message === true) {
                return document.getElementById(link.dataset.formName).submit();
            }
            return false;
        };
    }
</script>

@if(!isset($title))
    {{ ($title = $model->title) ? '':'' }}
    @empty($title)
        {{ ($title = 'Model id: '.$model->id) ? '':'' }}
    @endempty
@endif

{{ ($customFormName = 'form-'.$type.'-'.$model->id.'-'.mt_rand(100, 999)) ? '':'' }}

{{ ($linkOptions = [
        'class' => 'btn btn-danger btn-sm',
        'title' => $title,
        'onclick' => 'confirmDelete(this);',
        'data-title' => $title,
        'data-form-name' => $customFormName
    ]) ? '':'' }}
{{ Html::link( '#' , 'Delete' , $linkOptions) }}
{{ Form::model( $model, ['route' => [$type.'.destroy', $model->getKey()], 'method' => 'delete', 'id' => $customFormName, 'style' => 'display:none;'] ) }}
{{ Form::submit( 'Delete', [ 'class' => 'btn btn-link' ] ) }}
{{ Form::close() }}
