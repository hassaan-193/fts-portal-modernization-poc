@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('models/lpoouts.plural')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{!! route('projects.index') !!}">@lang('models/projects.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('models/lpoouts.plural')</li>
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
                    {!! Form::open(['route' => ['projects.store_lpoout', $project->id ] ]) !!}
                        <div class="form-group col-sm-6" >
                            {!! Form::label('lpoout_id', __('models/projects.lpoout').':') !!}
                            {!! Form::select('lpoout_id', $lpooutItems, null, ['class' => ($errors->has('lpoout_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'lpoout_id']) !!}
                            @if ($errors->has('lpoout_id'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('lpoout_id') }}</strong>
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
                                    <th>@lang('models/projects.fields.name')</th>
                                </tr>
                                <tbody>
                                    @if($project && $project->lpoouts)
                                        @foreach($project->lpoouts as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    {!! Form::open(['route' => ['projects.delete_lpoout', $project->id], 'method' => 'delete']) !!}
                                                        <input type="hidden" name="lpoout_id" value="{{$item->id}}">
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
        $('#lpoout_id').select2({
            theme: 'bootstrap4'
        })
    </script>
@endsection
