<li class="list-group-item">
  <b>Project Name: </b> <a class="text-left">{{ $project->subject ?: '-' }}</a>
</li>
<li class="list-group-item" >
  <b>@lang('models/projects.fields.project_estimation'): </b> <a class="text-left">{{ $project->project_estimation }}</a>
</li>
<li class="list-group-item">
    <b>Quotation: </b> 
    @if($project->quotation && $project->quotation->id)
        <a class="text-left text-primary" href="{{ route('quotations.show', $project->quotation->id) }}">{{ $project->quotation->name }}</a>
    @else
        <a class="text-left">-</a>
    @endif
</li>
<li class="list-group-item">
    <b>Lpoin: </b> 
    @if($project->quotation && $project->quotation->lpoins && $project->quotation->lpoins->id)
        <a class="text-left text-primary" href="{{ route('lpoins.show', $project->quotation->lpoins->id) }}">{{ $project->quotation->lpoins->ref_no }}</a>
    @else
        <a class="text-left">-</a>
    @endif
</li>
<br>

<div class="row">
    @php
        // Only count an invoice's receipt once its transaction has actually cleared —
        // cash clears instantly, cheques stay pending until cleared via /cheques.
        $clearedInvoices = $project->invoices->filter(fn($i) => $i->transaction && $i->transaction->status == 1);

        $vendorPoTotal = $project->lpoouts->where('is_latest_revision', true)->sum('total_amount');

        $vendorPoPaid = 0;
        foreach ($project->lpoouts->where('is_latest_revision', true) as $lpoout) {
            foreach ($lpoout->paymentInvoices as $pi) {
                if ($pi->transaction && $pi->transaction->status == 1) {
                    $vendorPoPaid += $pi->total_amount;
                }
            }
        }
        $vendorPoToBePaid = $vendorPoTotal - $vendorPoPaid;

        $pettyCashTotal = $project->petty_cash->sum('total_amount');
        $totalExpense = $vendorPoPaid + $project->labour_charges + $pettyCashTotal;
        $profit = $clearedInvoices->sum('amount') - $totalExpense;

        $amountReceive = 0.0;
        $serviceCharges = 0.0;
        foreach ($clearedInvoices as $invoice) {
            $amountReceive += $invoice->invoice_product_details->sum('total_amount');
            $serviceCharges += $invoice->invoice_service_details->sum('amount');
        }
        $remaining = $project->quotation->amount - $clearedInvoices->sum('amount');
    @endphp
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-project-diagram"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Contract Value</span>
            <span class="info-box-number">{{ $project->quotation->amount }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Vat</span>
            <span class="info-box-number">{{ $project->quotation->vat }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-user-friends"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Estimation Labour Cost</span>
            <span class="info-box-number">{{ $project->labour_charges }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-file-invoice"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">@lang('models/projects.fields.material_charges')</span>
            <span class="info-box-number">{{ $project->material_charges }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-thumbs-up"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Service Charges</span>
            <span class="info-box-number">{{ $serviceCharges }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Received (Without VAT)</span>
            <span class="info-box-number">{{ $clearedInvoices->sum('amount') }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-compact-disc"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Vat Payable</span>
            <span class="info-box-number">{{ $clearedInvoices->sum('vat') }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-warning elevation-1"><i class="far fa-sticky-note"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Vendor PO (To Be Paid)</span>
            <span class="info-box-number">{{ $vendorPoToBePaid }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="far fa-check-circle"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Vendor PO (Already Paid)</span>
            <span class="info-box-number">{{ $vendorPoPaid }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-thumbs-up"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Total Expense</span>
            <span class="info-box-number">{{ $totalExpense }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon {{ $profit < 0 ? 'bg-danger' : 'bg-success' }} elevation-1"><i class="fas fa-chart-line"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Profit</span>
            <span class="info-box-number {{ $profit < 0 ? 'text-danger' : '' }}">{{ $profit }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Amount Receive</span>
            <span class="info-box-number">{{ $amountReceive }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-warning elevation-1"><i class="far fa-sticky-note"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Remaining</span>
            <span class="info-box-number">{{ $remaining }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-thumbs-up"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Petty Cash</span>
            <span class="info-box-number">{{ $pettyCashTotal }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>

    <!-- /.col -->
</div>

{{-- <!-- Date Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.date')</b> <a class="float-right">{{ $project->date }}</a>
</li>

<li class="list-group-item">
<b>@lang('models/projects.fields.quotation_id')</b> <a href="{{ route('quotations.show',$project->quotation_id) }}" class="float-right text-primary">{{ $project->quotation->name }}</a>
</li>

<!-- contract_value Field -->
<li class="list-group-item">
    <b>@lang('models/quotations.fields.contract_value')</b> <a class="float-right">{{ $project->quotation->contract_value }}</a>
</li>

<!-- project_type_id Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.project_type_id')</b> <a class="float-right">{{ $project->project_type->name }}</a>
</li>


<!-- subject Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.subject')</b> <a class="float-right">{{ $project->subject }}</a>
</li>

<!-- payment_terms Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.payment_terms')</b> <a class="float-right">{{ $project->payment_terms }}</a>
</li>

<!-- labour_charges Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.labour_charges')</b> <a class="float-right">{{ $project->labour_charges }}</a>
</li>

<!-- material_charges Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.material_charges')</b> <a class="float-right">{{ $project->material_charges }}</a>
</li>

<!-- project_source Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.project_source')</b> <a class="float-right">{{ $project->project_source }}</a>
</li>

<!-- note Field -->
<li class="list-group-item">
    <b>@lang('models/projects.fields.note')</b> <a class="float-right">{{ $project->note }}</a>
</li> --}}
