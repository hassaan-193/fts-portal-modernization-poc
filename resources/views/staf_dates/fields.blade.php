<!-- Staff Member Selection -->
<div class="form-group col-md-6">
    {!! Form::label('staff_id', 'Staff Member *') !!}
    <div class="input-group">
        {!! Form::select('staff_id', 
            $staffMembers->pluck('name', 'id'), 
            isset($stafDate) ? $stafDate->staff_id : null, 
            ['class' => $errors->has('staff_id') ? 'form-control is-invalid' : 'form-control', 'id' => 'staff_id', 'placeholder' => 'Select Staff Member']
        ) !!}
    </div>
    @if ($errors->has('staff_id'))
        <span class="invalid-feedback d-block">
            {{ $errors->first('staff_id') }}
        </span>
    @endif
</div>

<!-- Leave Start Date -->
<div class="form-group col-md-6">
    {!! Form::label('start_date', 'Leave Start Date *') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="far fa-calendar-alt"></i>
            </span>
        </div>
        {!! Form::text('start_date', 
            isset($stafDate) ? $stafDate->start_date : old('start_date'), 
            ['class' => $errors->has('start_date') ? 'form-control is-invalid datePickerField' : 'form-control datePickerField', 'placeholder' => 'YYYY-MM-DD']
        ) !!}
    </div>
    @if ($errors->has('start_date'))
        <span class="invalid-feedback d-block">
            {{ $errors->first('start_date') }}
        </span>
    @endif
</div>

<!-- Leave End Date -->
<div class="form-group col-md-6">
    {!! Form::label('end_date', 'Joining Date After Leave') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="far fa-calendar-alt"></i>
            </span>
        </div>
        {!! Form::text('end_date', 
            isset($stafDate) ? $stafDate->end_date : old('end_date'), 
            ['class' => $errors->has('end_date') ? 'form-control is-invalid datePickerField' : 'form-control datePickerField', 'placeholder' => 'YYYY-MM-DD']
        ) !!}
    </div>
    @if ($errors->has('end_date'))
        <span class="invalid-feedback d-block">
            {{ $errors->first('end_date') }}
        </span>
    @endif
</div>

<!-- Leave Days (Read-only, auto-calculated) -->
<div class="form-group col-md-6">
    {!! Form::label('days', 'Total Leave Days') !!}
    {!! Form::number('days', 
        isset($stafDate) ? $stafDate->days : old('days'), 
        ['class' => 'form-control', 'readonly' => true, 'id' => 'days']
    ) !!}
    <small class="form-text text-muted">Auto-calculated from start and end dates</small>
</div>

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
$(document).ready(function() {
    var picker = $('.datePickerField').daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,
        timePicker: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.datePickerField').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        calculateDays();
    });

    $('.datePickerField').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Calculate days when end date changes
    function calculateDays() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate && endDate) {
            var start = moment(startDate, 'YYYY-MM-DD');
            var end = moment(endDate, 'YYYY-MM-DD');
            var days = end.diff(start, 'days') + 1;
            $('#days').val(Math.max(0, days));
        }
    }

    // Bind to end_date change
    $('#end_date').on('change', calculateDays);
});
</script>
@endsection
