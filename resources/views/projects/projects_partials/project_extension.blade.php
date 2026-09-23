@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('models/extensions.plural')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{!! route('projects.index') !!}">@lang('models/projects.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('models/extensions.plural')</li>
                    </ol>
                </div>
            </div>
        </div>
  </div>
  <div class="content">
    @include('flash::message')
    <div class="row">
        <div class="col-md-6 offset-3">
            <div class="card card-primary card-outline card-maroon">
                <div class="card-header">
                    {!! Form::open(['route' => ['projects.store_extensions', $project->id ] ]) !!}
                        <div class="form-group col-sm-6">
                            {!! Form::label('name', __('models/extensions.fields.name').':') !!}
                            {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control']) !!}
                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-sm-6">
                            {!! Form::label('value', __('models/extensions.fields.value').':') !!}
                            {!! Form::text('value', null, ['class' => ($errors->has('value')) ? 'form-control is-invalid' : 'form-control']) !!}
                            @if ($errors->has('value'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('value') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-sm-6">
                            {!! Form::label('quotation_id', __('models/extensions.fields.quotation').':') !!}
                            {!! Form::select('quotation_id', $quotationItems, null, ['class' => ($errors->has('quotation_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'quotation_id']) !!}
                            @if ($errors->has('quotation_id'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('quotation_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger']) !!}
                            <a href="{{ route('projects.index') }}" class="btn text-maroon">@lang('crud.cancel')</a>
                        </div>
                    {!! Form::close() !!}
                </div>
                <div class="card-body table-responsive" >
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" width="100%">
                                <tr>
                                    <th>@lang('models/extensions.fields.name')</th>
                                    <th>@lang('models/extensions.fields.value')</th>
                                    <th>@lang('models/extensions.fields.quotation')</th>
                                </tr>
                                <tbody>
                                    @if($project && $project->extensions)
                                        @foreach($project->extensions as $item)
                                            <tr>
                                                <td>{{ $item->pivot->name }}</td>
                                                <td>{{ $item->pivot->value }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    {!! Form::open(['route' => ['projects.delete_extensions', $project->id], 'method' => 'delete']) !!}
                                                        <input type="hidden" name="id" value="{{$item->id}}">
                                                        {!! Form::button('<i class="fa fa-trash"></i>', [
                                                            'type' => 'submit',
                                                            'class' => 'btn btn-danger',
                                                            'onclick' => "return confirm('Are you sure?')"
                                                        ]) !!}
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $('#quotation_id').select2({
            theme: 'bootstrap4'
        })
    </script>
@endsection
