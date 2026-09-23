@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Letter</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Letter</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')
    <div class="row">
        <div class="col-md-12">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Create New Letter</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Staff Profile Field - CHANGED TO MULTIPLE -->
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="staff_profile_ids">Staff Profiles: <small class="text-muted">(Select one or more)</small></label>
                                    <select name="staff_profile_ids[]" id="staff_profile_ids" class="form-control {{ $errors->has('staff_profile_ids') ? 'is-invalid' : '' }}" multiple>
                                        @foreach($stafProfiles as $profile)
                                            <option value="{{ $profile->id }}">{{ $profile->name }} {{ $profile->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('staff_profile_ids'))
                                        <span class="invalid-feedback d-block">
                                            <strong>{{ $errors->first('staff_profile_ids') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Type Field -->
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="type">Type:</label>
                                    <select name="type" id="type" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}">
                                        <option value="">-- Select Type --</option>
                                        <option value="warning">Warning</option>
                                        <option value="appreciation">Appreciation</option>
                                        <option value="general_notice">General Notice</option>
                                        <option value="poor_performance_notice">Poor Performance Notice</option>
                                        <option value="accommodation_notice">Accommodation Notice</option>
                                        <option value="vehicle_notice">Vehicle Notice</option>
                                        <option value="attendance_notice">Attendance Notice</option>
                                        <option value="weather_notice">Weather Notice</option>
                                        <option value="eid_holidays_notice">Eid Holidays Notice</option>
                                    </select>
                                    @if ($errors->has('type'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Days Deduct Field (Only for Warning Type) -->
                            <div class="col-md-3 col-sm-6" id="days_deduct_container" style="display: none;">
                                <div class="form-group">
                                    <label for="days_deduct">Days to Deduct:</label>
                                    <input type="number" name="days_deduct" id="days_deduct" class="form-control {{ $errors->has('days_deduct') ? 'is-invalid' : '' }}" min="1" max="365" placeholder="Enter number of days">
                                    @if ($errors->has('days_deduct'))
                                        <span class="invalid-feedback d-block">
                                            <strong>{{ $errors->first('days_deduct') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Title Field -->
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="title">Title:</label>
                                    <input type="text" name="title" id="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" required>
                                    @if ($errors->has('title'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Issued By Field -->
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="issued_by">Issued By:</label>
                                    <input type="text" name="issued_by" id="issued_by" class="form-control {{ $errors->has('issued_by') ? 'is-invalid' : '' }}" required>
                                    @if ($errors->has('issued_by'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('issued_by') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Issued At Field -->
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="issued_at">Issued At:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="issued_at" id="issued_at" class="form-control {{ $errors->has('issued_at') ? 'is-invalid' : '' }}" required>
                                        @if ($errors->has('issued_at'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('issued_at') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Reference Number -->
                            <div class="col-md-3 col-sm-6"  >
                                <div class="form-group">
                                    <label for="ref_no">Reference Number:</label>
                                    <input type="text"  name="ref_no" id="ref_no" class="form-control {{ $errors->has('ref_no') ? 'is-invalid' : '' }}">
                                    @if ($errors->has('ref_no'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('ref_no') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- File Upload Field -->
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="file">Attach Files:</label>
                                    <input type="file" name="file" id="file" class="form-control">
                                </div>
                            </div>

                            <!-- Content Field -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content">Content:</label>
                                    <textarea name="content" id="content" class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}" rows="4" required></textarea>
                                    @if ($errors->has('content'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('content') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Submit Field -->
                            <div class="form-group col-sm-12">
                                <button type="submit" class="btn btn-danger btn-flat btn-lg">Create Letter</button>
                                <a href="{{ route('letters.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script>
    $('#staff_profile_ids').select2({
        theme: 'bootstrap4',
        placeholder: "Select Staff Members",
        allowClear: true,
        closeOnSelect: false
    });

    // Handle conditional display of days_deduct field
    const typeSelect = document.getElementById('type');
    const daysDeductContainer = document.getElementById('days_deduct_container');
    const daysDeductInput = document.getElementById('days_deduct');

    // Function to toggle days_deduct field
    function toggleDaysDeductField() {
        if (typeSelect.value === 'warning') {
            daysDeductContainer.style.display = 'block';
        } else {
            daysDeductContainer.style.display = 'none';
            daysDeductInput.value = ''; // Clear the field when hiding
        }
    }

    // Listen for changes on type select
    typeSelect.addEventListener('change', toggleDaysDeductField);

    // Initialize on page load if warning is already selected
    toggleDaysDeductField();
</script>
@endsection