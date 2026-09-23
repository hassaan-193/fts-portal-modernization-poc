@extends('layouts.master')

@section('content')
<style>
    body {
        margin: 0;
        background-color: #f9f9f9;
    }
    .form-container {
        width: 95%;
        margin: 0 auto;
        background-color: white;
        padding: 25px; 
        border: 1px solid #ccc;
        border-radius: 8px;
    }
    .section-header{
        /* background-color: #fdf2e3; */
        font-weight: 500;
        font-size: 15px;
        
    }
    
    /* Tab Styles */
    .tabs-container {
        margin: 20px 0;
    }
    .tabs-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        border-bottom: 1px solid #d81b60;
        padding-bottom:1%;
        margin-bottom: 20px;
    }
    .tab-button {
        padding: 8px ;
        background-color: #f5f5f5;
        border: none;
        border-radius: 5px 5px 0 0;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        color: #333;
    }
    .tab-button:hover {
        background-color: #fdf2e3;
    }
    .tab-button.active {
        background-color: #d81b60;
        color: white;
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    input[type=checkbox] {
        margin: 4px 0 0;
        line-height: normal;
        width: 15px;
        height: 15px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
        font-size: 13px;
    }
    .row-red {
        background-color: #d81b60;
        color: white; 
    }
    label{
        font-weight: 500;
        font-size: 13px;
    }
    .text-left{
        text-align: left;
    }
    .form-control, input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea, select {
        font-size: 13px;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .system-table th {
        background-color: #d81b60;
        color: white;
        font-weight: 500;
    }
    .na-checkbox {
        margin-left: 10px;
    }
    .locked-cell {
        pointer-events: none;
        background-color: #e9ecef !important;
        opacity: 0.7;
    }
    .urgent-action-cell {
        min-width: 120px;
    }
    .btn {
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        margin-top: 10px;
                background-color: #d81b60;
        color: white;
        border: none;
    }
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
    }
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
    }
    .btn-success {
        background-color: #28a745;
        color: white;
        border: none;
    }
    /* Tablet & Mobile Responsiveness */
    @media (max-width: 1024px) {
        .form-container { width: 100%; padding: 15px; }
        .tabs-nav { gap: 3px; }
        .tab-button { padding: 6px; font-size: 11px; }
        table { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        th, td { min-width: 80px; padding: 5px; font-size: 12px; }
        th:first-child, td:first-child { min-width: 120px; text-align: left; }
        input[type="number"] { width: 55px !important; }
        input[type="file"] { font-size: 10px !important; }
    }
    @media (max-width: 768px) {
        .col-md-3, .col-md-4, .col-md-6 { margin-bottom: 10px; }
        #clientSignatureCanvas { width: 100%; max-width: 300px; touch-action: none; }
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">@lang('crud.add_new') AMC Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('projects.index') !!}">AMC Report</a></li>
                    <li class="breadcrumb-item active">@lang('crud.add_new')</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    @include('flash::message')
    @if(!empty($draftCount))
        <div class="alert alert-info">
            You have {{ $draftCount }} saved {{ Str::plural('draft', $draftCount) }} not yet submitted.
            <a href="{{ route('projects.drafts') }}" class="alert-link">Continue from My AMC Drafts</a>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
        <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">@lang('crud.add_new') AMC Report</h3>
                </div>
                <div class="card-body">
        <form action="{{ route('projects.submitReport') }}" method="POST" enctype="multipart/form-data">
            @csrf
                        
            <div class="row">
                <!-- Reference Number -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('reference_number', 'Reference Number:') !!}
                        {!! Form::text('reference_number', old('reference_number'), [
                            'class' => 'form-control',
                            'id' => 'reference_number',
                            'readonly' => true,
                            'placeholder' => 'Auto-generated'
                        ]) !!}
                    </div>
                </div>

                <!-- Date -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('date', 'Date:') !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            {!! Form::text('date', old('date'), [
                                'class' => $errors->has('date') ? 'form-control is-invalid' : 'form-control',
                                'id' => 'date'
                            ]) !!}
                            @if ($errors->has('date'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Visiting Time -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('inspector_visiting_time', 'Visiting Time:') !!}
                        {!! Form::time('inspector_visiting_time', old('inspector_visiting_time'), [
                            'class' => 'form-control',
                            'id' => 'inspector_visiting_time'
                        ]) !!}
                        <small class="text-muted">(Optional)</small>
                    </div>
                </div>

                <!-- Leaving Time -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('inspector_leaving_time', 'Leaving Time:') !!}
                        {!! Form::time('inspector_leaving_time', old('inspector_leaving_time'), [
                            'class' => 'form-control',
                            'id' => 'inspector_leaving_time'
                        ]) !!}
                        <small class="text-muted">(Optional)</small>
                    </div>
                </div>
            </div>

            @section('scripts')
            @parent
            <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
            <script>
                $('#date').daterangepicker({
                    singleDatePicker: true,
                    locale: { format: 'YYYY-MM-DD' }
                });
            </script>
            @endsection
            <h6>Project Details</h6>

            <!-- AMC Type Selection -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label style="font-weight: 500;font-size:13px;">AMC Type:</label>
                        <div>
                            <label style="margin-right: 20px;">
                                <input type="radio" name="amc_type" value="existing" {{ old('amc_type', 'existing') === 'existing' ? 'checked' : '' }}> 
                                Existing AMC
                            </label>
                            <label>
                                <input type="radio" name="amc_type" value="new" {{ old('amc_type') === 'new' ? 'checked' : '' }}> 
                                New AMC
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Client -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('company_id', 'Client:') !!}
                        {!! Form::select('company_id', 
                            isset($companies) ? $companies->pluck('name','id') : [],
                            old('company_id'), 
                            [
                                'class' => $errors->has('company_id') ? 'form-control select2 is-invalid' : 'form-control select2',
                                'placeholder' => 'Select Company',
                                'id' => 'company_id'
                            ]
                        ) !!}
                        @if ($errors->has('company_id'))
                            <span class="invalid-feedback"><strong>{{ $errors->first('company_id') }}</strong></span>
                        @endif
                    </div>
                </div>

                <!-- Manual Client (New Inquiry, only relevant for New AMC) -->
                <div class="col-md-3 col-sm-6" id="manual_client_wrapper" style="display:none;">
                    <div class="form-group">
                        <label style="font-weight: 500; font-size: 13px;">
                            <input type="checkbox" id="is_manual_client" name="is_manual_client" value="1" style="width:auto;height:auto;margin-right:6px;vertical-align:middle;">
                            Client not listed (New Inquiry)
                        </label>
                        {!! Form::text('manual_client_name', old('manual_client_name'), [
                            'class' => $errors->has('manual_client_name') ? 'form-control is-invalid' : 'form-control',
                            'id' => 'manual_client_name',
                            'placeholder' => 'Enter client name',
                            'style' => 'display:none;margin-top:4px;'
                        ]) !!}
                        @if ($errors->has('manual_client_name'))
                            <span class="invalid-feedback"><strong>{{ $errors->first('manual_client_name') }}</strong></span>
                        @endif
                    </div>
                </div>

                <!-- Project -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('project_id', 'Project:') !!}
                        {!! Form::select('project_id',
                            isset($projects) ? $projects->pluck('name','id') : [],
                            old('project_id'),
                            ['class' => 'form-control select2', 'placeholder' => 'Select Project', 'id' => 'project_id']
                        ) !!}
                    </div>
                </div>

                <!-- Visit Schedule -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('visit_schedule_id', 'Visit Schedule:') !!}
                        {!! Form::select('visit_schedule_id',
                            isset($visitSchedules) ? $visitSchedules->pluck('name','id') : [],
                            old('visit_schedule_id'),
                            ['class' => 'form-control select2', 'placeholder' => 'Select Schedule', 'id' => 'visit_schedule_id']
                        ) !!}
                    </div>
                </div>

                <!-- Emergency Visit (only relevant for Existing AMC) -->
                <div class="col-md-3 col-sm-6" id="emergency_visit_wrapper">
                    <div class="form-group">
                        <label style="font-weight: 500; font-size: 13px;">
                            <input type="checkbox" id="is_emergency_visit" name="is_emergency_visit" value="1" style="width:auto;height:auto;margin-right:6px;vertical-align:middle;">
                            Emergency Visit (no scheduled visit)
                        </label>
                        {!! Form::date('emergency_visit_date', old('emergency_visit_date'), [
                            'class' => $errors->has('emergency_visit_date') ? 'form-control is-invalid' : 'form-control',
                            'id' => 'emergency_visit_date',
                            'style' => 'display:none;margin-top:4px;'
                        ]) !!}
                        @if ($errors->has('emergency_visit_date'))
                            <span class="invalid-feedback"><strong>{{ $errors->first('emergency_visit_date') }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Location -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('site_location', 'Location:') !!}
                        {!! Form::text('site_location', old('site_location'), ['class' => 'form-control', 'id' => 'site_location']) !!}
                    </div>
                </div>

                <!-- Site Name -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('site_name', 'Site Name:') !!}
                        {!! Form::text('site_name', old('site_name'), ['class' => 'form-control', 'id' => 'site_name']) !!}
                    </div>
                </div>
            </div>




