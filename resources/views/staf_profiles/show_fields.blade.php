<!-- General Information Section -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h4 class="mb-0 section-header">
            <i class="fas fa-user-circle"></i>General Information:
			
        </h4>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Profile Picture -->
            <div class="col-md-2 col-sm-6">
                <div class="text-center mb-3">
                    @php
                        $profilePicture = null;
                        foreach ($model->getMedia() as $item) {
                            if ($item->name === 'profile_picture') {
                                $profilePicture = $item;
                                break;
                            }
                        }
                    @endphp
                    
                    @if($profilePicture && in_array($profilePicture->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']))
                        <img src="{{ $profilePicture->getFullUrl() }}" 
                             alt="Profile Picture" 
                             class="img-fluid rounded-circle" 
                             style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dc3545;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-4x text-white"></i>
                        </div>
                    @endif
				    <label for="exclude_from_expiry">Exclude from Expiry Alerts</label><br>
					<input type="checkbox" id="exclude_from_expiry" name="exclude_from_expiry" disabled {{ $profile->exclude_from_expiry ? 'checked' : '' }}>
                </div>
            </div>

            <!-- Name and Basic Info -->
            <div class="col-md-5 col-sm-6">
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.name')</span>
                    <span class="info-value">{{ $profile->name }}</span>
                </div>
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.last_name')</span>
                    <span class="info-value">{{ $profile->last_name }}</span>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-md-5 col-sm-6">
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.mobile_no')</span>
                    <span class="info-value">{{ $profile->mobile_no }}</span>
                </div>
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.home_mobile_no')</span>
                    <span class="info-value">{{ $profile->home_mobile_no }}</span>
                </div>
				
				
            </div>
        </div>

        <div class="row">
			<div class="col-md-2 col-sm-6">
                <div class="text-center mb-3">

                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.staf_type')</span>
                    <span class="info-value">{{ $profile->staf_type }}</span>
                </div>
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.nationality')</span>
                    <span class="info-value">{{ $profile->nationality }}</span>
                </div>
				
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.gender')</span>
                    <span class="info-value">{{ $profile->gender }}</span>
                </div>
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.joining_date')</span>
                    <span class="info-value">{{ $profile->joining_date }}</span>
                </div>
				
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.total_leaves')</span>
                    <span class="info-value">{{ config('enum.total_staff_leaves') }}</span>
                </div>
				<div class="info-box">
                    <span class="info-label">@lang('models/stafprofile.fields.leaves_available')</span>
                    <span class="info-value">{{ $profile->remainingLeaveDays() }}</span>
                </div>
				
				
            </div>
        </div>

        <div class="row">
			<div class="col-md-2 col-sm-6">
                <div class="text-center mb-3">
					

                </div>
            </div>
			<div class="col-md-2 col-sm-6">
                <div class="text-center mb-3">

                </div>
            </div>
            <div class="col-md-2 col-sm-6">

            </div>
            <div class="col-md-2 col-sm-6">
				

            </div>
        </div>
    </div>
</div>
<div class="row" style=" display: flex; justify-content: space-around;">

	<!-- Expiry Dates Section -->
	<div class="card shadow-sm mb-4" style="width: 48%;">
		<div class="card-header bg-white">
			<h4 class="mb-0 section-header">
				<i class="fas fa-calendar-times"></i>Expiry Dates:
			</h4>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-4 col-sm-6">
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.passport_expiry')</span>
						<span class="info-value">{{ $profile->passport_expiry }}</span>
					</div>
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.visa_expiry')</span>
						<span class="info-value">{{ $profile->visa_expiry }}</span>
					</div>
				</div>

				<div class="col-md-4 col-sm-6">
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.emirates_id_expiry')</span>
						<span class="info-value">{{ $profile->emirates_id_expiry }}</span>
					</div>
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.labor_card_expiry')</span>
						<span class="info-value">{{ $profile->labor_card_expiry }}</span>
					</div>
				</div>

				@if($profile->staf_type == 'Driver')
				<div class="col-md-4 col-sm-6">
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.driver_permit_expiry')</span>
						<span class="info-value">{{ $profile->driver_permit_expiry }}</span>
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>

	<!-- More Info Section -->
	<div class="card shadow-sm mb-4" style="width: 48%;">
		<div class="card-header bg-white">
			<h4 class="mb-0 section-header">
				<i class="fas fa-info-circle"></i>More Info:
			</h4>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-4 col-sm-6">
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.last_vacation_start')</span>
						<span class="info-value">{{ $profile->last_vacation_start }}</span>
					</div>
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.last_increment')</span>
						<span class="info-value">{{ $profile->last_increment }}</span>
					</div>
				</div>

				<div class="col-md-4 col-sm-6">
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.last_vacation_days')</span>
						<span class="info-value">{{ $profile->last_vacation_days }}</span>
					</div>
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.last_increment_amount')</span>
						<span class="info-value">{{ $profile->last_increment_amount }}</span>
					</div>
				</div>

				<div class="col-md-4 col-sm-6">
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.joining_date_after_vacation')</span>
						<span class="info-value">{{ $profile->last_vacation_end }}</span>
					</div>
					<div class="info-box">
						<span class="info-label">@lang('models/stafprofile.fields.joining_date')</span>
						<span class="info-value">{{ $profile->joining_date }}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Tickets Section -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h4 class="mb-0 section-header">
            <i class="fas fa-ticket-alt"></i>Tickets:
        </h4>
    </div>
    <div class="card-body">
        @if($profile->tickets->isEmpty())
            <div class="p-3 text-center">
                <p class="text-muted">No tickets available for this staff member.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Ticket Date</th>
                            <th>Agent Name</th>
                            <th>Travel Type</th>
                            <th>Travel Date</th>
                            <th>Return Date</th>
                            <th>Amount</th>
                            <th>Total Value</th>
                            <th>Payment Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profile->tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_date }}</td>
                                <td>{{ $ticket->agent_name }}</td>
                                <td>{{ $ticket->travel_type }}</td>
                                <td>{{ $ticket->travel_date }}</td>
                                <td>{{ $ticket->return_date ?: 'N/A' }}</td>
                                <td>{{ number_format($ticket->amount, 2) }}</td>
                                <td>{{ number_format($ticket->total_value, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $ticket->payment_status === 'Paid' ? 'success' : ($ticket->payment_status === 'Pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($ticket->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Absences Section -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h4 class="mb-0 section-header">
            <i class="fas fa-user-check"></i>Approved Absences:
        </h4>
    </div>
    <div class="card-body">
        @php
            $approvedAbsences = $profile->approvedAbsences()->get();
        @endphp

        @if($approvedAbsences->isEmpty())
            <div class="p-3 text-center">
                <p class="text-muted">No approved absences recorded. </p>
            </div>
        @else
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-label">Total Absences</span>
                                <span class="info-value text-danger" style="font-size: 24px;">{{ $approvedAbsences->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-label">This Month</span>
                                @php
                                    $thisMonth = $approvedAbsences->filter(function($item) {
                                        return $item->attendance_date->format('Y-m') === now()->format('Y-m');
                                    })->count();
                                @endphp
                                <span class="info-value text-warning" style="font-size: 24px;">{{ $thisMonth }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-label">This Year</span>
                                @php
                                    $thisYear = $approvedAbsences->filter(function($item) {
                                        return $item->attendance_date->format('Y') === now()->format('Y');
                                    })->count();
                                @endphp
                                <span class="info-value text-info" style="font-size: 24px;">{{ $thisYear }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-label">Last Absence</span>
                                @php
                                    $lastAbsence = $approvedAbsences->first();
                                @endphp
                                <span class="info-value text-secondary" style="font-size: 14px;">
                                    {{ $lastAbsence ? $lastAbsence->attendance_date->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Foreman</th>
                            <th>Status</th>
                            <th>Approved On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedAbsences as $key => $absence)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $absence->attendance_date->format('M d, Y') }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $absence->attendance_date->format('l') }}</small>
                                </td>
                                <td>
                                    {{ $absence->foreman->name ??  'Unknown' }}
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Approved
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $absence->updated_at->format('M d, Y H:i') }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
    .info-box {
        padding: 12px;
        border-radius:  8px;
        text-align:  center;
        margin-bottom: 8px;
    }

    .info-label {
        display: block;
        font-size: 12px;
        color: #999;
        margin-bottom: 5px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .info-value {
        display: block;
        font-weight: 600;
    }

    .section-header {
        font-weight: 600;
        color:  #333;
    }

    .badge-success {
        background-color:  #28a745;
    }

    table tbody tr:hover {
        background-color: #f8f9fa;
    }

    thead. thead-light {
        background-color: #f8f9fa;
    }
</style>

<!-- Letters Section -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h4 class="mb-0 section-header">
            <i class="fas fa-envelope"></i>Letters:
        </h4>
    </div>
    <div class="card-body">
        <div class="row">
            <x-card-letters :profile="$profile" type="appreciation" />
            <x-card-letters :profile="$profile" type="warning" />
        </div>
    </div>
</div>
        
        <!-- Documents Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 text-danger"><i class="fas fa-file-upload mr-2"></i>Documents Uploaded</h4>
                    </div>
                    <div class="card-body">
                        @include('components.model_files', ['model' => $profile])
                    </div>
                </div>
            </div>
        </div>