<div class="form-group{{ $errors->has($name) ? ' is-invalid' : '' }}">
    {{ Form::label($name, isset($field_label) ? $field_label : ucfirst($name), ['class' => 'col-md-12 control-label']) }}
    <div class="col-md-12">
        @includeFirst(['vendor.resource-controller.include.form.errors', 'resource-controller::include.form.errors'], ['field' => $name])
        @if(!isset($field_options))
            {{ ($field_options = ['class' => 'form-control']) ? '':'' }}
        @endif
        @if(isset($field_required))
            {{ ($field_options = array_merge($field_options, ['required' => 'required'])) ? '':'' }}
        @endif
        {{ Form::date($name, null, ['class' => 'form-control']) }}
    </div>
</div>
