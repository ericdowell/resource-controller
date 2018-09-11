<div class="form-group{{ $errors->has($errorName ?? $name) ? ' is-invalid' : '' }}">
    {{ Form::label($name, isset($field_label) ? $field_label : ucfirst($name), ['class' => 'col-md-12 control-label', 'required' => 'required']) }}
    <div class="col-md-12">
        @includeFirst(['vendor.resource-controller.include.form.errors', 'resource-controller::include.form.errors'], ['field' => $errorName ?? $name])
        @if(!isset($field_options))
            {{ ($field_options = ['class' => 'form-control', 'required' => 'required']) ? '':'' }}
        @endif
        {{ Form::password($name, $field_options) }}
    </div>
</div>
