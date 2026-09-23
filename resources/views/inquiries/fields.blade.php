{{-- ─── Section 1: Client Details ─── --}}
<h5 class="border-bottom pb-2 mb-3 text-maroon">Client Details</h5>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('client_name', 'Client Name *') !!}
            {!! Form::text('client_name', null, ['class' => $errors->has('client_name') ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'Full client name']) !!}
            @error('client_name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('phone', 'Phone Number *') !!}
            {!! Form::text('phone', null, ['class' => $errors->has('phone') ? 'form-control is-invalid' : 'form-control', 'placeholder' => '+971...']) !!}
            @error('phone') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('email', 'Email') !!}
            {!! Form::email('email', null, ['class' => $errors->has('email') ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'optional']) !!}
            @error('email') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('location', 'Location') !!}
            {!! Form::text('location', null, ['class' => $errors->has('location') ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'City / Area']) !!}
            @error('location') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('project', 'Project') !!}
            {!! Form::text('project', null, ['class' => $errors->has('project') ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'Project name']) !!}
            @error('project') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
</div>

{{-- ─── Section 2: Inquiry Details ─── --}}
<h5 class="border-bottom pb-2 mb-3 mt-3 text-maroon">Inquiry Details</h5>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('inquiry_type', 'Inquiry Type *') !!}
            {!! Form::select('inquiry_type', array_combine(\App\Models\Inquiry::INQUIRY_TYPES, \App\Models\Inquiry::INQUIRY_TYPES), null, [
                'class' => $errors->has('inquiry_type') ? 'form-control is-invalid' : 'form-control',
                'id'    => 'inquiry_type',
                'placeholder' => '— Select Type —',
            ]) !!}
            @error('inquiry_type') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6" id="other_type_wrapper" style="display:none;">
        <div class="form-group">
            {!! Form::label('other_type', 'Other Type *') !!}
            {!! Form::text('other_type', null, ['class' => $errors->has('other_type') ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'Specify type']) !!}
            @error('other_type') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('source', 'Source of Inquiry *') !!}
            {!! Form::select('source', array_combine(\App\Models\Inquiry::SOURCES, \App\Models\Inquiry::SOURCES), null, [
                'class' => $errors->has('source') ? 'form-control is-invalid' : 'form-control',
                'placeholder' => '— Select Source —',
            ]) !!}
            @error('source') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('expected_price', 'Expected Price (AED)') !!}
            {!! Form::number('expected_price', null, ['class' => $errors->has('expected_price') ? 'form-control is-invalid' : 'form-control', 'min' => '0', 'step' => '0.01', 'placeholder' => 'optional']) !!}
            @error('expected_price') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
</div>

{{-- ─── Section 3: Tracking ─── --}}
<h5 class="border-bottom pb-2 mb-3 mt-3 text-maroon">Tracking</h5>
<div class="row">
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('priority', 'Priority *') !!}
            {!! Form::select('priority', array_combine(\App\Models\Inquiry::PRIORITIES, \App\Models\Inquiry::PRIORITIES), 'Medium', [
                'class' => $errors->has('priority') ? 'form-control is-invalid' : 'form-control',
            ]) !!}
            @error('priority') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <!-- <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('follow_up_date', 'Follow-up Date') !!}
            {!! Form::date('follow_up_date', null, ['class' => $errors->has('follow_up_date') ? 'form-control is-invalid' : 'form-control']) !!}
            @error('follow_up_date') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="form-group">
            {!! Form::label('expected_closing_date', 'Expected Closing Date') !!}
            {!! Form::date('expected_closing_date', null, ['class' => $errors->has('expected_closing_date') ? 'form-control is-invalid' : 'form-control']) !!}
            @error('expected_closing_date') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div> -->
</div>

{{-- ─── Section 4: Notes ─── --}}
<h5 class="border-bottom pb-2 mb-3 mt-3 text-maroon">Notes</h5>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('notes', 'Notes') !!}
            {!! Form::textarea('notes', null, ['class' => $errors->has('notes') ? 'form-control is-invalid' : 'form-control', 'rows' => 3, 'placeholder' => 'Additional notes...']) !!}
            @error('notes') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>
    </div>
</div>

{{-- ─── Section 5: Attachments ─── --}}
<h5 class="border-bottom pb-2 mb-3 mt-3 text-maroon">Attachments</h5>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('attachments[]', 'Files (multiple allowed)') !!}
            <input type="file" name="attachments[]" class="form-control-file" multiple accept="*/*">
            <small class="form-text text-muted">You can select multiple files at once.</small>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
    // Toggle "Other Type" field visibility
    function toggleOtherType() {
        var val = $('#inquiry_type').val();
        if (val === 'Other') {
            $('#other_type_wrapper').show();
        } else {
            $('#other_type_wrapper').hide();
            $('[name=other_type]').val('');
        }
    }

    $(document).ready(function () {
        toggleOtherType();
        $('#inquiry_type').on('change', toggleOtherType);
    });
</script>
@endsection
