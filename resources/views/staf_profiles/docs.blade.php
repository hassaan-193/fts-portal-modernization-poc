<div class="col-sm-5">
    <!-- File Field -->
    <div class="form-group">
        {!! Form::label('type', 'Document Type') !!}
        <div class="input-group">
            {!! Form::select('type', config('enum.document_types'), null, ['class' => 'form-control file-type']) !!}

            @if ($errors->has('type'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('type') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="col-md-5 col-sm-6">
    <!-- File Field -->
    <div class="form-group">
        <div class="custom-file mt-4">
            {!! Form::file('passport', ['class' => 'custom-file-input file-name']) !!}
            {!! Form::label('passport', 'passport', ['class' => 'custom-file-label file-label']) !!}            
        </div>
    </div>
</div>
<span id='del'><a class="btn btn-danger mt-4"><i class="fa fa-trash"></i> Delete Document</a></span>
@section('scripts')
    @parent
    <script type="text/javascript">
        var fileName=$('#file_name');
        var fileLabel=$('#file_label');
        $(document).ready(function() {
            $('#file_type').on('change',function(){
                var optionSelected = $("option:selected", this);
                var valueSelected = this.value;
                fileName.attr('name',valueSelected);
                fileLabel.text(valueSelected);
                fileLabel.attr('for',valueSelected);
            })
            $('#del').click(function(){
                $('#file_row').remove();
            })
        });
    </script>
@endsection
