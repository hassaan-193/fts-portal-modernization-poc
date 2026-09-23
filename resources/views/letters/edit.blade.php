@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Edit Letter</h1>

    <!-- Form for updating the letter -->
    {!! Form::model($letter, ['route' => ['letters.update', $letter->id], 'method' => 'PUT']) !!}
        @csrf

        <div class="form-group">
            <label for="staff_profile_id">Staff Profile</label>
            
            <!-- Search input for filtering staff profiles -->
            <input type="text" id="staff_profile_search" class="form-control" placeholder="Search Staff Profile...">

            <!-- Dropdown with all staff profiles initially visible -->
            <select name="staff_profile_id" id="staff_profile_id" class="form-control">
                @foreach($stafProfiles as $profile)
                    <option value="{{ $profile->id }}" {{ $letter->staff_profile_id == $profile->id ? 'selected' : '' }}>
                        {{ $profile->name }} {{ $profile->last_name }} <!-- Display first and last name -->
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type" class="form-control">
                <option value="">-- Select Type --</option>
                <option value="warning" {{ $letter->type == 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="appreciation" {{ $letter->type == 'appreciation' ? 'selected' : '' }}>Appreciation</option>
                <option value="general_notice" {{ $letter->type == 'general_notice' ? 'selected' : '' }}>General Notice</option>
                <option value="poor_performance_notice" {{ $letter->type == 'poor_performance_notice' ? 'selected' : '' }}>Poor Performance Notice</option>
                <option value="accommodation_notice" {{ $letter->type == 'accommodation_notice' ? 'selected' : '' }}>Accommodation Notice</option>
                <option value="vehicle_notice" {{ $letter->type == 'vehicle_notice' ? 'selected' : '' }}>Vehicle Notice</option>
                <option value="attendance_notice" {{ $letter->type == 'attendance_notice' ? 'selected' : '' }}>Attendance Notice</option>
                <option value="weather_notice" {{ $letter->type == 'weather_notice' ? 'selected' : '' }}>Weather Notice</option>
                <option value="eid_holidays_notice" {{ $letter->type == 'eid_holidays_notice' ? 'selected' : '' }}>Eid Holidays Notice</option>
            </select>
        </div>

        <div class="form-group" id="days_deduct_container" style="display: {{ $letter->type == 'warning' ? 'block' : 'none' }};">
            <label for="days_deduct">Days to Deduct</label>
            <input type="number" name="days_deduct" id="days_deduct" class="form-control" min="1" max="365" value="{{ old('days_deduct', $letter->days_deduct) }}" placeholder="Enter number of days">
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $letter->title) }}" required>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" class="form-control" required>{{ old('content', $letter->content) }}</textarea>
        </div>

        <div class="form-group">
            <label for="issued_by">Issued By</label>
            <input type="text" name="issued_by" id="issued_by" class="form-control" value="{{ old('issued_by', $letter->issued_by) }}" required>
        </div>

        <div class="form-group">
            <label for="issued_at">Issued At</label>
            <input type="date" name="issued_at" id="issued_at" class="form-control" value="{{ old('issued_at', $letter->issued_at) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Letter</button>
    {!! Form::close() !!}
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // When the user types in the search input
    $('#staff_profile_search').on('keyup', function() {
        var searchQuery = $(this).val().toLowerCase();

        // Show the dropdown if it's hidden
        $('#staff_profile_id').show();

        // Filter the options based on the search query
        $('#staff_profile_id option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if (optionText.indexOf(searchQuery) !== -1) {
                $(this).show();  // Show matching option
            } else {
                $(this).hide();  // Hide non-matching option
            }
        });
    });

    // Reset dropdown if input is cleared or focus is lost
    $('#staff_profile_search').on('focusout', function() {
        if ($(this).val() === '') {
            // When focus is lost and search is empty, show all options
            $('#staff_profile_id option').show();
        }
    });

    // Make dropdown visible when input is focused
    $('#staff_profile_search').on('focus', function() {
        $('#staff_profile_id').show();
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
});
</script>

@endsection
