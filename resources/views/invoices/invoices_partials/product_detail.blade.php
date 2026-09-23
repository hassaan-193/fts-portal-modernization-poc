<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Order Info:</h3>
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
                @if($products)
                    @foreach($products as $item)
                        <tr>
                            <td><input type="text" value="{{ $item->product }}" name="product[]" class="form-control"></td>
                            <td><input type="text" name="unit[]" value="{{ $item->unit }}" class="form-control"></td>
                            <td><input type="text" name="qty[]" value="{{ $item->qty }}" class="form-control"></td>
                            <td><input type="text" name="rate[]" value="{{ $item->rate }}" class="form-control"></td>
                            <td><input type="text" value="{{ $item->amount }}" name="amount[]" class="form-control"></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@section('scripts')
@parent
    <script type="text/javascript">
        $('#orderTableBody').on("change",'[name^=qty]',function(){
            var qty = $(this).val() != '' ? parseFloat($(this).val()) : 0;
            var price = $(this).closest('td').next().find('input').val();
            price = price != '' ? parseFloat(price) : 0
            var total = qty * price;
            $(this).closest('td').next().next().find('input').val(total);
        });

        $('#orderTableBody').on("change",'[name^=rate]',function(){
            var price = $(this).val() != '' ? parseFloat($(this).val()) : 0;
            var qty = $(this).closest('td').prev().find('input').val();
            qty = qty != '' ? parseFloat(qty) : 0
            var total = qty * price;
            $(this).closest('td').next().find('input').val(total);
        });
    </script>
@endsection
