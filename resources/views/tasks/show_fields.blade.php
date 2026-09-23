<h4>General Information :</h4>
<br>
<div class="row">
    <div class="col-md-4 col-sm-6">	<!-- Name Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.name')</b> <br><a class="text-left">{{ $profile->name }}</a>
		</li>
		<!-- Amount Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.last_name')</b> <br><a class="text-left">{{ $profile->last_name }}</a>
		</li>
		<!-- Subject Field -->
	</div>
    <div class="col-md-4 col-sm-6">
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.staf_type')</b> <br><a class="text-left">{{ $profile->staf_type }}</a>
		</li>
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.nationality')</b> <br><a class="text-left">{{ $profile->nationality }}</a>
		</li>
		<!-- Company Id Field -->
	</div>
    <div class="col-md-4 col-sm-6">
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.gender')</b> <br><a class="text-left">{{ $profile->gender }}</a>
		</li>
		<!-- Civil Defence fee Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.joining_date')</b> <br><a class="text-left">{{ $profile->joining_date }}</a>
		</li>
		<!-- Location Field -->
	</div>
	<div class="col-md-4 col-sm-6">
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.dob')</b> <br><a class="text-left">{{ $profile->dob }}</a>
		</li>
		<!-- Location Field -->
	</div>
</div>
<h4>Expiry Dates :</h4>
<br>
<div class="row">
	<div class="col-md-4 col-sm-6">
		<!-- Ref No Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.passport_expiry')</b> <br><a class="text-left">{{ $profile->passport_expiry }}</a>
		</li>
		<!-- Date Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.visa_expiry')</b> <br><a class="text-left">{{ $profile->visa_expiry }}</a>
		</li>
	</div>
	<div class="col-md-4 col-sm-6">	
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.emirates_id_expiry')</b> <br><a class="text-left">{{ $profile->emirates_id_expiry }}</a>
		</li>
		<!-- Ref No Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.labor_card_expiry')</b> <br><a class="text-left">{{ $profile->labor_card_expiry }}</a>
		</li>
	</div>
	@if ($profile->staf_type=='Driver')
	<div class="col-md-4 col-sm-6">	
		<!-- Date Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.driver_permit_expiry')</b> <br><a class="text-left">{{$profile->driver_permit_expiry}} </a>
		</li>
	</div>
	@endif
</div>
<h4>More Info :</h4>
<br>
<div class="row">
	<div class="col-md-4 col-sm-6">
		<!-- Ref No Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.last_vacation_start')</b> <br><a class="text-left">{{ $profile->last_vacation_start}}</a>
		</li>
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.last_increment')</b> <br><a class="text-left"> {{  $profile->last_increment }}</a>
		</li>		
	</div>
	<div class="col-md-4 col-sm-6">	
		<!-- Date Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.last_vacation_days')</b> <br><a class="text-left">{{ $profile->last_vacation_days }}</a>
		</li>		
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.last_increment_amount')</b> <br><a class="text-left">{{ $profile->last_increment_amount }}</a>
		</li>
		<!-- Ref No Field -->
	</div>
	<div class="col-md-4 col-sm-6">	
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.joining_date_after_vacation')</b> <br><a class="text-left">{{  $profile->last_vacation_end }}</a>
		</li>
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/stafprofile.fields.joining_date')</b> <br><a class="text-left">{{ $profile->joining_date }}</a>
		</li>
	</div>
</div>
<h4>Uploaded Documents :</h4>

