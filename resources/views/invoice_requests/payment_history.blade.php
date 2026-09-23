<div class="row" hidden id="payment_request_div">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Payment Detail:</h3>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th>#</th>
                <th>@lang('models/invoice_requests.payment_history.date')</th>
                <th>@lang('models/invoice_requests.payment_history.type')</th>
                <th>@lang('models/invoice_requests.payment_history.amount')</th>
                <th>@lang('models/invoice_requests.payment_history.note')</th>
                <th>@lang('models/invoice_requests.payment_history.status')</th>
            </tr>
            <tbody id="paymentTableBody">
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
@parent
    <script type="text/javascript">
        $('#quotation_id').change(function (){
            $('#paymentTableBody').html('');
            $('#payment_request_div').attr('hidden',true);

            if(!$(this).val())
                return;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
            $.ajax({
                type:'GET',
                url: window.baseUrl(`/invoiceRequests/get/${ $(this).val() }/invoice-payments`),
                success:function(data){
                    var total = 0;
                    data.forEach((item,key) => {
                        total += item.total;

                        var row = `
                        <tr>
                            <td>
                                ${key + 1}
                            </td>
                            <td>
                                ${item.date_time}
                            </td>
                            <td>
                                ${item.transaction_payment_type.name}
                            </td>
                            <td>
                                ${item.total ? item.total : 0}
                            </td>
                            <td>
                                ${item.note ? item.note : ''}
                            </td>
                            <td>
                                ${item.status ? "Completed" : "Pending"}
                            </td>
                        </tr>
                        `;
                        $('#paymentTableBody').append(row)
                    });
                    // append total amount row
                    $('#paymentTableBody').append(
                        `<tr>
                            <td></td><td></td>
                            <td><strong>Total:</strong></td>
                            <td colspan="4">${total.toFixed(2)}</td>
                        </tr>`
                    );
                    $('#payment_request_div').attr('hidden',false);
                },
                error: function (xhr) {
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText);
                        console.log(errors)
                    }
                }
            });
        });
    </script>
@endsection
