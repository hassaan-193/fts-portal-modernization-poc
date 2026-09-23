<div class="row" id="quotation_product_div" style="display: none;">
    <div class="col-md-12">
        <div class="form-group">
            <h3>Products:</h3>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table" width="100%">
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
            <tbody id="productTableBody">
                @if($quotation && $quotation->products)
                    @foreach($quotation->products as $item)
                        <tr>
                            <td>
                                <select name="product_id[]" class="form-control product-select">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $product->id == $item->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" value="{{ $item->quantity }}" name="quantity[]" class="form-control qty-input"></td>
                            <td><input type="text" value="{{ $item->unit_price }}" name="unit_price[]" class="form-control price-input" ></td>
                            <td><input type="text" value="{{ $item->total_price }}" name="total_price[]" class="form-control total-input" readonly></td>
                            <td><i class="deleteBtn fa fa-trash btn-danger btn-xs" title="delete row" data-toggle="tooltip"></i></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="col-md-12">
        <div class="pull-right">
            <button type="button" id="addRowProduct" class="btn btn-info btn-sm mb-3">Add Row</button>
        </div>
    </div>
</div>

@section('scripts')
@parent
    <script type="text/javascript">
        const products = @json($products->keyBy('id'));

        function generateProductOptions() {
            let options = '<option value="">Select a product</option>';
            @foreach($products as $product)
                options += `<option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>`;
            @endforeach
            return options;
        }

        function updateUnitPriceAndTotal(row) {
            const productSelect = row.find('.product-select');
            const unitPriceInput = row.find('.price-input');
            const qtyInput = row.find('.qty-input');
            const totalInput = row.find('.total-input');

            const unitPrice = productSelect.find(':selected').data('price') || 0;
            unitPriceInput.val(unitPrice);

            const qty = qtyInput.val();
            const total = unitPrice * qty;
            totalInput.val(total);
        }

        function addRow() {
            const newRow = $(`
                <tr>
                    <td>
                        <select name="product_id[]" class="form-control product-select">
                            ${generateProductOptions()}
                        </select>
                    </td>
                    <td><input type="text" name="quantity[]" value="1" class="form-control qty-input"></td>
                    <td><input type="text" name="unit_price[]" class="form-control price-input" ></td>
                    <td><input type="text" name="total_price[]" class="form-control total-input" readonly></td>
                    <td><i class="deleteBtn fa fa-trash btn-danger btn-xs" title="delete row" data-toggle="tooltip"></i></td>
                </tr>`);
            $('#productTableBody').append(newRow);

            $('.product-select').select2({
                theme: 'bootstrap4',
                width: '100%'
            }); // Initialize Select2 for new elements

            // Add event listeners to the new row
            newRow.find('.product-select').change(function() {
                updateUnitPriceAndTotal(newRow);
            });

            newRow.find('.qty-input').on('input', function() {
                updateUnitPriceAndTotal(newRow);
            });
        }

        $('#addRowProduct').click(function() {
            addRow();
        });

        $('#productTableBody').on('click', '.deleteBtn', function() {
            $(this).closest("tr").remove();
        });

        // Initialize Select2 on existing elements and set up event listeners
        $(document).ready(function() {
            $('.product-select').select2();
            $('#productTableBody').find('tr').each(function() {
                const row = $(this);
                row.find('.product-select').change(function() {
                    updateUnitPriceAndTotal(row);
                });
                row.find('.qty-input').on('input', function() {
                    updateUnitPriceAndTotal(row);
                });
            });

            $('#quotation_type_id').trigger('change');
        });
    </script>
@endsection
