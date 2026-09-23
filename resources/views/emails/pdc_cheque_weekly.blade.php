<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDC Cheque</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="row">
        <div class="col-md-12" style="height: 300px;overflow: scroll;">
            <h3><strong>Cheques Notifications</strong></h3>
            <table id="example2" class="bg-white table-bordered table-striped table-sm" role="grid" aria-describedby="example1_info" style="width:100%;">
                <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Company</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Type</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Bank/Cheque Clearance Date</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Bank/Cheque No</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Total</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cheques as $key => $cheque)
                        <tr class="odd">
                            <td>
                                @if(isset($cheque->transactionable->quotation->company))
                                    <a href="{{ route('companies.show', $cheque->transactionable->quotation->company->id ) }}" class='btn btn-ghost-success'>
                                    {{ $cheque->transactionable->quotation->company->name }}
                                    </a>
                                @endif
                                @if(isset($cheque->transactionable->lpoout->vendor))
                                    <a href="{{ route('vendors.show', $cheque->transactionable->lpoout->vendor->id ) }}" class='btn btn-ghost-success'>
                                    {{ $cheque->transactionable->lpoout->vendor->name }}
                                    </a>
                                @endif
                            </td>
                            <td>{{ isset($cheque->transactionable->quotation) ? "Receipt" : "Payment"}}</td>
                            <td>{{$cheque->clearance_date}}</td>
                            <td>{{$cheque->payment_no}}</td>
                            <td>{{$cheque->total}}</td>
                            <td>@include('components.datatables_status', [
                                'msg' => ($cheque->status) ? 'complete' : 'pending',
                                'type' => ($cheque->status) ? 'success' : 'danger',
                                ])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
