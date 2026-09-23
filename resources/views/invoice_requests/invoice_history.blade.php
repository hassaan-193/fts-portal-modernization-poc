<div class="row" hidden id="invoice_history_div">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Invoices Detail:</h3>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th>#</th>
                <th>@lang('models/invoice_requests.invoice_history.date')</th>
                <th>@lang('models/invoice_requests.invoice_history.invoice')</th>
                <th>@lang('models/invoice_requests.invoice_history.government')</th>
                <th>@lang('models/invoice_requests.invoice_history.net')</th>
                <th>@lang('models/invoice_requests.invoice_history.vat')</th>
                <th>@lang('models/invoice_requests.invoice_history.amount')</th>
                <th>@lang('models/invoice_requests.invoice_history.status')</th>
            </tr>
            <tbody id="historyTableBody">
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
@parent
    <script type="text/javascript">
        $('#quotation_id').change(function (){
            $('#historyTableBody').html('');
            $('#invoice_history_div').attr('hidden',true);

            if(!$(this).val())
                return;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
            $.ajax({
                type:'GET',
                url: window.baseUrl(`/invoiceRequests/get/${ $(this).val() }/invoices`),
                success:function(response){
                    $('input[name="temp_sum_dif"]').val(response.sum);
                    var data= response.invoices;
                    var total = 0, totalVat = 0, totalNet = 0;
                    data.forEach((item,key) => {
                        totalNet += item.amount;
                        totalVat += item.vat;
                        total += item.total_amount;
                        var row = `
                        <tr>
                            <td>
                                ${key + 1}
                            </td>
                            <td>
                                ${item.start_date}
                            </td>
                            <td>
                                <a href="/invoices/${item.id}" class='btn btn-ghost-success' target="_blank">${item.invoice_no}</a>
                            </td>
                            <td>
                                ${item.government_fee}
                            </td>
                            <td>
                                ${item.amount}
                            </td>
                            <td>
                                ${item.vat}
                            </td>
                            <td>
                                ${item.total_amount}
                            </td>
                            <td>
                                ${item.status ? "<p style='color:green;'>GENERATED</p>" : (item.is_overdue ? "<p style='color:red;'>Pending (Overdue)</p>" : "<p style='color:red;'>Pending</p>") }
                            </td>
                        </tr>
                        `;
                        $('#historyTableBody').append(row);
                    });
                    // append total amount row
                    $('#historyTableBody').append(
                            `<tr>
                                <td></td><td></td><td></td>
                                <td><strong>Total Amounts:</strong></td>
                                <td>${totalNet.toFixed(2)}</td>
                                <td>${totalVat.toFixed(2)}</td>
                                <td id="net_total_amount" colspan="2">${total.toFixed(2)}</td>
                            </tr>`
                        );
                    $('#invoice_history_div').attr('hidden',false);
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
