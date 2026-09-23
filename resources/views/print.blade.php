<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Print Table</title>
        <meta charset="UTF-8">
        <meta name=description content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <style>
            body {margin: 20px}
        </style>
    </head>
    <body>
        <div class="row" id="header">
            <div class="col-xs-12">
                <img
                src="{{public_path() . '/uploads/invoice_header.jpeg'}}" />
            </div>
        </div>
        <br>
        <br>
        <br>
        <div class="row mb-5">
            <div class="col-xs-6 ">
                <table class="table table-bordered mx-auto">
                    <thead>
                        <tr>
                            <td>
                                <strong>REPORT NAME </strong>
                            </td>
                            <td>
                                {{ isset($filters['report_name']) ? $filters['report_name'] : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>REPORT FOR</strong>
                            </td>
                            <td>
                                {{ isset($filters['customer']) ? $filters['customer'] : '' }}
                                {{ isset($filters['supplier']) ? $filters['supplier'] : '' }}
                                {{ isset($filters['account']) ? $filters['account'] : '' }}
                                {{ isset($filters['salesman']) ? $filters['salesman'] : '' }}
                                {{ isset($filters['bank']) ? $filters['bank'] : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong> REPORT BY</strong>
                            </td>
                            <td>
                                {{ isset($filters['by']) ? $filters['by'] : '' }}
                            </td>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="col-xs-6">
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <td>
                                <strong>DATE FROM </strong>
                            </td>
                            <td>
                                {{ isset($filters['from']) ? $filters['from'] : '' }}

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>TO DATE</strong>
                            </td>
                            <td>
                                {{ isset($filters['to']) ? $filters['to'] : '' }}
                            </td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <br>
        <br>
        @if (isset($header_data['opening_balance']))
        <hr>
            <div class="row">
                <div class="col-xs-4 col-xs-offset-3">
                    <table class="table table-bordered mx-auto ">
                        <tr>
                            <th>Opening Balance</th>
                            <td>{{ $header_data['opening_balance']}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
        <br>
        <br>
        <table class="table table-bordered table-condensed table-striped">
            @foreach($data as $row)
                @if ($row == reset($data))
                    <tr>
                        @foreach($row as $key => $value)
                            <th>{!! $key !!}</th>
                        @endforeach
                    </tr>
                @endif
                <tr>
                    @foreach($row as $key => $value)
                        @if(is_string($value) || is_numeric($value))
                            <td>{!! $value !!}</td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </table>
        @if (isset($footer_data))
        <hr>
            <div class="row">
                <div class="col-xs-4 col-xs-offset-3">
                    <table class="table table-bordered mx-auto ">
                        @foreach($footer_data as $key => $data)
                            <tr>
                                <th>{!! $key !!}</th>
                                <td>{!! $data !!}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endif
        <hr>
        <div class="row" id="signature">
            <div class="col-xs-6 col-xs-offset-2">
                <h5 >FOR ALL LAMAIE RENTAL</h5>
            </div>
            <div class="col-xs-4">
                <h5 >FOR CUSTOMER</h5>
            </div>
        </div>
        <br><br><br><br><br><br><br>
        <div class="row" id="user">
            <div class="col-md-12">
                {{ 'Printed BY: ' . Auth::user()->name .' - '. date("Y-m-d h:i:s a", time()) }}
            </div>
        </div>
        <br><br> <br><br> <br>
        <div class="row" id="footer">
            <div class="col-xs-12">
                <img src="{{ public_path() . '/uploads/invoice_footer.jpeg'}}" />
            </div>
        </div>
    </body>
</html>
