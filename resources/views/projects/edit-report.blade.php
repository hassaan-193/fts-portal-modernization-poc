@extends('layouts.master')

@section('content')
<style>
    body { margin: 0; background-color: #f9f9f9; }
    .section-header { font-weight: 500; font-size: 15px; }
    .tabs-nav { display: flex; flex-wrap: wrap; gap: 5px; border-bottom: 1px solid #d81b60; padding-bottom: 1%; margin-bottom: 20px; }
    .tab-button { padding: 8px; background-color: #f5f5f5; border: none; border-radius: 5px 5px 0 0; cursor: pointer; font-size: 12px; font-weight: 500; transition: all 0.3s ease; color: #333; }
    .tab-button:hover { background-color: #fdf2e3; }
    .tab-button.active { background-color: #d81b60; color: white; }
    .tab-content { display: none; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    input[type=checkbox] { margin: 4px 0 0; width: 15px; height: 15px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; font-size: 13px; }
    .row-red { background-color: #d81b60; color: white; }
    label { font-weight: 500; font-size: 13px; }
    .text-left { text-align: left; }
    .form-control, input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea, select { font-size: 13px; padding: 4px 8px; border: 1px solid #ccc; border-radius: 4px; }
    .system-table th { background-color: #d81b60; color: white; font-weight: 500; }
    .btn { padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; margin-top: 10px; background-color: #d81b60; color: white; border: none; }
    .btn-primary { background-color: #007bff; color: white; border: none; }
    .btn-secondary { background-color: #6c757d; color: white; border: none; }
    .btn-success { background-color: #28a745; color: white; border: none; }
    .locked-cell { pointer-events: none; background-color: #e9ecef !important; opacity: 0.7; }
    @media (max-width: 1024px) {
        .tabs-nav { gap: 3px; }
        .tab-button { padding: 6px; font-size: 11px; }
        table { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        th, td { min-width: 80px; padding: 5px; font-size: 12px; }
        th:first-child, td:first-child { min-width: 120px; text-align: left; }
        input[type="number"] { width: 55px !important; }
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">{{ $report->isDraft() ? 'Continue AMC Report (Draft)' : 'Edit AMC Report' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    @if($report->isDraft())
                    <li class="breadcrumb-item"><a href="{{ route('projects.drafts') }}">My AMC Drafts</a></li>
                    @else
                    <li class="breadcrumb-item"><a href="{{ route('projects.reportStatus') }}">Reports</a></li>
                    @endif
                    <li class="breadcrumb-item active">@lang('crud.edit')</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-white card card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">{{ $report->isDraft() ? 'Continue AMC Report (Draft)' : 'Edit AMC Report' }}</h3>
                </div>
                <div class="card-body">
                    @include('flash::message')
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('projects.updateReport', $report->id) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Reference Number (read-only) -->
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Reference Number</label>
                                <input type="text" class="form-control" value="{{ $report->reference_number }}" disabled>
                            </div>

                            <!-- Date -->
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" name="date" id="date_edit"
                                        class="{{ $errors->has('date') ? 'form-control is-invalid' : 'form-control' }}"
                                        value="{{ old('date', $report->date) }}">
                                    @if($errors->has('date'))
                                        <span class="invalid-feedback"><strong>{{ $errors->first('date') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            <!-- Visiting Time -->
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Visiting Time</label>
                                <input type="time" name="inspector_visiting_time" class="form-control"
                                    value="{{ old('inspector_visiting_time', substr($report->inspector_visiting_time ?? '', 0, 5)) }}">
                                <small class="text-muted">(Optional)</small>
                            </div>

                            <!-- Leaving Time -->
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Leaving Time</label>
                                <input type="time" name="inspector_leaving_time" class="form-control"
                                    value="{{ old('inspector_leaving_time', substr($report->inspector_leaving_time ?? '', 0, 5)) }}">
                                <small class="text-muted">(Optional)</small>
                            </div>
                        </div>

                        <h6>Project Details</h6>
                        <div class="row">
                            <div class="form-group col-md-3 col-sm-6">
                                <label>AMC Type</label>
                                <input type="text" class="form-control" value="{{ ucfirst($report->amc_type ?? 'existing') }}" disabled>
                                <input type="hidden" name="amc_type" value="{{ $report->amc_type ?? 'existing' }}">
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Client</label>
                                <input type="text" class="form-control" value="{{ $report->company->name ?? $report->manual_client_name ?? 'N/A' }}" disabled>
                            </div>
                            @if(($report->amc_type ?? 'existing') === 'existing')
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Project</label>
                                <input type="text" class="form-control" value="{{ $report->project->subject ?? 'N/A' }}" disabled>
                            </div>
                            @if($report->is_emergency_visit)
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Emergency Visit Date</label>
                                <input type="date" name="emergency_visit_date" class="form-control"
                                    value="{{ old('emergency_visit_date', $report->emergency_visit_date) }}">
                                <small class="text-muted">This report was filed as an emergency visit (no scheduled visit).</small>
                            </div>
                            @else
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Visit Schedule</label>
                                <input type="text" class="form-control"
                                    value="{{ $report->visitSchedule ? \Carbon\Carbon::parse($report->visitSchedule->visit_date)->format('d M Y') : 'N/A' }}" disabled>
                            </div>
                            @endif
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Location</label>
                                <input type="text" name="site_location" class="form-control"
                                    value="{{ old('site_location', $report->site_location) }}">
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Site Name</label>
                                <input type="text" name="site_name" class="form-control"
                                    value="{{ old('site_name', $report->site_name) }}">
                            </div>
                        </div>

                        {{-- Block Information --}}
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">Block Information</h5>
                                <p style="font-size:12px;color:#666;margin-bottom:15px;">Edit block inspection details in the tabbed sections below</p>
                                <div id="block-tabs-nav" class="tabs-nav" style="border-bottom:2px solid #d81b60;padding-bottom:10px;margin-bottom:20px;"></div>
                                <div id="block-tabs-content"></div>
                                <button type="button" id="add-block" class="btn btn-danger btn-sm mt-3">
                                    <i class="fa fa-plus"></i> Add Block
                                </button>
                            </div>
                        </div>

<script>
const _existingBlockInfo = @json($report->block_info ?? []);
const _oldBlocksData = @json(old('blocks', []));
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let blockCount = 0;
    const blockTabsNav = document.getElementById('block-tabs-nav');
    const blockTabsContent = document.getElementById('block-tabs-content');
    const addBlockBtn = document.getElementById('add-block');

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

    /* Map JS prefix key → DB system name */
    const prefixToSystemName = {
        fa:'fire_alarm', ff:'fire_fighting', fm200:'fm200', foam:'foam_tank',
        voice:'voice_evacuation', emergency:'emergency_lighting', si:'system_interfacing',
        exit:'exit_route', storage:'storage_conditions', pump:'pump', deluge:'deluge'
    };

    /* HTML-escape a value for use in an attribute or text node */
    function esc(v) {
        return String(v === null || v === undefined ? '' : v)
            .replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    /* Items previously added by users via "Add New Row" are persisted as defaults for every
     * future form (per system). Merge them in now so every block includes them automatically. */
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

    /* Per-block state for dynamically added custom tabs (task 5) */
    const blockCustomTabState = {};
    function getCustomTabState(blockId) {
        if (!blockCustomTabState[blockId]) blockCustomTabState[blockId] = { count: 0, metaIndex: 0 };
        return blockCustomTabState[blockId];
    }

    /*
     * Convert flat old()-style block data (all fields at top level keyed by form name)
     * into the systems object structure used by the DB.
     * e.g. { fa_control_panels_total: '5' } → { fire_alarm: { fa_control_panels_total: '5' } }
     * Also buckets dynamic custom-tab fields (custom1_..., custom2_...) under their own key.
     */
    function flatToSystems(blockData) {
        const systems = {};
        Object.keys(blockData).forEach(key => {
            if (key === 'name' || key === 'id' || key === 'custom_tabs' || key === 'used_items' || key === 'required_items') return;
            let matched = false;
            for (const p of Object.keys(prefixToSystemName)) {
                if (key.startsWith(p + '_')) {
                    const sysName = prefixToSystemName[p];
                    if (!systems[sysName]) systems[sysName] = {};
                    systems[sysName][key] = blockData[key];
                    matched = true;
                    break;
                }
            }
            if (!matched) {
                const m = key.match(/^(custom\d+)_/);
                if (m) {
                    const sysName = m[1];
                    if (!systems[sysName]) systems[sysName] = {};
                    systems[sysName][key] = blockData[key];
                }
            }
        });
        return systems;
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

    /*
     * createBlockTab — builds the entire block HTML with values pre-filled.
     * existingSystems: the block.systems object from DB or converted from old() data.
     * customTabs: array of {key,label} for this block's dynamically-added tabs (task 5).
     */
    function createBlockTab(blockName, existingSystems, customTabs) {
        existingSystems = existingSystems || {};
        customTabs = customTabs || [];
        blockCount++;
        const blockId = `block-${blockCount}`;

        // Seed this block's custom-tab counter so newly-added tabs continue the numbering
        let maxCustomNum = 0;
        customTabs.forEach(tab => {
            const m = String(tab.key).match(/^custom(\d+)$/);
            if (m) maxCustomNum = Math.max(maxCustomNum, parseInt(m[1], 10));
        });
        blockCustomTabState[blockId] = { count: maxCustomNum, metaIndex: customTabs.length };

        /* --- Tab button --- */
        const tabButton = document.createElement('button');
        tabButton.type = 'button';
        tabButton.className = `tab-button ${blockCount === 1 ? 'active' : ''}`;
        tabButton.textContent = blockName || `Block ${blockCount}`;
        tabButton.setAttribute('data-block-tab', blockId);
        const deleteBtn = document.createElement('span');
        deleteBtn.style.cssText = 'margin-left:10px;cursor:pointer;color:#d81b60;';
        deleteBtn.innerHTML = '✕';
        deleteBtn.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); deleteBlock(blockId, tabButton); });
        tabButton.appendChild(deleteBtn);
        blockTabsNav.appendChild(tabButton);
        tabButton.addEventListener('click', function(e) { if (e.target.tagName !== 'SPAN') switchBlockTab(blockId); });

        /* --- Tab content --- */
        const tabContent = document.createElement('div');
        tabContent.className = `tab-content ${blockCount === 1 ? 'active' : ''}`;
        tabContent.id = blockId;

        let customTabsMetaHtml = '';
        customTabs.forEach((tab, i) => {
            customTabsMetaHtml += `<input type="hidden" name="blocks[${blockCount}][custom_tabs][${i}][key]" value="${esc(tab.key)}">`;
            customTabsMetaHtml += `<input type="hidden" name="blocks[${blockCount}][custom_tabs][${i}][label]" value="${esc(tab.label || tab.key)}">`;
        });

        let html = `
            <div style="margin-bottom:20px;padding:15px;background-color:#f9f9f9;border-radius:4px;">
                <div class="row"><div class="col-md-4"><div class="form-group">
                    <label style="font-weight:500;">Block Name:</label>
                    <input type="text" name="blocks[${blockCount}][name]" class="form-control block-name-input"
                        value="${esc(blockName)}" placeholder="e.g. Block A" required>
                </div></div></div>
            </div>
            <input type="hidden" name="blocks[${blockCount}][id]" value="${blockId}">
            <div id="${blockId}-custom-tabs-meta">${customTabsMetaHtml}</div>
            <div class="block-system-tabs" style="margin-top:20px;">
            <div class="tabs-nav" style="border-bottom:1px solid #999;">`;

        Object.keys(systemItems).forEach((prefix, idx) => {
            html += `<button type="button" class="tab-button ${idx===0?'active':''}" data-block-system-tab="${blockId}-system-${prefix}">${systemItems[prefix].label}</button>`;
        });
        customTabs.forEach(tab => {
            html += `<button type="button" class="tab-button" data-block-system-tab="${blockId}-system-${tab.key}">${esc(tab.label || tab.key)}</button>`;
        });
        html += `<button type="button" class="tab-button add-custom-tab-btn" data-block-id="${blockId}" style="background-color:#28a745;color:#fff;" title="Add a custom inspection section">+ Add Tab</button>`;
        html += `</div>`;

        Object.keys(systemItems).forEach((prefix) => {
            const sd       = systemItems[prefix];
            const sysName  = prefixToSystemName[prefix];
            const sysFields = existingSystems[sysName] || {};

            html += `<div class="tab-content ${prefix==='fa'?'active':''}" id="${blockId}-system-${prefix}">`;
            html += `<div class="section-header">${sd.label}</div>`;
            html += `<table class="system-table"><thead><tr>
                        <th>Item</th><th>Total Qty</th><th>Qty Damaged/Defective</th>
                        <th>Urgent Action Required</th><th>Remarks</th><th>Attachment</th><th>Action</th>
                     </tr></thead><tbody>`;

            sd.items.forEach(item => {
                const slug = item.toLowerCase().replace(/\s+/g,'_').replace(/[^\w_]/g,'');
                const fTotal   = `${prefix}_${slug}_total`;
                const fDefect  = `${prefix}_${slug}_defective`;
                const fStatus  = `${prefix}_${slug}_status`;
                const fUrgent  = `${prefix}_${slug}_urgent`;
                const fRemarks = `${prefix}_${slug}_remarks`;
                const fAttach  = `${prefix}_${slug}_attachment`;

                const totalVal   = esc(sysFields[fTotal]   ?? '');
                const defVal     = esc(sysFields[fDefect]  ?? '');
                const remarksVal = esc(sysFields[fRemarks] ?? '');
                const urgentVal  = sysFields[fUrgent]  || 'no';
                const statusVal  = sysFields[fStatus]  || 'not_working';
                const attachArr  = sysFields[fAttach];

                /* Render existing attachment links */
                let existingAttach = '';
                if (Array.isArray(attachArr) && attachArr.length > 0) {
                    attachArr.forEach(p => {
                        existingAttach += `<a href="/storage/${esc(p)}" target="_blank" style="display:block;font-size:11px;color:#007bff;">📎 ${esc(p.split('/').pop())}</a>`;
                    });
                }

                let defCol = '';
                if (sd.type === 'complex') {
                    defCol = `<td><input type="number" name="blocks[${blockCount}][${fDefect}]" value="${defVal}" style="width:60px;"></td>`;
                } else {
                    defCol = `<td>
                        <label><input type="radio" name="blocks[${blockCount}][${fStatus}]" value="working" ${statusVal==='working'?'checked':''}> Working</label>
                        <label><input type="radio" name="blocks[${blockCount}][${fStatus}]" value="not_working" ${statusVal!=='working'?'checked':''}> Not Working</label>
                    </td>`;
                }

                html += `<tr>
                    <td class="text-left">${esc(item)}</td>
                    <td class="locked-cell"><input type="number" name="blocks[${blockCount}][${fTotal}]" value="${totalVal}" readonly style="width:60px;"></td>
                    ${defCol}
                    <td class="locked-cell">
                        <label><input type="radio" name="blocks[${blockCount}][${fUrgent}]" value="yes" ${urgentVal==='yes'?'checked':''}> Yes</label>
                        <label><input type="radio" name="blocks[${blockCount}][${fUrgent}]" value="no" ${urgentVal!=='yes'?'checked':''}> No</label>
                    </td>
                    <td><input type="text" name="blocks[${blockCount}][${fRemarks}]" value="${remarksVal}" style="width:100%;"></td>
                    <td>
                        ${existingAttach}
                        <input type="file" name="blocks[${blockCount}][${fAttach}][]" accept="image/*,.pdf" multiple style="font-size:11px;">
                    </td>
                    <td></td>
                </tr>`;
            });

            html += `</tbody></table>`;
            html += `<button type="button" class="btn add-block-row-btn btn-sm" data-block-id="${blockId}" data-prefix="${prefix}" style="margin-top:10px;">Add New Row</button>`;
            html += usedRequiredItemsHTML(blockCount, prefix, sysFields[`${prefix}_used_items`], sysFields[`${prefix}_required_items`]);
            html += `</div>`;
        });

        // Custom tabs (task 5): reconstruct with whatever items were previously ad-hoc-added
        customTabs.forEach(tab => {
            const customKey = tab.key;
            const label = tab.label || customKey;
            const sysFields = existingSystems[customKey] || {};
            const itemSlugRe = new RegExp(`^${customKey}_(.+)_(total|defective|urgent|remarks|attachment)$`);
            const itemSlugs = [];
            Object.keys(sysFields).forEach(k => {
                const m = k.match(itemSlugRe);
                if (m && !itemSlugs.includes(m[1])) itemSlugs.push(m[1]);
            });

            html += `<div class="tab-content" id="${blockId}-system-${customKey}">`;
            html += `<div class="section-header">${esc(label)}</div>`;
            html += `<table class="system-table" data-block-id="${blockId}" data-prefix="${customKey}"><thead><tr>
                        <th>Item</th><th>Total Qty</th><th>Qty Damaged/Defective</th>
                        <th>Urgent Action Required</th><th>Remarks</th><th>Attachment</th><th>Action</th>
                     </tr></thead><tbody>`;

            itemSlugs.forEach(slug => {
                const fTotal   = `${customKey}_${slug}_total`;
                const fDefect  = `${customKey}_${slug}_defective`;
                const fUrgent  = `${customKey}_${slug}_urgent`;
                const fRemarks = `${customKey}_${slug}_remarks`;
                const fAttach  = `${customKey}_${slug}_attachment`;

                const totalVal   = esc(sysFields[fTotal]   ?? '');
                const defVal     = esc(sysFields[fDefect]  ?? '');
                const remarksVal = esc(sysFields[fRemarks] ?? '');
                const urgentVal  = sysFields[fUrgent] || 'no';
                const attachArr  = sysFields[fAttach];

                let existingAttach = '';
                if (Array.isArray(attachArr) && attachArr.length > 0) {
                    attachArr.forEach(p => {
                        existingAttach += `<a href="/storage/${esc(p)}" target="_blank" style="display:block;font-size:11px;color:#007bff;">📎 ${esc(p.split('/').pop())}</a>`;
                    });
                }

                html += `<tr>
                    <td class="text-left">${esc(slug.replace(/_/g, ' '))}</td>
                    <td class="locked-cell"><input type="number" name="blocks[${blockCount}][${fTotal}]" value="${totalVal}" readonly style="width:60px;"></td>
                    <td><input type="number" name="blocks[${blockCount}][${fDefect}]" value="${defVal}" style="width:60px;"></td>
                    <td class="locked-cell">
                        <label><input type="radio" name="blocks[${blockCount}][${fUrgent}]" value="yes" ${urgentVal==='yes'?'checked':''}> Yes</label>
                        <label><input type="radio" name="blocks[${blockCount}][${fUrgent}]" value="no" ${urgentVal!=='yes'?'checked':''}> No</label>
                    </td>
                    <td><input type="text" name="blocks[${blockCount}][${fRemarks}]" value="${remarksVal}" style="width:100%;"></td>
                    <td>
                        ${existingAttach}
                        <input type="file" name="blocks[${blockCount}][${fAttach}][]" accept="image/*,.pdf" multiple style="font-size:11px;">
                    </td>
                    <td></td>
                </tr>`;
            });

            html += `</tbody></table>`;
            html += `<button type="button" class="btn add-block-row-btn btn-sm" data-block-id="${blockId}" data-prefix="${customKey}" style="margin-top:10px;">Add New Row</button>`;
            html += usedRequiredItemsHTML(blockCount, customKey, sysFields[`${customKey}_used_items`], sysFields[`${customKey}_required_items`]);
            html += `</div>`;
        });

        html += `</div>`;

        tabContent.innerHTML = html;
        blockTabsContent.appendChild(tabContent);
        return { blockId, blockNum: blockCount };
    }

    function switchBlockTab(blockId) {
        document.querySelectorAll('#block-tabs-nav .tab-button').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('#block-tabs-content > .tab-content').forEach(c => c.classList.remove('active'));
        document.querySelector(`[data-block-tab="${blockId}"]`)?.classList.add('active');
        document.getElementById(blockId)?.classList.add('active');
    }

    function deleteBlock(blockId, tabButton) {
        tabButton.remove();
        document.getElementById(blockId)?.remove();
        if (blockTabsNav.children.length > 0) switchBlockTab(blockTabsNav.children[0].getAttribute('data-block-tab'));
    }

    /* --- Initialise blocks -------------------------------------------------- */
    if (Object.keys(_existingBlockInfo).length > 0 && Object.keys(_oldBlocksData).length === 0) {
        /* Normal edit: load from saved report */
        Object.keys(_existingBlockInfo).forEach(blockId => {
            const bd = _existingBlockInfo[blockId];
            createBlockTab(bd.name || '', bd.systems || {}, bd.custom_tabs || []);
        });
        switchBlockTab('block-1');
    } else if (Object.keys(_oldBlocksData).length > 0) {
        /* Validation failure on update: restore from old() */
        Object.keys(_oldBlocksData).forEach(idx => {
            const bd = _oldBlocksData[idx];
            createBlockTab(bd.name || '', flatToSystems(bd), bd.custom_tabs || []);
        });
        switchBlockTab('block-1');
    } else {
        createBlockTab();
    }

    /* --- Add new block ------------------------------------------------------- */
    addBlockBtn.addEventListener('click', function() {
        const { blockId } = createBlockTab();
        switchBlockTab(blockId);
    });

    /* --- System tab switching (scoped to parent block) ----------------------- */
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tab-button') && e.target.hasAttribute('data-block-system-tab')) {
            const tabId = e.target.getAttribute('data-block-system-tab');
            const blockId = tabId.split('-system-')[0];
            const blockDiv = document.getElementById(blockId);
            if (blockDiv) {
                blockDiv.querySelectorAll('.block-system-tabs .tab-button').forEach(b => b.classList.remove('active'));
                blockDiv.querySelectorAll('.block-system-tabs .tab-content').forEach(c => c.classList.remove('active'));
                e.target.classList.add('active');
                document.getElementById(tabId)?.classList.add('active');
            }
        }
    });

    /* --- Add new row to a system table -------------------------------------- */
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
                ? `<td><input type="number" name="${base}_defective]" style="width:60px;"></td>`
                : `<td><label><input type="radio" name="${base}_status]" value="working"> Working</label><label><input type="radio" name="${base}_status]" value="not_working" checked> Not Working</label></td>`;

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="${base}_name]" placeholder="New Item" style="width:100%;"></td>
                <td class="locked-cell"><input type="number" name="${base}_total]" readonly style="width:60px;"></td>
                ${defectiveCol}
                <td class="locked-cell"><label><input type="radio" name="${base}_urgent]" value="yes"> Yes</label><label><input type="radio" name="${base}_urgent]" value="no" checked> No</label></td>
                <td><input type="text" name="${base}_remarks]" placeholder="Remarks" style="width:100%;"></td>
                <td><input type="file" name="${base}_attachment][]" accept="image/*,.pdf" multiple style="font-size:11px;"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();">Remove</button></td>
            `;
            tbody.appendChild(newRow);
        }
    });

    /* --- Add a new custom tab (section) to a block ---------------------------- */
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
                <table class="system-table" data-block-id="${blockId}" data-prefix="${customKey}">
                    <thead><tr>
                        <th>Item</th><th>Total Qty</th><th>Qty Damaged/Defective</th>
                        <th>Urgent Action Required</th><th>Remarks</th><th>Attachment</th><th>Action</th>
                    </tr></thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn add-block-row-btn btn-sm" data-block-id="${blockId}" data-prefix="${customKey}" style="margin-top:10px;">Add New Row</button>
                ${usedRequiredItemsHTML(blockNum, customKey)}
            `;
            systemTabsDiv.appendChild(tabContent);

            const metaContainer = document.getElementById(`${blockId}-custom-tabs-meta`);
            const idx = state.metaIndex++;
            metaContainer.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="blocks[${blockNum}][custom_tabs][${idx}][key]" value="${customKey}">
                <input type="hidden" name="blocks[${blockNum}][custom_tabs][${idx}][label]" value="${esc(trimmedLabel)}">
            `);

            blockDiv.querySelectorAll('.block-system-tabs .tab-button').forEach(b => b.classList.remove('active'));
            blockDiv.querySelectorAll('.block-system-tabs .tab-content').forEach(c => c.classList.remove('active'));
            tabBtn.classList.add('active');
            tabContent.classList.add('active');
        }
    });
});
</script>

                        {{-- Used Items / Required Items are now captured per-block (see Block Information tabs above) --}}

                        <!-- Notes -->
                        <div class="section-header mt-3">Notes</div>
                        <div class="mb-3">
                            <textarea name="notes_used_items" rows="4" placeholder="Enter any notes here..." style="width:100%;resize:vertical;border:1px solid #ccc;border-radius:4px;padding:6px;font-size:13px;">{{ old('notes_used_items', $report->notes_used_items) }}</textarea>
                        </div>

                        <!-- Photos Section -->
                        <div class="section-header">📸 Photos</div>
                        @php
                            $existingPhotos = is_array($report->photos_data) ? $report->photos_data : json_decode($report->photos_data, true);
                            $existingPhotos = $existingPhotos ?? [];
                        @endphp
                        <table class="system-table" id="photos-table">
                            <thead>
                                <tr>
                                    <th>Reference (Item/Equipment)</th>
                                    <th>Existing Photos</th>
                                    <th>Upload New</th>
                                    <th>Remarks / Label</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($existingPhotos as $i => $photo)
                                <tr>
                                    <td><input type="text" name="photo_reference[]" value="{{ $photo['reference'] ?? '' }}" style="width:100%;"></td>
                                    <td>
                                        @foreach($photo['files'] ?? [] as $file)
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank" style="display:block;font-size:11px;">📎 {{ basename($file) }}</a>
                                            <input type="hidden" name="existing_photo_files[{{ $i }}][]" value="{{ $file }}">
                                        @endforeach
                                    </td>
                                    <td><input type="file" name="photo_files[{{ $i }}][]" accept="image/*" multiple></td>
                                    <td><input type="text" name="photo_label[]" value="{{ $photo['label'] ?? '' }}" style="width:100%;"></td>
                                    <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
                                </tr>
                                @empty
                                <tr>
                                    <td><input type="text" name="photo_reference[]" placeholder="e.g., Fire Pump Panel" style="width:100%;"></td>
                                    <td>-</td>
                                    <td><input type="file" name="photo_files[0][]" accept="image/*" multiple></td>
                                    <td><input type="text" name="photo_label[]" placeholder="Label or remarks" style="width:100%;"></td>
                                    <td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <button type="button" class="btn add-photo-row-btn">Add New Photo Row</button>

                        <hr>
                        <h6>Client Details</h6>
                        <div class="row">
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Client EID Details</label>
                                <input type="text" name="client_eid_details" class="form-control"
                                    value="{{ old('client_eid_details', $report->client_eid_details) }}">
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Client Phone Number</label>
                                <input type="text" name="client_phone" class="form-control"
                                    value="{{ old('client_phone', $report->client_phone) }}">
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label>Scope Attachment</label>
                                @php
                                    $existingAttachments = is_array($report->file_path) ? $report->file_path : json_decode($report->file_path, true);
                                    $existingAttachments = $existingAttachments ?? [];
                                @endphp
                                @foreach($existingAttachments as $idx => $path)
                                    <div><a href="{{ asset('storage/'.$path) }}" target="_blank" style="font-size:12px;">📎 Attachment {{ $idx+1 }}</a>
                                    <input type="hidden" name="existing_scope_attachments[]" value="{{ $path }}"></div>
                                @endforeach
                                <input type="file" name="scope_attachment[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <small class="text-muted">Upload new files to replace existing</small>
                            </div>
                        </div>

                        <!-- Signature -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <p style="font-weight:500;font-size:13px;margin-bottom:4px;">Client Signature</p>
                                @if($report->client_signature)
                                    <p style="font-size:12px;color:#666;">Existing signature:</p>
                                    <img src="{{ $report->client_signature }}" alt="Existing Signature"
                                        style="border:1px solid #ccc;border-radius:4px;max-width:250px;height:80px;display:block;margin-bottom:8px;">
                                    <p style="font-size:12px;color:#666;">Draw below to replace:</p>
                                @endif
                                <canvas id="clientSignatureCanvas" width="300" height="100"
                                    style="border:1px solid #ccc;border-radius:4px;touch-action:none;max-width:100%;"></canvas>
                                <div style="margin-top:4px;">
                                    <button type="button" id="clearClientCanvas" class="btn btn-sm btn-secondary" style="border:none;">Clear</button>
                                    <button type="button" id="saveClientCanvas" class="btn btn-sm btn-success" style="border:none;">Save Signature</button>
                                    <input type="hidden" id="clientSignatureInput" name="client_signature">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            @if($report->isDraft())
                            <button type="submit" name="save_draft" value="1" class="btn btn-secondary btn-sm">Save Draft</button>
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Submit this report for approval? It can no longer be edited as a draft.')">Submit Report</button>
                            <a href="{{ route('projects.drafts') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            @else
                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                            <a href="{{ route('projects.reportStatus') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Signature canvas with touch support
    const canvas = document.getElementById('clientSignatureCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let drawing = false;
        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            if (e.touches) return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
            return { x: e.clientX - rect.left, y: e.clientY - rect.top };
        }
        function startDraw(e) { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
        function drawLine(e) {
            e.preventDefault(); if (!drawing) return;
            ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000';
            const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); ctx.beginPath(); ctx.moveTo(p.x, p.y);
        }
        function stopDraw() { drawing = false; ctx.beginPath(); }
        canvas.addEventListener('mousedown', startDraw); canvas.addEventListener('mousemove', drawLine);
        canvas.addEventListener('mouseup', stopDraw); canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, {passive:false}); canvas.addEventListener('touchmove', drawLine, {passive:false});
        canvas.addEventListener('touchend', stopDraw, {passive:false});
        document.getElementById('clearClientCanvas')?.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('clientSignatureInput').value = '';
        });
        document.getElementById('saveClientCanvas')?.addEventListener('click', () => {
            document.getElementById('clientSignatureInput').value = canvas.toDataURL();
            alert('Signature saved!');
        });
    }

    // Photos add row
    let photoIdx = {{ count($existingPhotos) }};
    document.querySelector('.add-photo-row-btn')?.addEventListener('click', function() {
        const tbody = document.getElementById('photos-table').querySelector('tbody');
        const row = document.createElement('tr');
        row.innerHTML = `<td><input type="text" name="photo_reference[]" placeholder="e.g., Fire Pump Panel" style="width:100%;"></td><td>-</td><td><input type="file" name="photo_files[${photoIdx}][]" accept="image/*" multiple></td><td><input type="text" name="photo_label[]" placeholder="Label" style="width:100%;"></td><td><button type="button" class="btn btn-sm remove-row-btn">Remove</button></td>`;
        tbody.appendChild(row);
        photoIdx++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row-btn')) e.target.closest('tr').remove();
    });
});
</script>
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
$(document).ready(function() {
    $('#date_edit').daterangepicker({ singleDatePicker: true, locale: { format: 'YYYY-MM-DD' } });
});
</script>
@endsection
