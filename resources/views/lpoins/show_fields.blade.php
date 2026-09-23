<div class="row">
    <div class="col-md-4 col-sm-6">		<!-- Quotation Id Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/lpoins.fields.quotation_id')</b> <br><a class="text-left text-primary" href="{{ route('quotations.show',$lpoin->quotation_id) }}">{{ $lpoin->quotation->name }}</a>
		</li>
		<!-- Government Fee Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/lpoins.fields.government_fee')</b> <br><a class="text-left">{{ $lpoin->government_fee }}</a>
		</li>
		<!-- Date Issue Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/lpoins.fields.date_issue')</b> <br><a class="text-left">{{ $lpoin->date_issue }}</a>
		</li>
	</div>
    <div class="col-md-4 col-sm-6">
		<!-- Ref No Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/lpoins.fields.ref_no')</b> <br><a class="text-left">{{ $lpoin->ref_no }}</a>
		</li>
		<!-- Adjustment Fee Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/lpoins.fields.adjustment_fee')</b> <br><a class="text-left">{{ $lpoin->adjustment_fee }}</a>
		</li>
		<!-- Date Due Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/lpoins.fields.date_due')</b> <br><a class="text-left">{{ $lpoin->date_due }}</a>
		</li>
    </div>
    <div class="col-md-4 col-sm-6">
		<!-- Civil Defense Fee Field -->
		<li class="callout callout-danger list-group-item mb-3 shadow">
		    <b>@lang('models/lpoins.fields.civil_defence_fee')</b> <br><a class="text-left">{{ $lpoin->civil_defence_fee }}</a>
		</li>
		<!-- Payment Terms Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/lpoins.fields.payment_terms')</b> <br><a class="text-left">{{ $lpoin->payment_terms }}</a>
        </li>
	</div>
    <div class="col-md-4 col-sm-6">
		
	</div>
    <div class="col-md-4 col-sm-6">
		
	</div>
    <div class="col-md-4 col-sm-6">
        
    </div>
    <div class="col-md-4 col-sm-6">
		
    </div>
    <div class="col-md-4 col-sm-6">
		
	</div>
</div>


