<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FTS Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css">

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
</head>

<body>
    <div class="form-body">
        <div class="row">
            <div class="form-holder">
                <div class="form-content">
                    <div class="form-items">
                        <h3>FTS Form Today</h3>
                        <p>Fill in the data below.</p>
                        <form class="requires-validation" method="post" action="{{ route('labor_request_form') }}">
                            @csrf
                            <div class="col-md-12">
                                <label for="">Name</label>
                                <input class="form-control" type="text" name="name" placeholder="Full Name">
                                <div class="valid-feedback">Username field is valid!</div>
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="">Labor ID*</label>
                                <input class="form-control" type="text" name="labor_id" placeholder="Labor ID">
                                <div class="valid-feedback">Labor ID field is valid!</div>
                                @if ($errors->has('labor_id'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('labor_id') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="">Request Type*</label>
                                <select class="form-select mt-3" id="sectionChooser" name="type">
                                    <option selected value="Sick Leave">Sick Leave</option>
                                    <option value="Annual Leave">Annual Leave</option>
                                    <option value="Advance Money">Advance Money</option>
                                    <option value="Tools">Tools</option>
                                    <option value="Accoumdation ">Accoumdation Tools</option>
                                </select>
                                <div class="valid-feedback">You selected a Request Type!</div>
                                @if ($errors->has('type'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('type') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-12 panel" id="leave_dates">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Start Date</label>
                                        <input class="form-control" type="date" name="start_date"
                                            placeholder="Start Date">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">End Date</label>
                                        <input class="form-control" type="date" name="end_date"
                                            placeholder="End Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 panel" id="advance_money">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">Advance Money</label>
                                        <input class="form-control" type="text" name="advance_money"
                                            placeholder="Advance Money">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="">Note</label>
                                <textarea class="form-control" name="note" placeholder="Note"></textarea>
                                <div class="valid-feedback">Note field is valid!</div>
                            </div>
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="form-button mt-3">
                                <button id="submit" type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        $('#sectionChooser').change(function() {
            $('.panel').hide();
            $('.panel').find('input').val('');

            var myID = $(this).val();
            if (myID === 'Sick Leave' || myID === 'Annual Leave') {
                $('#leave_dates').show();
            }
            if (myID === 'Advance Money') {
                $('#advance_money').show();
            }
        });

        // Trigger the change event after the event listener is defined
        $('#sectionChooser').trigger('change');
    </script>


</body>

</html>
