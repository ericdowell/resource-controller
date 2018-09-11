<div class="form-group">
    <div class="col-md-6 col-md-offset-4">
        @if(!isset($field_options))
            {{ ($field_options = ['class' => 'btn btn-primary']) ? '':'' }}
        @endif
        @if(isset($field_required))
            {{ ($field_options = array_merge($field_options, ['required' => 'required'])) ? '':'' }}
        @endif
        {{ Form::submit($btnMessage, $field_options) }}
    </div>
</div>
