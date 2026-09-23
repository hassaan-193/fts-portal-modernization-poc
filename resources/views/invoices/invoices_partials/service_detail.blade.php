<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Service Info:</h3>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            <tbody id="serviceTableBody">
                @if($invoice && $invoice->invoice_service_details)
                    @foreach($invoice->invoice_service_details as $item)
                        <tr>
                            <td><input type="text" value="{{ $item->description }}" name="description[]" class="form-control"></td>
                            <td><input type="text" value="{{ $item->amount }}" name="service_amount[]" class="form-control"></td>
                            <td><i style="display:inline" class="deleteBtn fa fa-trash  btn-danger btn-xs" title="delete row" data-toggle="tooltip"></i></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="col-md-12">
        <div class="pull-right">
            <button type="button" id="addRowService" class="btn btn-info btn-sm mb-3">Add Row</button>
        </div>
    </div>
</div>

@section('scripts')
@parent
    <script type="text/javascript">
       $('#addRowService').click(function (){
            $('#serviceTableBody').append('<tr><td><input type="text" name="description[]" class="form-control"></td><td><input type="text" name="service_amount[]" value="0" class="form-control"> </td><td><i style="display:inline" class="deleteBtn fa fa-trash  btn-danger btn-xs" title="delete row" data-toggle="tooltip"></i></td></tr>');
        });
        $('#serviceTableBody').on('click','.deleteBtn', function () {
            $(this).closest("tr").remove();
        });
    </script>
@endsection
