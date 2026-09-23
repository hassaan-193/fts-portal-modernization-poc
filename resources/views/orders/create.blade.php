@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 text-dark font-weight-bold">Create Order</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <form method="POST" action="{{ route('orders.store') }}">
        @csrf
        <div class="card card-primary card-maroon">
            <div class="card-header"><h3 class="card-title">Create Order</h3></div>
            <div class="card-body">
                <!-- Order Details -->
                <div class="card mb-3">
                    <div class="card-header">Order Details</div>
                    <div class="card-body row">
                        <div class="form-group col-md-3"><label>Date</label><input type="date" name="date" class="form-control" required></div>
                        <div class="form-group col-md-3"><label>TRN</label><input type="text" name="trn" class="form-control" required></div>
                        <div class="form-group col-md-6">
                            <label>Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-control" required>
                                <option value="">Select Vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3"><label>ATTN</label><input type="text" name="attn" class="form-control"></div>
                        <div class="form-group col-md-3"><label>Ship To</label><input type="text" name="ship_to" class="form-control"></div>
                        <div class="form-group col-md-6"><label>Address</label><textarea name="address" class="form-control"></textarea></div>
                        <div class="form-group col-md-3"><label>Contact</label><input type="text" name="contact" class="form-control"></div>
                        <div class="form-group col-md-3"><label>Ref No</label><input type="text" name="ref_no" class="form-control"></div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <span>Order Items</span>
                        <button type="button" class="btn btn-sm btn-success" id="addRow">Add Item</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="item-rows">
                                @include('orders.partials.item_row', ['index' => 0])
                            </tbody>
                            <tfoot>
        <tr>
          <td colspan="4" class="text-right font-weight-bold">Subtotal</td>
          <td><input name="subtotal" id="subtotal" readonly class="form-control"></td>
        </tr>
        <tr>
          <td colspan="4" class="text-right font-weight-bold">Discount</td>
          <td>
            <input name="discount" id="discount" class="form-control item-calc" value="0" step="any" />
          </td>
        </tr>
        <tr>
          <td colspan="4" class="text-right font-weight-bold">VAT (5%)</td>
          <td><input name="vat" id="vat" readonly class="form-control"></td>
        </tr>
        <tr>
          <td colspan="4" class="text-right font-weight-bold">Total w/ VAT</td>
          <td><input name="total_with_vat" id="total_with_vat" readonly class="form-control"></td>
        </tr>
      </tfoot>
                        </table>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Save Order</button>
                </div>
            </div>
        </div>

        <!-- Hidden Template -->
        <template id="row-template">
            {!! str_replace('__INDEX__', '__INDEX__', view('orders.partials.item_row', ['index' => '__INDEX__'])->render()) !!}
        </template>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIdx = 1;
    const addBtn = document.getElementById('addRow');
    const template = document.getElementById('row-template').innerHTML;
    const tbody = document.getElementById('item-rows');

    addBtn.addEventListener('click', () => {
        const newRow = template.replace(/__INDEX__/g, rowIdx);
        tbody.insertAdjacentHTML('beforeend', newRow);
        rowIdx++;
    });

    // Remove row
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    });

    // Recalculate totals on input
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('item-calc')) {
            calculateTotals();
        }
    });

    function calculateTotals() {
        let subtotal = 0;

        document.querySelectorAll('#item-rows tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
            const price = parseFloat(row.querySelector('.price')?.value) || 0;
            const total = qty * price;
            row.querySelector('.total').value = total.toFixed(2);
            subtotal += total;
        });

        const discount = parseFloat(document.getElementById('discount')?.value) || 0;
        const afterDiscount = subtotal - discount;
        const vat = afterDiscount * 0.05;
        const totalWithVat = afterDiscount + vat;

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('vat').value = vat.toFixed(2);
        document.getElementById('total_with_vat').value = totalWithVat.toFixed(2);
    }

    // Initial total calculation
    calculateTotals();
});
</script>
@endsection

@section('scripts')
@parent
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $('#vendor_id').select2({
            theme: 'bootstrap4',
            placeholder: "Select a Vendor",
            allowClear: true
        })
    </script>
@endsection
