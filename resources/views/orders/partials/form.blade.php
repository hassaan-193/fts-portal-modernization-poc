<div class="card card-primary">
    <div class="card-body">
        <!-- Order Header -->
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="date">Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', $order->date ?? now()->toDateString()) }}" required>
            </div>

            <div class="form-group col-md-3">
                <label for="trn">TRN</label>
                <input type="text" name="trn" class="form-control" value="{{ old('trn', $order->trn ?? '') }}" required>
            </div>

            <div class="form-group col-md-3">
                <label for="vendor_id">Vendor</label>
                <select name="vendor_id" class="form-control" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id', $order->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="ref_no">Reference No</label>
                <input type="text" name="ref_no" class="form-control" value="{{ old('ref_no', $order->ref_no ?? '') }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="attn">ATTN</label>
                <input type="text" name="attn" class="form-control" value="{{ old('attn', $order->attn ?? '') }}">
            </div>

            <div class="form-group col-md-3">
                <label for="ship_to">Ship To</label>
                <input type="text" name="ship_to" class="form-control" value="{{ old('ship_to', $order->ship_to ?? '') }}">
            </div>

            <div class="form-group col-md-3">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $order->address ?? '') }}">
            </div>

            <div class="form-group col-md-3">
                <label for="contact">Contact</label>
                <input type="text" name="contact" class="form-control" value="{{ old('contact', $order->contact ?? '') }}">
            </div>
        </div>

        <!-- Items Section -->
        <hr>
        <h5>Order Items</h5>
        <table class="table" id="items-table">
    <thead>
        <tr>
            <th>Description</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Discount</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->items as $index => $item)
            @include('orders.partials.item_row', ['index' => $index, 'item' => $item])
        @endforeach
    </tbody>
</table>
<button type="button" class="btn btn-secondary" id="add-item">Add Item</button>


        <button type="button" class="btn btn-sm btn-primary" id="add-item">+ Add Item</button>

        <!-- Submit -->
        <hr>
        <button type="submit" class="btn btn-success">Save Order</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</div>
