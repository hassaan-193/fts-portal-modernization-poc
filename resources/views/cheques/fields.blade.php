@section('css')
@parent
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection

<!-- Account Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('account_id', __('models/cheques.fields.account_id').':') !!}
    {!! Form::select('account_id', $accountItems, null, ['class' => ($errors->has('account_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'account_id']) !!}
    @if ($errors->has('account_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('account_id') }}</strong>
        </span>
    @endif
</div>

@section('scripts')
@parent
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $('#account_id').select2({
        theme: 'bootstrap4'
        })
    </script>
@endsection


<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('cheques.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>
