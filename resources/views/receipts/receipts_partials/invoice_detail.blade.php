<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Invoices:</h3>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th></th>
                <th>@lang('models/receipts.invoices_detail.no')</th>
                <th>@lang('models/receipts.invoices_detail.quotation')</th>
                <th>@lang('models/receipts.invoices_detail.amount')</th>
            </tr>
            <tbody id="invoiceTableBody">
                @if(isset($receipt->transactionable))
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" disabled checked type="checkbox" id="{{$receipt->transactionable->invoice_no}}" value="">
                                <label for="{{$receipt->transactionable->invoice_no}}" class="custom-control-label"></label>
                            </div>
                        </td>
                        <td>{{$receipt->transactionable->invoice_no}}</td>
                        <td>{{$receipt->transactionable->total_amount}}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
@parent
    <script type="text/javascript">
        $("#search").click(function(){
            var $search_button = $(this)
            $search_button.attr('disabled',true)

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: window.baseUrl(`/receipts/company_invoices`),
                data: {company_id:$('#company_id').val()},
                success:function(data){
                    $('#invoiceTableBody').html('')

                    data.forEach(item => {
                        var row = `
                        <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="invoices[]" id="${item.invoice_no}" value="${item.id}">
                                    <label for="${item.invoice_no}" class="custom-control-label"></label>
                                </div>
                            </td>
                            <td>
                                ${item.invoice_no}
                            </td>
                            <td>
                                ${item.quotation.name}
                            </td>
                            <td>
                                ${item.total_amount}
                            </td>
                        </tr>
                        `;
                        $('#invoiceTableBody').append(row)
                    });
                    $search_button.attr('disabled',false)
                },
                error: function (xhr) {
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText);
                        console.log(errors)
                    }
                }
            });
        });
        $('#invoiceTableBody').on('change', 'input[type="checkbox"]', function() {
            var total_amount = 0
            $('input[type=checkbox]').each(function () {
                if(this.checked)
                    total_amount += parseFloat($(this).closest('tr').children('td').eq(3).text())
            });
            $('#total_amount').val(total_amount)
        });
        $(function() {
            $('input[type="checkbox"]').trigger("change");
        });
    </script>
@endsection
