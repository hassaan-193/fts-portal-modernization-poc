<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Order Info:</h3>
            {{-- {{$errors}} --}}
            @if ($errors->has('product.0'))
                <strong style="color: red;">At least one Product/Service Description is required</strong>
            @endif
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th>@lang('models/invoiceProductDetails.fields.product')</th>
                <th>@lang('models/invoiceProductDetails.fields.unit')</th>
                <th>@lang('models/invoiceProductDetails.fields.qty')</th>
                <th>@lang('models/invoiceProductDetails.fields.rate')</th>
                <th>@lang('models/invoiceProductDetails.fields.amount')</th>
            </tr>
            <tbody id="orderTableBody">
                @if(($products))
                    @foreach($invoiceRequest->request_products as $item)
                        <tr>
                            <td><input type="text" value="{{ $item->product }}" name="product[]" class="form-control"></td>
                            <td><input type="text" name="unit[]" value="{{ $item->unit }}" class="form-control"></td>
                            <td><input type="text" name="qty[]" value="{{ $item->qty }}" class="form-control"></td>
                            <td><input type="text" name="rate[]" value="{{ $item->rate }}" class="form-control"></td>
                            <td><input type="text" readonly value="{{ $item->amount }}" name="amount[]" class="form-control"></td>
                            <td><i style="display:inline" class="deleteBtn fa fa-trash  btn-danger btn-xs" title="delete row" data-toggle="tooltip"></i></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="col-md-12">
        <div class="pull-right">
            <button type="button" id="addRowOrder" class="btn btn-info btn-sm mb-3">Add Row</button>
        </div>
    </div>
</div>

@section('scripts')
@parent
    <script type="text/javascript">
       $('#addRowOrder').click(function (){
            $('#orderTableBody').append('<tr><td><input type="text" name="product[]" class="form-control"></td><td><input type="text" name="unit[]" class="form-control"></td><td><input type="text" name="qty[]" value="0" class="form-control"></td><td><input type="text" name="rate[]" value="0" class="form-control"></td><td><input type="text" readonly name="amount[]" value="0" class="form-control"> </td><td><i style="display:inline" class="deleteBtn fa fa-trash  btn-danger btn-xs" title="delete row" data-toggle="tooltip"></i></td></tr>');
        });
        $('#orderTableBody').on('click','.deleteBtn', function () {
            $(this).closest("tr").remove();
        });
        $('#orderTableBody').on("change",'[name^=qty]',function(){
            var qty = $(this).val() != '' ? parseFloat($(this).val()) : 0;
            var price = $(this).closest('td').next().find('input').val();
            price = price != '' ? parseFloat(price) : 0
            var total = qty * price;
            if(parseFloat($("#net_total_amount").text()) + total <= parseFloat($("input[name='temp_sum_dif']").val())){
                $(this).closest('td').next().next().find('input').val(total);
            }
            else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Quotations Amount Exceeded, Can only create invoice of amount '+ (parseFloat($("#net_total_amount").text()) - parseFloat($("input[name='temp_sum_dif']").val()))
                })
                $(this).val(0);
                $(this).closest('td').next().find('input').val(0);
                $(this).closest('td').next().next().find('input').val(0);
            }
        });
        $('#orderTableBody').on("change",'[name^=rate]',function(){
            var price = $(this).val() != '' ? parseFloat($(this).val()) : 0;
            var qty = $(this).closest('td').prev().find('input').val();
            qty = qty != '' ? parseFloat(qty) : 0
            var total = qty * price;
            if(parseFloat($("#net_total_amount").text()) + total <= parseFloat($("input[name='temp_sum_dif']").val())){
                $(this).closest('td').next().find('input').val(total);
            }
            else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Quotations Amount Exceeded, Can only create invoice of amount '+ (parseFloat($("#net_total_amount").text()) - parseFloat($("input[name='temp_sum_dif']").val()))
                });
                $(this).val(0);
                $(this).closest('td').prev().find('input').val(0);
                $(this).closest('td').next().find('input').val(0);
            }
        });
    </script>
@endsection
