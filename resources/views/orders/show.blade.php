@extends('layouts.master')

@section('content')

<style>
    body {
        margin: 0;
        background-color: #f9f9f9;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header img {
        width: 100px;
        height: 120px;
    }

    .header h1 {
        font-size: 20px;
        margin: 10px 0 0;
    }

    .form-container {
        width: 70%;
        margin: 0 auto;
        background-color: white;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    .issuer-details {
        background-color: #fdf2e3;
        text-align: left;
        padding-left: 10px;
    }

    .project-details {
        background-color: #fdf2e3;
        text-align: left;
        padding-left: 10px;
    }

    input[type=checkbox] {
        margin: 4px 0 0;
        line-height: normal;
        width: 15px;
        height: 15px;
    }

    label {
        font-size: smaller;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
    }

    .row-red {
        background-color: #E47770;
        color: white;
    }

    label {
        font-size: xx-small;
    }

    td {
        font-size: xx-small;
    }

    th {
        font-size: xx-small;
        font-family: Arial, Helvetica, sans-serif;
    }
    .terms-box {
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 15px;
        font-family: Arial, sans-serif;
        font-size: 14px; /* Smaller text */
        line-height: 1.5;
        text-align: left;
        margin: 10px 0;
    }
    
    .terms-box ul {
        padding-left: 20px;
        margin: 0;
    }
    
    .terms-box li {
        margin-bottom: 10px;
    }
</style>

<br>
<br>
<br>

<div class="main header">
    <div class="form-container">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1 style="text-align: left;">
                        FIRE TECHNICAL SERVICE
                    </h1>
                    <div class="row">
                        <div class="col-md-9">
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h1 style="text-align: right;">فاير للخدمات الفنية</h1>
                    <div class="row"
                        style="display: flex; justify-content: flex-start; align-items: flex-end; flex-direction: column;">
                        <div class="col-md-9">
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="row">ABU DHABI</div>
                    <div class="row">TEL: +971 2 491 8383</div>
                    <div class="row">FAX: +971 2 491 8380</div>
                    <div class="row">P.O.BOX: 30934</div>
                    <div class="row">RAS AL KHAIMAH</div>
                    <div class="row">TEL: +971 7 227 5747</div>
                    <div class="row">FAX: +971 7 227 5788</div>
                    <div class="row">P.O.BOX: 36625</div>
                </div>
                <div class="col-md-4">
                    <img src="{{ asset('dist/img/1592935047.png') }}" alt="Image Description">
                    <h6><u>Testing & Inspection Report</u></h6>
                    <br>
                </div>
                <div class="col-md-4"
                    style="display: flex; justify-content: flex-start; align-items: flex-end; flex-direction: column;">
                    <div class="row">أبو ظبي</div>
                    <div class="row">هاتف: +971 2 491 8383</div>
                    <div class="row">فاكس: +971 2 491 8380</div>
                    <div class="row">صندوق بريد: 30934</div>
                    <div class="row">رأس الخيمة</div>
                    <div class="row">هاتف: +971 7 227 5747</div>
                    <div class="row">فاكس: +971 7 227 5788</div>
                    <div class="row">صندوق بريد: 36625</div>
                </div>
            </div>
        </div>
        <br>
        <div class="container">
            <div class="row" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="col-md-4">
                    <div class="row">
                        <div style="background-color:antiquewhite; display: flex !important; justify-content: center !important; border-radius: 5px 0px 0px 5px; padding-top: 12px;"
                            class="col-md-5">
                            <p style="font-size: small;">Date</p>
                        </div>
                        <div style="background-color:antiquewhite; display: flex; justify-content: center; align-items: center; border-radius: 0px 5px 5px 0px;"
                            class="col-md-7">
                            <input type="date" name="date" class="form-control"
                                value="{{ old('date', $order->date->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div style="background-color:antiquewhite; display: flex !important; justify-content: center !important; border-radius: 5px 0px 0px 5px; padding-top: 12px; "
                            class="col-md-5">
                            <p style="font-size: small;">TRN Number</p>
                        </div>
                        <div style="background-color:antiquewhite; display: flex; justify-content: center; align-items: center; border-radius: 0px 5px 5px 0px;"
                            class="col-md-7">
                            <input type="text" name="trn" class="form-control" value="{{ old('trn', $order->trn) }}"
                                required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div style="background-color:antiquewhite; display: flex !important; justify-content: center !important; border-radius: 5px 0px 0px 5px; padding-top: 12px;"
                            class="col-md-5">
                            <p style="font-size: small;">ATN</p>
                        </div>
                        <div style="background-color:antiquewhite; display: flex; justify-content: center; align-items: center; border-radius: 0px 5px 5px 0px;"
                            class="col-md-7">
                            <input type="text" name="attn" class="form-control" value="{{ old('attn', $order->attn) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="issuer-details p-1 mb-3">
            <h5>Order Details</h5>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <label for="inspector-visiting-time">Vendor</label>
                    <input type="text" id="inspector-visiting-time" name="inspector-visiting-time" style="width: 70%;"
                        value="{{ $order->vendor->name ?? 'N/A' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="inspector-leaving-time">Ref No:</label>
                    <input type="text" id="inspector-leaving-time" name="inspector-leaving-time" style="width: 70%;"
                        value="{{ old('ref_no', $order->ref_no) }}" readonly>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <label for="inspector-visiting-time">Ship To</label>
                    <input type="text" id="inspector-visiting-time" name="inspector-visiting-time" style="width: 70%;"
                        value="{{ old('ship_to', $order->ship_to) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inspector-visiting-time">Address</label>
                    <input type="text" id="inspector-visiting-time" name="inspector-visiting-time" style="width: 70%;"
                        value="{{ old('address', $order->address) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inspector-visiting-time">Contact</label>
                    <input type="text" id="inspector-visiting-time" name="inspector-visiting-time" style="width: 70%;"
                        value="{{ old('contact', $order->contact) }}" readonly>
                </div>
            </div>
        </div>
        <br>
        <div class="issuer-details p-1 mb-3">
            <h5>Terms and Conditions</h5>
        </div>
        <div class="container">
            <div class="row">
                  <div class="terms-box">
                    <ul>
                      <li>Invoice must accompany goods or be mailed prior to collection. The LPO number must appear on all invoices.</li>
                      <li>
                        <strong>Payment Terms:</strong>
                        <ul>
                          <li>10% Advance with Bank Guarantee</li>
                          <li>80% on Deliveries & Work Progress (Office & Shed Structure Works)</li>
                          <li>10% on Testing & Commissioning Works</li>
                        </ul>
                      </li>
                      <li>Goods (materials, products, or services) must be supplied as per the Purchase Order. FTS reserves the right to accept or reject any items not meeting the required quality or specification.</li>
                      <li>If the shipment is not ready on the specified or agreed date, FTS reserves the right to cancel the order without penalty or refuse late deliveries.</li>
                      <li>In case of rejection due to manufacturing errors, the supplier is fully responsible for replacements. FTS may withhold payment until the issue is resolved and the job is delivered.</li>
                      <li><strong>Delivery:</strong> As Agreed</li>
                      <li>A penalty of 2% per week will be imposed after the agreed delivery date.</li>
                    </ul>
                  </div>
                  
            </div>
        </div>

        <div class="issuer-details p-1 mb-3">
            <h5>Items</h5>
        </div>
        <br>
        <div class="container">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Order Items</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->item_description }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Subtotal</td>
                                <td>{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Discount</td>
                                <td>{{ number_format($order->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">VAT (5%)</td>
                                <td>{{ number_format($order->vat, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Total w/ VAT</td>
                                <td><strong>{{ number_format($order->total_with_vat, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                </div>
            </div>
        </div>
        <hr>

        <div class="container">
            <div class="row">
                <div class="col-md-4"><p>Managing Director</p></div>
                <div class="col-md-4"><p>Accounts</p></div>
                <div class="col-md-4"><p>Adminstration Office</p></div>
            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

    </div>
</div>

<br>
<br>
<br>







@endsection