<div class="row">
    <div class="col-md-12">
        <h5 class="mb-3">Block Information</h5>
        <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Add blocks and fill their inspection details in the tabbed sections below</p>

        <!-- Block Tabs Navigation -->
        <div id="block-tabs-nav" class="tabs-nav" style="border-bottom: 2px solid #d81b60; padding-bottom: 10px; margin-bottom: 20px;">
            <!-- Block tabs will be dynamically added here -->
        </div>

        <!-- Block Tabs Content -->
        <div id="block-tabs-content">
            <!-- Block content will be dynamically added here -->
        </div>

        <!-- Add Block Button -->
        <button type="button" id="add-block" class="btn btn-danger btn-sm mt-3">
            <i class="fa fa-plus"></i> Add Block
        </button>
    </div>
</div>

<script>
/* Old blocks data for restoring after validation failure */
const _oldBlocksData = @json(old('blocks', []));
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let blockCount = 0;
    const blockTabsNav = document.getElementById('block-tabs-nav');
    const blockTabsContent = document.getElementById('block-tabs-content');
    const addBlockBtn = document.getElementById('add-block');

    // System items definitions
    const systemItems = {
        fa: { label: 'Fire Alarm System', items: ['Control Panels', 'Smoke Detectors', 'Heat Detectors', 'Manual Call Points', 'Sounders / Flashers', 'FACP Batteries & Charger', 'Sounder Quality'], type: 'complex' },
        ff: { label: 'Fire Fighting Equipment', items: ['Fire Extinguishers', 'Hose Reels', 'Landing Valves', 'Sprinklers', 'Valves', 'Paint', 'Water Flow Switches', 'Tamper Switches'], type: 'complex' },
        fm200: { label: 'FM-200 System', items: ['Cylinders', 'Nozzles', 'Control Panels'], type: 'complex' },
        foam: { label: 'Foam Bladder Tank', items: ['Foam Tanks', 'Proportions', 'Piping/Valves', 'Paint'], type: 'complex' },
        voice: { label: 'Voice Evacuation', items: ['Speakers', 'Message Controllers', 'Panel'], type: 'complex' },
        emergency: { label: 'Emergency Lighting', items: ['Emergency Lights (30 mins check on battery)', 'Exit Lights', 'Panel (If app)'], type: 'complex' },
        si: { label: 'System Interfacing', items: ['Lift Interface', 'Gas Detection Panels', 'Ventilation / FAHU', 'Doors / Magnetic Locks', 'Other FACP Interface', 'Repeaters/ Others'], type: 'simple' },
        exit: { label: 'Exit Route & Storage', items: ['Exit Routes Clear', 'Emergency Exit Doors Functional', 'Signage Visible and Illuminated', 'Storage Near Equipment'], type: 'simple' },
        storage: { label: 'Storage Conditions', items: ['Equipment Rooms', 'Fire Pump Room', 'Electrical Rooms', 'Egress Paths'], type: 'simple' },
        pump: { label: 'Pump', items: ['Jockey Pump – Auto Run', 'Electric Pump – Auto Run', 'Diesel Pump – Auto Run', 'Pipeline Leakages (PumpRoom / Hose Reels / Hydrants)', 'Fire Pump Controllers', 'Sensor Lines', 'Diesel Level in Tank', 'Oil Level / Air & Oil Filters', 'Coolant Level', 'Rust on Pumps / Seal Check', 'Support Brackets & Joints', 'Pressure Gauges', 'Batteries', 'Charger', 'Pump Base, Alignment, Paint', 'Pump Room Cleanliness', 'Exhaust System & Ventilation', 'Leakage', 'Interfaced with FACP', 'Alarm Indications, Pump Run, Power failure, Low fuel, Phase Failure, Battery Charger', 'Model Number & Photos'], type: 'complex' },
        deluge: { label: 'Deluge Valve', items: ['Deluge Valves (Body & Trim)', 'Detection Devices (e.g., heat/smoke sensors)', 'Actuation Mechanism (manual/electric/pneumatic)', 'Control Valves (isolation/inlet/outlet)', 'Pressure Gauges', 'Gasket & Accessories', 'Strainers / Filters', 'Spray Nozzles / Open Sprinkler Heads', 'Drain Valves', 'Main Drain Flow (Tested)', 'Alarms / Supervisory Devices', 'System Reset Function (Post-Test)', 'Electrical Control Panel (if applicable)'], type: 'complex' }
    };

    /* Items previously added by users via "Add New Row" are persisted as defaults for every
     * future form (per system). Merge them in now so every new block includes them automatically. */
    const _extraSystemItems = @json($extraSystemItems ?? []);
    Object.keys(_extraSystemItems || {}).forEach(prefix => {
        if (!systemItems[prefix]) return;
        const existingLower = systemItems[prefix].items.map(i => i.toLowerCase());
        (_extraSystemItems[prefix] || []).forEach(label => {
            if (!existingLower.includes(String(label).toLowerCase())) {
                systemItems[prefix].items.push(label);
                existingLower.push(String(label).toLowerCase());
            }
        });
    });

    /* HTML-escape a value for use in an attribute or text node */
    function esc(v) {
        return String(v === null || v === undefined ? '' : v)
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    /* Used Items / Required Items are per system-tab (task 6): every fixed system and every
     * custom tab gets its own pair, named "{prefix}_used_items" / "{prefix}_required_items". */
    function usedRequiredItemsHTML(blockNum, prefix, usedVal, requiredVal) {
        usedVal = usedVal || '';
        requiredVal = requiredVal || '';
        return `
            <div style="margin-top: 15px;">
                <div class="section-header">Used Items</div>
                <textarea name="blocks[${blockNum}][${prefix}_used_items]" class="form-control" rows="3" placeholder="Used items for this section...">${esc(usedVal)}</textarea>
            </div>
            <div style="margin-top: 10px;">
                <div class="section-header">Required Items</div>
                <textarea name="blocks[${blockNum}][${prefix}_required_items]" class="form-control" rows="3" placeholder="Required items for this section...">${esc(requiredVal)}</textarea>
            </div>
        `;
    }

    /* Per-block state for dynamically added custom tabs (task 5) */
    const blockCustomTabState = {};
    function getCustomTabState(blockId) {
        if (!blockCustomTabState[blockId]) blockCustomTabState[blockId] = { count: 0, metaIndex: 0 };
        return blockCustomTabState[blockId];
    }

    function createBlockTab() {
        blockCount++;
        const blockId = `block-${blockCount}`;
        
        // Create tab button
        const tabButton = document.createElement('button');
        tabButton.type = 'button';
        tabButton.className = `tab-button ${blockCount === 1 ? 'active' : ''}`;
        tabButton.textContent = `Block ${blockCount}`;
        tabButton.setAttribute('data-block-tab', blockId);
        
        // Create delete button for tab
        const deleteBtn = document.createElement('span');
        deleteBtn.style.marginLeft = '10px';
        deleteBtn.style.cursor = 'pointer';
        deleteBtn.style.color = '#d81b60';
        deleteBtn.innerHTML = '✕';
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            deleteBlock(blockId, tabButton);
        });
        
        tabButton.appendChild(deleteBtn);
        blockTabsNav.appendChild(tabButton);

        // Add click handler for tab switching
        tabButton.addEventListener('click', function(e) {
            if(e.target.tagName !== 'SPAN') {
                switchBlockTab(blockId);
            }
        });

        // Create tab content with block name and system tables
        const tabContent = document.createElement('div');
        tabContent.className = `tab-content ${blockCount === 1 ? 'active' : ''}`;
        tabContent.id = blockId;
        
        // Block name section
        let blockContentHTML = `
            <div style="margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border-radius: 4px;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 500;">Block Name:</label>
                            <input type="text" name="blocks[${blockCount}][name]" class="form-control block-name-input" placeholder="e.g. Block A" data-block-id="${blockId}" required>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="blocks[${blockCount}][id]" value="${blockId}">
            <div id="${blockId}-custom-tabs-meta"></div>
        `;

        // System tables for this block
        blockContentHTML += `<div class="block-system-tabs" style="margin-top: 20px;">`;
        blockContentHTML += `<div class="tabs-nav" style="border-bottom: 1px solid #999;">`;

        // Create system tab buttons
        Object.keys(systemItems).forEach((prefix, idx) => {
            const isActive = idx === 0 ? 'active' : '';
            blockContentHTML += `<button type="button" class="tab-button ${isActive}" data-block-system-tab="${blockId}-system-${prefix}">${systemItems[prefix].label}</button>`;
        });

        blockContentHTML += `<button type="button" class="tab-button add-custom-tab-btn" data-block-id="${blockId}" style="background-color:#28a745;color:#fff;" title="Add a custom inspection section">+ Add Tab</button>`;
        blockContentHTML += `</div>`;

        // Create system tab contents
        Object.keys(systemItems).forEach((prefix) => {
            const systemData = systemItems[prefix];
            const isActive = prefix === 'fa' ? 'active' : '';
            blockContentHTML += `<div class="tab-content ${isActive}" id="${blockId}-system-${prefix}">`;
            blockContentHTML += `<div class="section-header">${systemData.label}</div>`;
            blockContentHTML += `<table class="system-table block-system-table" data-block-id="${blockId}" data-prefix="${prefix}">`;
            blockContentHTML += `<thead><tr>`;
            blockContentHTML += `<th>Item</th><th>Total Qty</th><th>Qty Damaged/Defective</th><th class="urgent-action-cell">Urgent Action Required</th><th>Remarks</th><th>Attachment</th><th>Action</th>`;
            blockContentHTML += `</tr></thead><tbody>`;

            systemData.items.forEach(item => {
                const slug = item.toLowerCase().replace(/\s+/g, '_').replace(/[^\w_]/g, '');
                const defectiveCol = (systemData.type === 'complex') ? `<td><input type="number" name="blocks[${blockCount}][${prefix}_${slug}_defective]" style="width: 60px;"></td>` :
                    `<td><label><input type="radio" name="blocks[${blockCount}][${prefix}_${slug}_status]" value="working"> Working</label><label><input type="radio" name="blocks[${blockCount}][${prefix}_${slug}_status]" value="not_working" checked> Not Working</label></td>`;

                blockContentHTML += `
                    <tr>
                        <td class="text-left">${item}</td>
                        <td class="locked-cell"><input type="number" name="blocks[${blockCount}][${prefix}_${slug}_total]" readonly style="width: 60px;"></td>
                        ${defectiveCol}
                        <td class="locked-cell"><label><input type="radio" name="blocks[${blockCount}][${prefix}_${slug}_urgent]" value="yes"> Yes</label><label><input type="radio" name="blocks[${blockCount}][${prefix}_${slug}_urgent]" value="no" checked> No</label></td>
                        <td><input type="text" name="blocks[${blockCount}][${prefix}_${slug}_remarks]" style="width: 100%;"></td>
                        <td><input type="file" name="blocks[${blockCount}][${prefix}_${slug}_attachment][]" accept="image/*,.pdf" multiple style="font-size: 11px;"></td>
                        <td></td>
                    </tr>
                `;
            });

            blockContentHTML += `</tbody></table>`;
            blockContentHTML += `<button type="button" class="btn add-block-row-btn btn-sm" data-block-id="${blockId}" data-prefix="${prefix}" style="margin-top: 10px;">Add New Row</button>`;
            blockContentHTML += usedRequiredItemsHTML(blockCount, prefix);
            blockContentHTML += `</div>`;
        });

        blockContentHTML += `</div>`;

        tabContent.innerHTML = blockContentHTML;
        blockTabsContent.appendChild(tabContent);

        // Return block ID for later use
        return blockId;
    }

    function switchBlockTab(blockId) {
        // Remove active class from all block buttons (tabs-nav only)
        document.querySelectorAll('#block-tabs-nav .tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Remove active class from all block contents (block tabs only, not system tabs)
        document.querySelectorAll('#block-tabs-content > .tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Add active class to selected block
        const tabButton = document.querySelector(`[data-block-tab="${blockId}"]`);
        if(tabButton) tabButton.classList.add('active');
        
        const blockContent = document.getElementById(blockId);
        if(blockContent) blockContent.classList.add('active');
    }

    function deleteBlock(blockId, tabButton) {
        // Remove the tab button and content
        tabButton.remove();
        document.getElementById(blockId).remove();

        // If deleted tab was active, activate another
        if (blockTabsNav.children.length > 0) {
            const firstTab = blockTabsNav.children[0];
            const firstTabId = firstTab.getAttribute('data-block-tab');
            switchBlockTab(firstTabId);
        } else {
            blockCount = 0; // Reset count
        }
    }

    // Add block button handler
    addBlockBtn.addEventListener('click', function() {
        const newBlockId = createBlockTab();
        // Automatically switch to the new block
        setTimeout(() => {
            switchBlockTab(newBlockId);
        }, 100);
    });

    // Create first block by default
    createBlockTab();

    // Restore old block data after validation failure
    if (_oldBlocksData && Object.keys(_oldBlocksData).length > 0) {
        // First block was already created above; populate it with old data for block index 1
        // Additional blocks will be created here
        let oldKeys = Object.keys(_oldBlocksData);
        oldKeys.forEach((blockNum, idx) => {
            const blockData = _oldBlocksData[blockNum];
            const targetBlockNum = idx + 1; // blockCount after createBlockTab() calls
            // Fill block name
            const nameInput = document.querySelector(`[name="blocks[${targetBlockNum}][name]"]`);
            if (nameInput && blockData.name) nameInput.value = blockData.name;
            // Fill system fields
            Object.keys(blockData).forEach(key => {
                if (key === 'name' || key === 'id') return;
                const input = document.querySelector(`[name="blocks[${targetBlockNum}][${key}]"]`);
                if (input) {
                    if (input.type === 'radio' || input.type === 'checkbox') return; // handled below
                    input.value = blockData[key];
                } else {
                    // Try radio buttons
                    const radios = document.querySelectorAll(`[name="blocks[${targetBlockNum}][${key}]"]`);
                    radios.forEach(r => { if (r.value === blockData[key]) r.checked = true; });
                }
            });
            // Create extra blocks if needed (beyond first)
            if (idx > 0) {
                const newId = createBlockTab();
                // After creating, re-fill (blockCount was incremented)
            }
        });
    }

    // Block system tabs switching
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tab-button') && e.target.hasAttribute('data-block-system-tab')) {
            const tabId = e.target.getAttribute('data-block-system-tab');
            const blockId = tabId.split('-system-')[0]; // Get block-N from "block-N-system-fa"
            const blockDiv = document.getElementById(blockId);
            
            if(blockDiv) {
                // Only deactivate system tabs within this specific block
                blockDiv.querySelectorAll('.block-system-tabs .tab-button').forEach(btn => btn.classList.remove('active'));
                blockDiv.querySelectorAll('.block-system-tabs .tab-content').forEach(content => content.classList.remove('active'));
                
                // Activate clicked system tab
                e.target.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            }
        }
    });

    // Add new row to block system tables
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-block-row-btn')) {
            const blockId = e.target.getAttribute('data-block-id');
            const prefix = e.target.getAttribute('data-prefix');
            const blockNum = blockId.split('-')[1];
            const table = e.target.previousElementSibling;
            const tbody = table.querySelector('tbody');
            const rowIndex = tbody.children.length;
            const isComplex = prefix.startsWith('custom') ? true : (systemItems[prefix] && systemItems[prefix].type === 'complex');
            const base = `blocks[${blockNum}][${prefix}_newitem${rowIndex}`;

            const defectiveCol = isComplex
                ? `<td><input type="number" name="${base}_defective]" style="width: 60px;"></td>`
                : `<td><label><input type="radio" name="${base}_status]" value="working"> Working</label><label><input type="radio" name="${base}_status]" value="not_working" checked> Not Working</label></td>`;

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="${base}_name]" placeholder="New Item" style="width: 100%;"></td>
                <td class="locked-cell"><input type="number" name="${base}_total]" readonly style="width: 60px;"></td>
                ${defectiveCol}
                <td class="locked-cell"><label><input type="radio" name="${base}_urgent]" value="yes"> Yes</label><label><input type="radio" name="${base}_urgent]" value="no" checked> No</label></td>
                <td><input type="text" name="${base}_remarks]" placeholder="Remarks" style="width: 100%;"></td>
                <td><input type="file" name="${base}_attachment][]" accept="image/*,.pdf" multiple style="font-size: 11px;"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }
    });

    // Add a new custom tab (section) to a block, with an empty table the user populates manually
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-custom-tab-btn')) {
            const blockId = e.target.getAttribute('data-block-id');
            const blockNum = blockId.split('-')[1];
            const label = prompt('Name this new section (tab):');
            if (!label || !label.trim()) return;
            const trimmedLabel = label.trim();

            const state = getCustomTabState(blockId);
            state.count++;
            const customKey = `custom${state.count}`;

            const tabBtn = document.createElement('button');
            tabBtn.type = 'button';
            tabBtn.className = 'tab-button';
            tabBtn.textContent = trimmedLabel;
            tabBtn.setAttribute('data-block-system-tab', `${blockId}-system-${customKey}`);
            e.target.parentNode.insertBefore(tabBtn, e.target);

            const blockDiv = document.getElementById(blockId);
            const systemTabsDiv = blockDiv.querySelector('.block-system-tabs');
            const tabContent = document.createElement('div');
            tabContent.className = 'tab-content';
            tabContent.id = `${blockId}-system-${customKey}`;
            tabContent.innerHTML = `
                <div class="section-header">${esc(trimmedLabel)}</div>
                <table class="system-table block-system-table" data-block-id="${blockId}" data-prefix="${customKey}">
                    <thead><tr>
                        <th>Item</th><th>Total Qty</th><th>Qty Damaged/Defective</th>
                        <th class="urgent-action-cell">Urgent Action Required</th><th>Remarks</th><th>Attachment</th><th>Action</th>
                    </tr></thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn add-block-row-btn btn-sm" data-block-id="${blockId}" data-prefix="${customKey}" style="margin-top: 10px;">Add New Row</button>
                ${usedRequiredItemsHTML(blockNum, customKey)}
            `;
            systemTabsDiv.appendChild(tabContent);

            const metaContainer = document.getElementById(`${blockId}-custom-tabs-meta`);
            const idx = state.metaIndex++;
            metaContainer.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="blocks[${blockNum}][custom_tabs][${idx}][key]" value="${customKey}">
                <input type="hidden" name="blocks[${blockNum}][custom_tabs][${idx}][label]" value="${esc(trimmedLabel)}">
            `);

            // Switch to the newly created tab
            blockDiv.querySelectorAll('.block-system-tabs .tab-button').forEach(b => b.classList.remove('active'));
            blockDiv.querySelectorAll('.block-system-tabs .tab-content').forEach(c => c.classList.remove('active'));
            tabBtn.classList.add('active');
            tabContent.classList.add('active');
        }
    });
});
</script>




            <!-- ==================== Urgent Rectification Summary ==================== -->
            <!-- <div class="section-header">
                 Urgent Rectification Summary
            </div>
            <table class="system-table" id="urgent-summary-table">
                <thead>
                    <tr>
                        <th>Issue</th>
                        <th>Location</th>
                        <th>Recommended Action</th>
                        <th>Deadline</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="urgent_issue[]" placeholder="Enter issue" style="width: 100%;"></td>
                        <td><input type="text" name="urgent_location[]" placeholder="Enter location" style="width: 100%;"></td>
                        <td><input type="text" name="urgent_action[]" placeholder="Recommended action" style="width: 100%;"></td>
                        <td><input type="date" name="urgent_deadline[]" style="width: 100%;"></td>
                        <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn add-urgent-row-btn" data-target-table="urgent-summary-table">Add New Row</button> -->



            {{-- Used Items / Required Items are now captured per-block (see Block Information tabs above) --}}

            <!-- ==================== Notes Section ==================== -->
            <div class="section-header">Notes</div>
            <div class="notes-container" style="margin-bottom: 1rem;">
                <textarea name="notes_used_items" rows="4" placeholder="Enter any notes here..." style="width: 100%; resize: vertical;">{{ old('notes_used_items') }}</textarea>
            </div>

            {{-- ==================== Photos Section (disabled for now) ====================
            <div class="section-header">📸 Photos (attachment option available against every option above)</div>
            <ul style="margin-bottom: 0.5rem;">
                <li>Attach photos for any defects or non-compliance</li>
                <li>Label each photo with reference to item/equipment</li>
            </ul>
            <table class="system-table" id="photos-table">
                <thead>
                    <tr>
                        <th>Reference (Item/Equipment)</th>
                        <th>Photo Attachments</th>
                        <th>Remarks / Label</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="photo_reference[]" placeholder="e.g., Fire Pump Panel" style="width: 100%;"></td>
                        <td><input type="file" name="photo_files[0][]" accept="image/*" multiple></td>
                        <td><input type="text" name="photo_label[]" placeholder="Label or remarks" style="width: 100%;"></td>
                        <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn add-photo-row-btn" data-target-table="photos-table">Add New Photo Row</button>
            --}}
            <hr>
            <h6>Client Details</h6>

            <div class="row">
                <!-- Client EID Details -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('client_eid_details', 'Client EID Details:') !!}
                        {!! Form::text('client_eid_details', old('client_eid_details'), [
                            'class' => $errors->has('client_eid_details') ? 'form-control is-invalid' : 'form-control',
                            'placeholder' => 'Enter client EID details'
                        ]) !!}
                        @if ($errors->has('client_eid_details'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('client_eid_details') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Client Phone -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('client_phone', 'Client Phone Number:') !!}
                        {!! Form::text('client_phone', old('client_phone'), [
                            'class' => $errors->has('client_phone') ? 'form-control is-invalid' : 'form-control',
                            'placeholder' => 'Enter client phone number'
                        ]) !!}
                        @if ($errors->has('client_phone'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('client_phone') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Attachment -->
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('scope_attachment', 'Attachment:') !!}
                        {!! Form::file('scope_attachment[]', ['class' => 'form-control', 'multiple' => true]) !!}
                    </div>
                </div>
            </div>
                        <!-- ==================== Next Due Date Section ====================
            <div class="section-header">Next Due Date</div>
            <div class="next-due-container" style="margin-bottom: 1rem;">
                <div style="display: flex; flex-direction: row; gap: 10px;">
                    <label>
                        • Next Inspection Due:
                        <input type="date" name="next_inspection_due" value="{{ old('next_inspection_due') }}" class="form-control" style="width: auto; margin-left: 5px;">
                    </label>
                    <label>
                        • Expiry Update Required On:
                        <input type="date" name="expiry_update_required_on" value="{{ old('expiry_update_required_on') }}" class="form-control" style="width: auto; margin-left: 5px;">
                    </label>
                </div>
            </div> -->

            <div class="scope-inputs" style="display: grid; gap: 0.5rem; max-width: 300px;">
                <div class="col-md-5">
                    <p style="font-weight: 500;font-size:13px;margin-bottom: 2px;">Signature</p>
                    <canvas id="clientSignatureCanvas" width="300" height="100" style="border: 1px solid #ccc;border-radius: 4px;touch-action: none;max-width:100%;"></canvas>
                    <div style="margin-top: 2px;">
                        <button type="button" class="btn btn-sm btn-secondary" id="clearClientCanvas" style="padding: 2px 8px;font-size:13px;border: none;">Clear</button>
                        <button type="button" class="btn btn-sm btn-success" id="saveClientCanvas" style="padding: 2px 8px;font-size:13px;border: none">Save</button>
                        <input type="hidden" id="clientSignatureInput" name="client_signature">
                    </div>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button type="submit" name="save_draft" value="1" class="btn btn-sm btn-secondary mt-3" style="text-align:center;font-size:13px;border: none;padding:4px 14px 6px;">Save as Draft</button>
                    <button type="submit" class="btn btn-sm btn-primary mt-3" style="text-align:center;font-size:13px;border: none;padding:4px 14px 6px;" onclick="return confirm('Submit this report for approval? It can no longer be edited as a draft.')">Submit Report</button>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <small>Save signature before report submit</small>
                </div>
            </div>
        </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    let newRowIndices = {};

    // --- Mapping Table IDs to Section Prefixes ---
    const sectionPrefixes = {
        'fire-alarm-table': 'fa_',
        'fire-fighting-table': 'ff_',
        'fm200-table': 'fm200_',
        'foam-table': 'foam_',
        'voice-table': 'voice_',
        'emergency-table': 'emergency_',
        'si-table': 'si_',
        'exit-table': 'exit_',
        'storage-table': 'storage_',
        'pump-table': 'pump_',
        'deluge-table': 'deluge_'
    };

    // --- Add Row (complex tables) ---
    document.querySelectorAll('.add-new-row-btn').forEach(button => {
        const tableId = button.getAttribute('data-target-table');
        if (!newRowIndices[tableId]) newRowIndices[tableId] = 0;

        button.addEventListener('click', function() {
            const tbody = document.getElementById(tableId).querySelector('tbody');
            const rowIndex = newRowIndices[tableId]++;
            const prefix = sectionPrefixes[tableId] || '';
            const slug = `custom_item_${rowIndex}`;

            let defectiveColumn = `
                <td><input type="number" name="${prefix}${slug}_defective" style="width: 60px;"></td>
            `;

            if (tableId === 'pump-table' || tableId === 'deluge-table') {
                defectiveColumn = `
                    <td>
                        <label><input type="radio" name="${prefix}${slug}_defective" value="yes"> Yes</label>
                        <label><input type="radio" name="${prefix}${slug}_defective" value="no" checked> No</label>
                    </td>
                `;
            }

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="${prefix}${slug}_name" placeholder="New Item" style="width: 100%;"></td>
                <td><input type="number" name="${prefix}${slug}_total" style="width: 60px;"></td>
                ${defectiveColumn}
                <td>
                    <label><input type="radio" name="${prefix}${slug}_urgent" value="yes"> Yes</label>
                    <label><input type="radio" name="${prefix}${slug}_urgent" value="no" checked> No</label>
                </td>
                <td><input type="text" name="${prefix}${slug}_remarks" placeholder="Remarks" style="width: 100%;"></td>
                <td><input type="file" name="${prefix}${slug}_attachment[]" accept="image/*,.pdf" multiple style="font-size: 11px;"></td>
                <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        });
    });

    // --- Add Row (simple yes/no tables) ---
    document.querySelectorAll('.add-simple-row-btn').forEach(button => {
        const tableId = button.getAttribute('data-target-table');
        if (!newRowIndices[tableId]) newRowIndices[tableId] = 0;

        button.addEventListener('click', function() {
            const tbody = document.getElementById(tableId).querySelector('tbody');
            const rowIndex = newRowIndices[tableId]++;
            const prefix = sectionPrefixes[tableId] || '';
            const slug = `custom_item_${rowIndex}`;

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="${prefix}${slug}_name" placeholder="New Item" style="width: 100%;"></td>
                <td>
                    <label><input type="radio" name="${prefix}${slug}_status" value="working"> Working</label>
                    <label><input type="radio" name="${prefix}${slug}_status" value="not_working" checked> Not Working</label>
                </td>
                <td>
                    <label><input type="radio" name="${prefix}${slug}_urgent" value="yes"> Yes</label>
                    <label><input type="radio" name="${prefix}${slug}_urgent" value="no" checked> No</label>
                </td>
                <td><input type="text" name="${prefix}${slug}_remarks" placeholder="Remarks" style="width: 100%;"></td>
                <td><input type="file" name="${prefix}${slug}_attachment[]" accept="image/*,.pdf" multiple style="font-size: 11px;"></td>
                <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        });
    });

    // --- Urgent Summary ---
    document.querySelector('.add-urgent-row-btn')?.addEventListener('click', function() {
        const tbody = document.getElementById('urgent-summary-table').querySelector('tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="urgent_issue[]" placeholder="Enter issue" style="width: 100%;"></td>
            <td><input type="text" name="urgent_location[]" placeholder="Enter location" style="width: 100%;"></td>
            <td><input type="text" name="urgent_action[]" placeholder="Recommended action" style="width: 100%;"></td>
            <td><input type="date" name="urgent_deadline[]" style="width: 100%;"></td>
            <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
        `;
        tbody.appendChild(newRow);
    });

    // --- Photos Table ---
    document.querySelector('.add-photo-row-btn')?.addEventListener('click', function() {
        const tbody = document.getElementById('photos-table').querySelector('tbody');
        const rowIndex = tbody.children.length;
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="photo_reference[]" placeholder="e.g., Fire Pump Panel" style="width: 100%;"></td>
            <td><input type="file" name="photo_files[${rowIndex}][]" accept="image/*" multiple></td>
            <td><input type="text" name="photo_label[]" placeholder="Label or remarks" style="width: 100%;"></td>
            <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
        `;
        tbody.appendChild(newRow);
    });

    // --- Remove Row ---
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row-btn')) {
            e.target.closest('tr').remove();
        }
    });

    // --- Signature Canvas ---
    const canvas = document.getElementById('clientSignatureCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let drawing = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            if (e.touches) {
                return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
            }
            return { x: e.clientX - rect.left, y: e.clientY - rect.top };
        }

        function startDraw(e) {
            e.preventDefault();
            drawing = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function drawLine(e) {
            e.preventDefault();
            if (!drawing) return;
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function stopDraw() { drawing = false; ctx.beginPath(); }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', drawLine);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', drawLine, { passive: false });
        canvas.addEventListener('touchend', stopDraw, { passive: false });

        document.getElementById('clearClientCanvas')?.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('clientSignatureInput').value = '';
        });

        document.getElementById('saveClientCanvas')?.addEventListener('click', () => {
            const dataURL = canvas.toDataURL();
            document.getElementById('clientSignatureInput').value = dataURL;
            alert('Signature saved successfully!');
        });
    }

// --- Dropdown dependencies with AMC type awareness ---
    $(document).ready(function () {
        const amcTypeRadios = document.querySelectorAll('input[name="amc_type"]');
        const companySelect = $('#company_id');
        const projectSelect = $('#project_id');
        const visitSelect = $('#visit_schedule_id');

        const manualClientWrapper = $('#manual_client_wrapper');
        const isManualClientCheckbox = document.getElementById('is_manual_client');
        const manualClientNameInput = document.getElementById('manual_client_name');
        const emergencyVisitWrapper = $('#emergency_visit_wrapper');
        const isEmergencyVisitCheckbox = document.getElementById('is_emergency_visit');
        const emergencyVisitDateInput = document.getElementById('emergency_visit_date');

        function resetManualClient() {
            isManualClientCheckbox.checked = false;
            manualClientNameInput.style.display = 'none';
            manualClientNameInput.removeAttribute('required');
            manualClientNameInput.value = '';
            companySelect.prop('disabled', false).closest('.form-group').closest('.col-md-3').show();
        }

        function resetEmergencyVisit() {
            isEmergencyVisitCheckbox.checked = false;
            emergencyVisitDateInput.style.display = 'none';
            emergencyVisitDateInput.removeAttribute('required');
            emergencyVisitDateInput.value = '';
            visitSelect.prop('disabled', false);
        }

        isManualClientCheckbox.addEventListener('change', function () {
            if (this.checked) {
                companySelect.val('').trigger('change.select2');
                companySelect.prop('disabled', true).closest('.form-group').closest('.col-md-3').hide();
                manualClientNameInput.style.display = 'block';
                manualClientNameInput.setAttribute('required', 'required');
            } else {
                companySelect.prop('disabled', false).closest('.form-group').closest('.col-md-3').show();
                manualClientNameInput.style.display = 'none';
                manualClientNameInput.removeAttribute('required');
                manualClientNameInput.value = '';
            }
        });

        isEmergencyVisitCheckbox.addEventListener('change', function () {
            if (this.checked) {
                visitSelect.val('').trigger('change.select2');
                visitSelect.prop('disabled', true);
                emergencyVisitDateInput.style.display = 'block';
                emergencyVisitDateInput.setAttribute('required', 'required');
            } else {
                visitSelect.prop('disabled', false);
                emergencyVisitDateInput.style.display = 'none';
                emergencyVisitDateInput.removeAttribute('required');
                emergencyVisitDateInput.value = '';
            }
        });

        // Function to generate reference number
        function generateReferenceNumber() {
            fetch('/projects/get-reference-number')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('reference_number').value = data.reference_number;
                })
                .catch(console.error);
        }

        // Generate reference number on page load
        generateReferenceNumber();

        // Handle AMC Type Change
        amcTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const amcType = this.value;
                
                if (amcType === 'new') {
                    // For new AMC, hide project and visit schedule
                    projectSelect.closest('.form-group').closest('.col-md-3').hide();
                    visitSelect.closest('.form-group').closest('.col-md-3').hide();
                    projectSelect.prop('disabled', true);
                    visitSelect.prop('disabled', true);
                    projectSelect.val('').trigger('change.select2');
                    visitSelect.val('').trigger('change.select2');

                    manualClientWrapper.show();
                    emergencyVisitWrapper.hide();
                    resetEmergencyVisit();
                } else {
                    // For existing AMC, show project and visit schedule
                    projectSelect.closest('.form-group').closest('.col-md-3').show();
                    visitSelect.closest('.form-group').closest('.col-md-3').show();
                    projectSelect.prop('disabled', false);
                    visitSelect.prop('disabled', false);

                    manualClientWrapper.hide();
                    emergencyVisitWrapper.show();
                    resetManualClient();
                }

                // Reload clients based on AMC type
                const companyId = companySelect.val();
                loadClients(amcType, companyId);
            });
        });

        // Function to load clients based on AMC type
        function loadClients(amcType, selectedCompanyId = null) {
            fetch(`/projects/get-clients?amc_type=${amcType}`)
                .then(res => res.json())
                .then(companies => {
                    companySelect.html('<option value="">Select Company</option>');
                    companies.forEach(c => {
                        const isSelected = selectedCompanyId && selectedCompanyId == c.id ? 'selected' : '';
                        companySelect.append(`<option value="${c.id}" ${isSelected}>${c.name}</option>`);
                    });
                    companySelect.trigger('change.select2');
                })
                .catch(console.error);
        }

        // Initialize AMC type and load clients
        const activeAmcType = document.querySelector('input[name="amc_type"]:checked').value;
        loadClients(activeAmcType);

        // Company → Projects
        companySelect.on('change', function () {
            const companyId = $(this).val();
            const amcType = document.querySelector('input[name="amc_type"]:checked').value;

            // Reset and disable dependent dropdowns
            projectSelect.html('<option value="">Select Project</option>').trigger('change');
            visitSelect.html('<option value="">Select Visit Schedule</option>').trigger('change');
            visitSelect.prop('disabled', true);

            if (companyId && amcType === 'existing') {
                fetch(`/projects/get-projects?company_id=${companyId}`)
                    .then(res => res.json())
                    .then(projects => {
                        projects.forEach(p => {
                            const newOption = new Option(p.subject, p.id, false, false);
                            projectSelect.append(newOption);
                        });
                        projectSelect.prop('disabled', false).trigger('change.select2');
                    })
                    .catch(console.error);
            } else {
                projectSelect.prop('disabled', amcType === 'new');
            }
        });

        // Project → Visit Schedules
        projectSelect.on('change', function () {
            const projectId = $(this).val();

            // Reset and disable visit dropdown
            visitSelect.html('<option value="">Select Visit Schedule</option>').trigger('change');
            visitSelect.prop('disabled', true);

            if (projectId) {
                fetch(`/projects/get-visit-schedules?project_id=${projectId}`)
                    .then(res => res.json())
                    .then(visits => {
                        visits.forEach(v => {
                            const newOption = new Option(
                                `Visit Date: ${v.visit_date} - Status: ${v.status}`,
                                v.id,
                                false,
                                false
                            );
                            visitSelect.append(newOption);
                        });
                        visitSelect.prop('disabled', false).trigger('change.select2');
                    })
                    .catch(console.error);
            }
        });

    });


    // --- Handle "N/A" checkbox disabling section ---
    const naCheckboxes = document.querySelectorAll('input[type="checkbox"][name$="_na"]');
    naCheckboxes.forEach(checkbox => {
        const sectionDiv = checkbox.closest('.section-header');
        const table = sectionDiv?.nextElementSibling;

        const toggleSection = () => {
            const inputs = table.querySelectorAll('input, select, textarea, button.add-new-row-btn, button.add-simple-row-btn');
            if (checkbox.checked) {
                inputs.forEach(input => {
                    input.disabled = true;
                    if (input.name) input.dataset.nameBackup = input.name;
                    input.removeAttribute('name');
                });
            } else {
                inputs.forEach(input => {
                    input.disabled = false;
                    if (input.dataset.nameBackup) input.name = input.dataset.nameBackup;
                });
            }
        };

        toggleSection();
        checkbox.addEventListener('change', toggleSection);
    });
});


</script>



@endsection


@section('scripts')
@parent

    <!-- ✅ Include Select2 JS (only once) -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- ✅ Initialize Select2 -->
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "Select an option",
            allowClear: true
        });
    });
    </script>

@endsection

