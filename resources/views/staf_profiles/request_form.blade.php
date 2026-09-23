
@extends('layouts.master')
@section('css')
@parent
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700;900&display=swap');

    *,
    body {
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        -moz-osx-font-smoothing: grayscale;
    }

    html,
    body {
        height: 100%;
        background-color: #020404;
        overflow-x: hidden;
    }

    .form-items label {
        margin-top: 10px;
    }

    .form-holder {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        /* min-height: 100vh; */
    }

    .form-holder .form-content {
        position: relative;
        text-align: center;
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-align-items: center;
        align-items: center;
        padding: 60px;
    }

    .form-content .form-items {
        border: 3px solid #fff;
        background-color: #ed1e24;
        padding: 40px;
        display: inline-block;
        width: 100%;
        min-width: 540px;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        text-align: left;
        -webkit-transition: all 0.4s ease;
        transition: all 0.4s ease;
    }

    .form-content h3 {
        color: #fff;
        text-align: left;
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .form-content h3.form-title {
        margin-bottom: 30px;
    }

    .form-content p {
        color: #fff;
        text-align: left;
        font-size: 17px;
        font-weight: 300;
        line-height: 20px;
        margin-bottom: 30px;
    }


    .form-content label,
    .was-validated .form-check-input:invalid~.form-check-label,
    .was-validated .form-check-input:valid~.form-check-label {
        color: #fff;
    }

    .form-content input[type=text],
    .form-content input[type=password],
    .form-content input[type=email],
    .form-content select {
        width: 100%;
        padding: 9px 20px;
        text-align: left;
        border: 0;
        outline: 0;
        border-radius: 6px;
        background-color: #fff;
        font-size: 15px;
        font-weight: 300;
        color: #8D8D8D;
        -webkit-transition: all 0.3s ease;
        transition: all 0.3s ease;
        margin-top: 0px !important;
    }


    .btn-primary {
        background-color: #f7901d;
        outline: none;
        border: 0px;
        box-shadow: none;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: #020404;
        outline: none !important;
        border: none !important;
        box-shadow: none;
    }

    .form-content textarea {
        position: static !important;
        width: 100%;
        padding: 8px 20px;
        border-radius: 6px;
        text-align: left;
        background-color: #fff;
        border: 0;
        font-size: 15px;
        font-weight: 300;
        color: #8D8D8D;
        outline: none;
        resize: none;
        height: 120px;
        -webkit-transition: none;
        transition: none;
        margin-bottom: 14px;
    }

    .form-content textarea:hover,
    .form-content textarea:focus {
        border: 0;
        background-color: #ebeff8;
        color: #8D8D8D;
    }

    .mv-up {
        margin-top: -9px !important;
        margin-bottom: 8px !important;
    }

    .invalid-feedback {
        display: block;
        color: #ebeff8;
    }

    .valid-feedback {
        color: #2acc80;
    }

    .panel {
        display: none;
    }

    #one {
        display: block;
    }

    @media only screen and (max-width: 600px) {
        .form-content .form-items {
            min-width: 80%;
        }
    }
</style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/stafprofile.front')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/stafprofile.front')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">@lang('models/stafprofile.front') Request Form</h3>
            </div>
            <div class="card-body table-responsive">
                <form class="requires-validation" method="post" action="{{ route('staff_request_form') }}">
                    @csrf
                    <input type="hidden" name="staf_id" value={{ $staff_id }} />
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Request Type*</label>
                                {!! Form::select('type',
                                    [ 'Sick Leave' => 'Sick Leave', 'Annual Leave' => 'Annual Leave', 'Emergency Leave' => 'Emergency Leave', 'Day off/personal leave' => 'Day off/personal leave', 'Maternity Leave' => 'Maternity Leave', 'Ticket allowance' => 'Ticket allowance', 'Leave Salary' => 'Leave Salary', 'Increment' => 'Increment', 'Half Day leave' => 'Half Day leave', 'Paternity Leave' => 'Paternity Leave', 'Devices' => 'Devices', 'Letters' => 'Letters', 'Advance Money' => 'Advance Money', 'Salary' => 'Salary', 'Others' => 'Others'], null , ['class' => $errors->has('staf_type') ? 'form-control is-invalid' : 'form-control', 'id' => 'sectionChooser'])
                                !!}
                                @if ($errors->has('type'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('type') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 panel date_fields">
                            <div class="form-group">
                                <label for="">Start Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    {!! Form::text('start_date', null, ['class' => $errors->has('start_date') ? 'form-control is-invalid datePicker' : 'form-control datePicker']) !!}
                                    @if ($errors->has('start_date'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 panel date_fields">
                            <div class="form-group">
                                <label for="">End Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    {!! Form::text('end_date', null, ['class' => $errors->has('end_date') ? 'form-control is-invalid datePicker' : 'form-control datePicker']) !!}
                                    @if ($errors->has('end_date'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 panel" id="advance_money">
                            <div class="form-group">
                                <label for="">Advance Money</label>
                                <input class="form-control" type="text" name="advance_money"
                                    placeholder="Advance Money">
                            </div>
                        </div>

                        <div class="col-md-4 panel" id="devices_type">
                            <div class="form-group">
                                <label for="">Device Type*</label>
                                {!! Form::select('device_type',
                                    [ '' => '-- Select Device --', 'Laptop' => 'Laptop', 'SIM' => 'SIM', 'Mobile' => 'Mobile', 'Mobile plan upgrade' => 'Mobile plan upgrade'], null , ['class' => 'form-control', 'id' => 'deviceChooser'])
                                !!}
                            </div>
                        </div>

                        <div class="col-md-4 panel" id="letters_type">
                            <div class="form-group">
                                <label for="">Letter Type*</label>
                                {!! Form::select('letter_type',
                                    [ '' => '-- Select Letter --', 'NOC' => 'NOC', 'Gatepass' => 'Gatepass', 'Salary Certificate' => 'Salary Certificate', 'Employment Certificate' => 'Employment Certificate'], null , ['class' => 'form-control', 'id' => 'letterChooser'])
                                !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Note</label>
                                <textarea class="form-control" name="note" placeholder="Note"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::submit('Submit', ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
                                <a href="{{ route('staf.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('.datePicker').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
        $('#sectionChooser').change(function() {
            $('.panel').hide();
            $('.panel').find('input').val('');
            $('.panel').find('select').val('');

            var myID = $(this).val();
            const hideDateFields = ['Advance Money', 'Salary', 'Leave Salary', 'Others'];

            if (!hideDateFields.includes(myID)) {
                $('.date_fields').show();
            }

            if (myID === 'Advance Money') {
                $('#advance_money').show();
            }

            if (myID === 'Devices') {
                $('#devices_type').show();
            }

            if (myID === 'Letters') {
                $('#letters_type').show();
            }
        });

        // Trigger the change event after the event listener is defined
        $('#sectionChooser').trigger('change');
    </script>
@endsection