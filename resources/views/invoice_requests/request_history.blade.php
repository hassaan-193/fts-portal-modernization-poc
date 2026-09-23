<div class="row" hidden id="invoice_request_div">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Requests Detail:</h3>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th>#</th>
                <th>@lang('models/invoice_requests.request_history.date')</th>
                <th>@lang('models/invoice_requests.request_history.user')</th>
                <th>@lang('models/invoice_requests.request_history.note')</th>
                <th>@lang('models/invoice_requests.request_history.status')</th>
            </tr>
            <tbody id="requestTableBody">
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
@parent
    <script type="text/javascript">
        $('#quotation_id').change(function (){
            $('#requestTableBody').html('');
            $('#invoice_request_div').attr('hidden',true);

            if(!$(this).val())
                return;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
            $.ajax({
                type:'GET',
                url: window.baseUrl(`/invoiceRequests/get/${ $(this).val() }/invoice-requests`),
                success:function(data){
                    data.forEach((item,key) => {
                        var row = `
                        <tr>
                            <td>
                                ${key + 1}
                            </td>
                            <td>
                                ${item.created_at ? item.created_at : ''}
                            </td>
                            <td>
                                ${item.user ? item.user.name : ''}
                            </td>
                            <td>
                                ${item.note ? item.note : ''}
                            </td>
                            <td>
                                ${item.status ? "Completed" : "Pending"}
                            </td>
                        </tr>
                        `;
                        $('#requestTableBody').append(row)
                    });
                    $('#invoice_request_div').attr('hidden',false);
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
