<div class="row">
    <div class="col-md-4 col-sm-6">
		<!-- Name Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.name')</b> <a class="float-right">{{ $vendor->name }}</a>
		</li>
		<!-- Contact No Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.contact_no')</b> <a class="float-right">{{ $vendor->contact_no }}</a>
		</li>
		<!-- Location Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.contact_person')Location</b> <a class="float-right">{{ $vendor->location }}</a>
        </li>
	</div>
    <div class="col-md-4 col-sm-6">
		<!-- Email Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.email')</b> <a class="float-right">{{ $vendor->email }}</a>
		</li>
		<!-- Additional Emails Field -->
		@php $additionalEmails = $vendor->additionalEmails(); @endphp
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>Additional Emails</b>
		    <a class="float-right">
		    	@forelse($additionalEmails as $additionalEmail)
		    		{{ $additionalEmail }}@if(!$loop->last)<br>@endif
		    	@empty
		    		-
		    	@endforelse
		    </a>
		</li>
		<!-- Contact No Two Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.contact_no_two')</b> <a class="float-right">{{ $vendor->contact_no_two }}</a>
		</li>
		<!-- credit_limit Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.credit_limit')</b> <a class="float-right">{{ $vendor->credit_limit }}</a>
        </li>
	</div>
    <div class="col-md-4 col-sm-6">
		<!-- Contact Person Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.contact_person')</b> <a class="float-right">{{ $vendor->contact_person }}</a>
		</li>
		<!-- Vat No Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.vat_no')</b> <a class="float-right">{{ $vendor->vat_no }}</a>
		</li>
		<!-- payment_terms Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.payment_terms')</b> <a class="float-right">{{ $vendor->payment_terms }}</a>
		</li>
	</div>
</div>

<div class="row mt-4">
	<div class="col-md-12">
		<h4 class="text-danger">@lang('models/vendors.fields.payment_preference')</h4>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-4 col-sm-6">
		<!-- Payment Preference Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.payment_preference')</b> <a class="float-right">
		    	@if($vendor->payment_preference === 'cod')
		    		Cash on Delivery
		    	@elseif($vendor->payment_preference === 'pdc')
		    		PDC (Post Dated Check)
		    	@else
		    		{{ $vendor->payment_preference }}
		    	@endif
		    </a>
		</li>
	</div>
	@if($vendor->payment_preference === 'pdc')
	<div class="col-md-4 col-sm-6">
		<!-- PDC Number of Days Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.pdc_number_of_days')</b> <a class="float-right">{{ $vendor->pdc_number_of_days }}</a>
		</li>
	</div>
	<div class="col-md-4 col-sm-6">
		<!-- PDC Payment Option Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.pdc_payment_option')</b> <a class="float-right">
		    	@if($vendor->pdc_payment_option === 'on_delivery_amount')
		    		On Delivery
		    	@elseif($vendor->pdc_payment_option === 'on_payment_release_amount')
		    		On PO Release
		    	@else
		    		{{ $vendor->pdc_payment_option }}
		    	@endif
		    </a>
		</li>
	</div>
	@endif
</div>

<div class="row mt-4">
	<div class="col-md-12">
		<h4 class="text-danger">Additional Information</h4>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-4 col-sm-6">
		<!-- Vendor Specialization Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/vendors.fields.vendor_specialization')</b> <a class="float-right">{{ $vendor->vendor_specialization ?? 'N/A' }}</a>
		</li>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<!-- Vendor Invoices -->
		<x-CardTable :title="__('models/payment_invoices.plural')" :project="$vendor->id" />
	</div>
	<div class="col-md-6">
		<!-- Vendor Payments -->
		<x-CardTable :title="'Vendor_Payments'" :project="$vendor->id" />
	</div>
</div>
<div class="row">

	<div class="col-md-6">
        <!-- Vendor LPOouts -->
        <x-CardTable :title="'Vendor_Lpoouts'" :project="$vendor->id" />
    </div>

</div>
