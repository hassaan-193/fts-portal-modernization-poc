<div class="col-sm-3">
    <!-- File Field -->
    <div class="form-group">
        {!! Form::label('type',__('models/document.fields.type')) !!}
        <div class="input-group">
            @if (str_contains(url()->current(), '/edit'))
                {!! Form::select('type', config('enum.expirey_document_types'), null , ['class' => $errors->has('type') ? 'form-control is-invalid' : 'form-control', 'id' => 'exp_file_type']) !!}
            @else
                {!! Form::select('type_1', config('enum.expirey_document_types'), null , ['class' => $errors->has('type') ? 'form-control is-invalid' : 'form-control', 'id' => 'exp_file_type']) !!}
            @endif
           
            @if ($errors->has('type'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('type') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="col-sm-2">
    <!-- File Field -->
    <div class="form-group">
        {!! Form::label('type',__('models/document.fields.name')) !!}
        <div class="input-group">
            @if (str_contains(url()->current(), '/edit'))
                {!! Form::text('name', null, ['class' => $errors->has('name') ? 'form-control is-invalid' : 'form-control' ,'id'=>"doc_name"]) !!}
            @else
                {!! Form::text('name_1', null, ['class' => $errors->has('name') ? 'form-control is-invalid' : 'form-control' ,'id'=>"doc_name"]) !!}
            @endif
          
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="col-sm-2">
    <div class="form-group">
        {!! Form::label('date',  __('models/document.fields.date')) !!}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                </span>
            </div>
            @if (str_contains(url()->current(), '/edit'))
                {!! Form::text('date', null, ['class' => $errors->has('date') ? 'form-control is-invalid' : 'form-control', 'id' => 'doc_date']) !!}
            @else
                {!! Form::text('date_1', null, ['class' => $errors->has('date') ? 'form-control is-invalid' : 'form-control', 'id' => 'doc_date']) !!}
            @endif

            @if ($errors->has('date'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('date') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="col-sm-3">
    <!-- File Field -->
    <div class="form-group">
        <div class="custom-file mt-4">
            @if (str_contains(url()->current(), '/edit'))
                 {!! Form::file('file', ['class' => 'custom-file-input','id'=>'exp_file_name']) !!}
                 {!! Form::label('file', $document->type, ['class' => 'custom-file-label','id'=>'exp_file_label']) !!}
            @else
                 {!! Form::file('file_1', ['class' => 'custom-file-input','id'=>'exp_file_name']) !!}
                 {!! Form::label('file', 'Vehicle Expires', ['class' => 'custom-file-label','id'=>'exp_file_label']) !!}
            @endif

        </div>
    </div>
</div>
@if (str_contains(url()->current(), '/create'))
<span id='exp_del'><a class="btn btn-danger mt-4"><i class="fa fa-trash"></i> Delete Document</a></span>
@endif
@section('scripts')
    @parent
    <script type="text/javascript">
        var expFileName=$('#exp_file_name');
        var expFileLabel=$('#exp_file_label');
        var expDocName=$('#doc_name');
        $(document).ready(function() {
            $('#exp_file_type').on('change',function(){
                var optionSelected = $("option:selected", this);
                var valueSelected = this.value;
                expFileLabel.text(valueSelected);
            })
            $('#exp_del').click(function(){
                $('#exp_file_row').remove();
            })
        });
    </script>
@endsection



