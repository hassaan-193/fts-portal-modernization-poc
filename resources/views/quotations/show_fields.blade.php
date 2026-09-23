<div class="row">
    <div class="col-md-4 col-sm-6">	<!-- Name Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.name')</b> <br><a class="text-left">{{ $quotation->name }}</a>
		</li>
		<!-- Amount Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.amount')</b> <br><a class="text-left">{{ $quotation->amount }}</a>
		</li>
		<!-- Subject Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.subject')</b> <br><a class="text-left">{{ $quotation->subject }}</a>
		</li>
	</div>
    <div class="col-md-4 col-sm-6">
		<!-- Company Id Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.company_id')</b> <br><a class="text-left text-primary" href="{{ route('companies.show',$quotation->company_id) }}">{{ $quotation->company->name }}</a>
		</li>
		<!-- Civil Defence fee Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.civil_defence_fee')</b> <br><a class="text-left">{{ $quotation->civil_defence_fee }}</a>
		</li>
		<!-- Location Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.location')</b> <br><a class="text-left">{{ $quotation->location }}</a>
		</li>
	</div>
    <div class="col-md-4 col-sm-6">
		<!-- Ref No Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.ref_no')</b> <br><a class="text-left">{{ $quotation->ref_no }}</a>
		</li>
		<!-- Date Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/quotations.fields.date')</b> <br><a class="text-left">{{ $quotation->date }}</a>
		</li>
	</div>

</div>

