@extends('layouts.master')
@section('content')
    <style>
        body {
            margin: 0;
            background-color: #f9f9f9;
        }
        .form-container {
            width: 85%;
            margin: 0 auto;
            background-color: white;
            padding: 25px; 
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .section-header{
            background-color: #fdf2e3;
            text-align: left;
            padding: 8px 10px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 14px;
            border-radius: 4px;
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
            background-color: #E47770;
            color: white; 
        }
        label{
            font-weight: 500;
            font-size: 13px;
        }
        .text-left{
            text-align: left;
        }
        .system-table th {
            background-color: #E47770;
            color: white;
            font-weight: 500;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: 500;
            font-size: 13px;
            margin-right: 10px;
        }
        .info-value {
            font-size: 13px;
            padding: 4px 8px;
            background-color: #f9f9f9;
            border-radius: 4px;
            display: inline-block;
        }
        .na-indicator {
            color: #999;
            font-style: italic;
        }
        .attachment-box {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            min-height: 60px;
        }
        .attachment-item {
            display: inline-block;
            padding: 5px 10px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
        }
        .attachment-item a {
            color: #007bff;
            text-decoration: none;
        }
        .attachment-item a:hover {
            text-decoration: underline;
        }
        .photo-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
        }
        .photo-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background-color: white;
        }
        .photo-item img {
            max-width: 150px;
            max-height: 150px;
            display: block;
            margin-bottom: 5px;
            cursor: pointer;
        }
        .photo-item img:hover {
            opacity: 0.8;
        }

        .info-box {
            display: flex;
            width: 100%;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .info-label {
            width: 40%;
            background-color: antiquewhite;
            text-align: center;
            padding: 10px;
            font-weight: 500;
            font-size: 13px;
        }

        .info-value {
            flex: 1;
            text-align: center;
            background-color: #fff8ef !important;
            padding: 10px;
            font-size: 13px;
        }

        /* Block Tabs Styles */
        .block-tabs-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            border-bottom: 2px solid #d81b60;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .block-tab-button {
            padding: 8px 16px;
            background-color: #f5f5f5;
            border: none;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: #333;
        }
        .block-tab-button:hover {
            background-color: #fdf2e3;
        }
        .block-tab-button.active {
            background-color: #d81b60;
            color: white;
        }
        .block-tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .block-tab-content.active {
            display: block;
        }
        .system-tab-button {
            padding: 6px 12px;
            background-color: #f5f5f5;
            border: none;
            border-radius: 4px 4px 0 0;
            cursor: pointer;
            font-size: 11px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: #333;
        }
        .system-tab-button:hover {
            background-color: #fdf2e3;
        }
        .system-tab-button.active {
            background-color: #E47770;
            color: white;
        }
        .system-tab-content {
            display: none;
        }
        .system-tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media print {
            /* ── Page setup (A4 portrait) ── */
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }

            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                min-width: 0 !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* ── Layout shell: remove app chrome spacing ── */
            .wrapper,
            .content-wrapper,
            .main.header {
                margin: 0 !important;
                padding: 0 !important;
                min-height: 0 !important;
                width: 100% !important;
                transform: none !important;
            }

            .main-header,
            .main-footer,
            .main-sidebar,
            .content-header,
            .no-print,
            .btn {
                display: none !important;
            }

            .form-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background: #fff !important;
            }

            .container,
            .container-fluid {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            [class*="col-"] {
                padding-left: 6px !important;
                padding-right: 6px !important;
            }

            /* ── Tighter vertical rhythm for print ── */
            .section-header {
                margin-top: 8px !important;
                margin-bottom: 5px !important;
                padding: 5px 8px !important;
                break-after: avoid;
                page-break-after: avoid;
            }

            .info-row {
                margin-bottom: 6px !important;
            }

            .attachment-box,
            .photo-gallery {
                min-height: 0 !important;
                padding: 4px !important;
                gap: 6px !important;
            }

            /* ── Tab panes: show all content, hide navigation ── */
            .block-tabs-nav,
            .system-tabs-nav,
            .block-tab-button,
            .system-tab-button {
                display: none !important;
            }

            .block-tab-content,
            .system-tab-content {
                display: block !important;
                visibility: visible !important;
                height: auto !important;
                overflow: visible !important;
                opacity: 1 !important;
                animation: none !important;
                transform: none !important;
                break-before: auto !important;
                page-break-before: auto !important;
                break-inside: auto !important;
                page-break-inside: auto !important;
                margin-bottom: 6px !important;
            }

            /* Block separator (section-header already labels each system) */
            .print-block-header {
                display: block !important;
                background-color: #d81b60 !important;
                color: #fff !important;
                padding: 6px 10px !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                border-radius: 4px !important;
                margin-top: 10px !important;
                margin-bottom: 6px !important;
                break-after: avoid;
                page-break-after: avoid;
            }

            /* Duplicate of .section-header inside each system tab */
            .print-system-header {
                display: none !important;
            }

            /* ── Tables: flow across pages, repeat headers, keep rows intact ── */
            table,
            .system-table {
                width: 100% !important;
                margin-top: 4px !important;
                break-inside: auto !important;
                page-break-inside: auto !important;
            }

            thead {
                display: table-header-group !important;
            }

            tfoot {
                display: table-footer-group !important;
            }

            tr {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            th, td {
                padding: 4px 5px !important;
                font-size: 11px !important;
            }

            /* Keep section label attached to the start of its content */
            .system-tab-content > .section-header {
                break-after: avoid;
                page-break-after: avoid;
            }

            /* ── Cohesive sections: avoid splitting small blocks ── */
            .info-box,
            .print-no-break {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .print-signature-section img {
                max-width: 220px !important;
                height: auto !important;
            }

            .photo-item img,
            table img {
                max-width: 90px !important;
                max-height: 90px !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            /* Readonly textarea: collapse to content height */
            .print-shrink-textarea {
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
                field-sizing: content;
                resize: none !important;
                white-space: pre-wrap !important;
                word-wrap: break-word !important;
            }

            .print-no-break[style*="min-height"] {
                min-height: 0 !important;
            }

            /* Letterhead: only print the visible header image */
            header img.images[style*="display:none"],
            header img.images[style*="display: none"],
            .expert_img,
            .ftsits_img {
                display: none !important;
            }

            header img.fts_img {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 0 6px 0 !important;
            }

            a[href]:after {
                content: none !important;
            }

            /* Hide all attachment content in print */
            .attachments-col,
            .print-hide-attachments {
                display: none !important;
            }
        }

    </style>

    <div class="main header" style="margin-top: 12px;margin-bottom: 12px;">
        <div class="form-container">
            <header>
                <img class="fts_img images" src="{{asset('dist/img/fts_latter_head.jpeg')}}" style="width:100%;margin:auto;" />
                <img class="expert_img images" src="{{asset('dist/img/experts_letter_head.jpeg')}}" style="width:100%; margin:auto; display:none;" />
                <img class="ftsits_img images" src="{{asset('dist/img/ftsits_letter_head.jpeg')}}" style="width:100%;margin:auto; display:none;" />
            </header>
            
            <!-- General Information Section -->
            <div class="section-header">General Information</div>
            
            <div class="container">
                <div class="row g-2 align-items-center" style="margin-top: 10px;">
                    <!-- Reference Number -->
                    <div class="col-md-6 d-flex">
                        <div class="col-5 d-flex justify-content-center align-items-center text-center" 
                            style="background-color: antiquewhite; border-radius: 5px 0 0 5px; padding: 10px;">
                            <p class="m-0 fw-medium" style=" font-size: 13px;">Reference Number</p>
                        </div>
                        <div class=" col-7 d-flex justify-content-center align-items-center text-center" 
                            style="  border-radius: 0 5px 5px 0; padding: 10px;">
                            <span style="font-size: 13px;">{{ $report->reference_number ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-md-6 d-flex">
                        <div class="col-5 d-flex justify-content-center align-items-center text-center" 
                            style="background-color: antiquewhite; border-radius: 5px 0 0 5px; padding: 10px;">
                            <p class="m-0 fw-medium" style="font-size: 13px;">Date</p>
                        </div>
                        <div class="col-7 d-flex justify-content-center align-items-center text-center" 
                            style=" border-radius: 0 5px 5px 0; padding: 10px;">
                            <span style="font-size: 13px;">
                                {{ $report->date ? \Carbon\Carbon::parse($report->date)->format('d M Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="info-row" style="margin-top: 15px;">
                <p style="font-weight: 400;font-size:13px;margin-bottom: 12px;">Issuer (Inspector) Details</p>
            </div>
            
            <div class="container">
                <div class="row">
                    <div class="col-md-6 info-row">
                        <span class="info-label">Visiting Time:</span>
                        <span class="info-value">{{ $report->inspector_visiting_time ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6 info-row">
                        <span class="info-label">Leaving Time:</span>
                        <span class="info-value">{{ $report->inspector_leaving_time ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="info-row">
                <p style="font-weight: 400;font-size:13px;margin-bottom: 12px;">Project Details</p>
            </div>
            
            <div class="container" style="display: flex; flex-wrap: wrap; ">
                <div style="flex: 1; min-width: 160px;">
                    <span class="info-label">AMC Type:</span>
                    <span class="info-value">{{ ucfirst($report->amc_type ?? 'existing') }}</span>
                </div>
                <div style="flex: 1; min-width: 160px;">
                    <span class="info-label">Client:</span>
                    <span class="info-value">{{ $report->company->name ?? $report->manual_client_name ?? 'N/A' }}</span>
                </div>
                @if(($report->amc_type ?? 'existing') === 'existing')
                <div style="flex: 1; min-width: 160px;">
                    <span class="info-label">Project:</span>
                    <span class="info-value">{{ $report->project->subject ?? 'N/A' }}</span>
                </div>
                <div style="flex: 1; min-width: 160px;">
                    <span class="info-label">Visit Schedule:</span>
                    <span class="info-value">
                        @if($report->is_emergency_visit)
                            {{ $report->emergency_visit_date ? \Carbon\Carbon::parse($report->emergency_visit_date)->format('d M Y') : 'N/A' }}
                            <span style="background-color:#dc3545;color:#fff;border-radius:3px;padding:1px 6px;font-size:11px;margin-left:4px;">Emergency</span>
                        @else
                            {{ $report->visitSchedule ? \Carbon\Carbon::parse($report->visitSchedule->visit_date)->format('d M Y') : 'N/A' }}
                        @endif
                    </span>
                </div>
                @endif
            </div>

        <div class="container" style="margin-top: 12px;">
            <div class="row">
                <div class="col-md-6 info-row">
                    <span class="info-label">Location:</span>
                    <span class="info-value">{{ $report->site_location ?? 'N/A' }}</span>
                </div>
                <div class="col-md-6 info-row">
                    <span class="info-label">Site Name:</span>
                    <span class="info-value">{{ $report->site_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Block Information Section with Tabbed System Tables --}}
        @php
            use Illuminate\Support\Str;

            $blockInfo = is_array($report->block_info) ? $report->block_info : json_decode($report->block_info, true);
            $blockInfo = $blockInfo ?? [];

            // System labels and types mapping
            $systemConfig = [
                'fire_alarm' => ['label' => 'Fire Alarm System', 'type' => 'complex'],
                'fire_fighting' => ['label' => 'Fire Fighting Equipment', 'type' => 'complex'],
                'fm200' => ['label' => 'FM-200 System', 'type' => 'complex'],
                'foam_tank' => ['label' => 'Foam Bladder Tank', 'type' => 'complex'],
                'voice_evacuation' => ['label' => 'Voice Evacuation', 'type' => 'complex'],
                'emergency_lighting' => ['label' => 'Emergency Lighting', 'type' => 'complex'],
                'system_interfacing' => ['label' => 'System Interfacing', 'type' => 'simple'],
                'exit_route' => ['label' => 'Exit Route & Storage', 'type' => 'simple'],
                'storage_conditions' => ['label' => 'Storage Conditions', 'type' => 'simple'],
                'pump' => ['label' => 'Pump', 'type' => 'complex'],
                'deluge' => ['label' => 'Deluge Valve', 'type' => 'complex'],
            ];

            /**
             * A field counts as meaningful only when the user actually entered data.
             * Default form values (urgent=no, status=not_working) are ignored.
             */
            if (!function_exists('amcReportMeaningfulItemValue')) {
                function amcReportMeaningfulItemValue(string $field, $value): bool
                {
                    if ($value === null || $value === '') {
                        return false;
                    }

                    if ($field === 'attachment') {
                        return is_array($value) && count($value) > 0;
                    }

                    if ($field === 'urgent') {
                        return $value === 'yes';
                    }

                    if ($field === 'status') {
                        return $value === 'working';
                    }

                    if ($field === 'defective') {
                        if (is_numeric($value)) {
                            return (float) $value > 0;
                        }

                        return $value === 'yes';
                    }

                    if ($field === 'total') {
                        return $value !== '' && $value !== null;
                    }

                    return !empty($value);
                }

                function amcReportGroupSystemItems(array $sysData): array
                {
                    $items = [];

                    foreach ($sysData as $key => $value) {
                        if (preg_match('/^[a-z0-9]+_(.+)_(total|defective|urgent|remarks|attachment|status)$/', $key, $matches)) {
                            $items[$matches[1]][$matches[2]] = $value;
                        }
                    }

                    return $items;
                }

                function amcReportFilterMeaningfulItems(array $items): array
                {
                    $filtered = [];

                    foreach ($items as $slug => $fields) {
                        foreach ($fields as $field => $value) {
                            if (amcReportMeaningfulItemValue($field, $value)) {
                                $filtered[$slug] = $fields;
                                break;
                            }
                        }
                    }

                    return $filtered;
                }

                function amcReportSystemHasMeaningfulData(array $sysData): bool
                {
                    return count(amcReportFilterMeaningfulItems(amcReportGroupSystemItems($sysData))) > 0;
                }

                /**
                 * A system tab is worth showing if it has real inspection data, OR the
                 * inspector left Used Items / Required Items notes for it (even with no rows filled).
                 */
                function amcReportSystemHasDisplayableContent(array $sysData): bool
                {
                    return amcReportSystemHasMeaningfulData($sysData)
                        || amcReportExtractSpecialField($sysData, '_used_items') !== null
                        || amcReportExtractSpecialField($sysData, '_required_items') !== null;
                }

                /**
                 * Used Items / Required Items are stored per system-tab as "{prefix}_used_items"
                 * / "{prefix}_required_items" inside that system's data array. Find it generically
                 * without needing to know the exact prefix (fa, ff, custom1, ...).
                 */
                function amcReportExtractSpecialField(array $sysData, string $suffix): ?string
                {
                    foreach ($sysData as $k => $v) {
                        if (\Illuminate\Support\Str::endsWith($k, $suffix) && !empty($v)) {
                            return $v;
                        }
                    }
                    return null;
                }
            }
        @endphp

        @if(!empty($blockInfo) && count($blockInfo) > 0)
            @php
                // Only keep blocks that have at least one system with real inspection data
                // (or Used/Required Items notes left on a system with no rows filled)
                $populatedBlocks = [];
                foreach ($blockInfo as $blockId => $block) {
                    foreach ($block['systems'] ?? [] as $sysData) {
                        if (amcReportSystemHasDisplayableContent($sysData)) {
                            $populatedBlocks[$blockId] = $block;
                            break;
                        }
                    }
                }
            @endphp
            @if(count($populatedBlocks) > 0)
            <div class="section-header">Block Information & Inspection Details</div>

            {{-- Block Tabs Navigation --}}
            <div class="block-tabs-nav">
                @php $blockIdx = 0; @endphp
                @foreach($populatedBlocks as $blockId => $block)
                    <button type="button" class="block-tab-button {{ $blockIdx === 0 ? 'active' : '' }}" data-block-tab="{{ $blockId }}">
                        {{ $block['name'] ?? 'Unnamed Block' }}
                    </button>
                    @php $blockIdx++; @endphp
                @endforeach
            </div>

            {{-- Block Tab Contents --}}
            @php $blockIdx = 0; @endphp
            @foreach($populatedBlocks as $blockId => $block)
                <div class="block-tab-content {{ $blockIdx === 0 ? 'active' : '' }}" id="view-{{ $blockId }}">
                    {{-- Print-only block header (hidden on screen, visible when printing) --}}
                    <div class="print-block-header" style="display: none;">Block: {{ $block['name'] ?? 'Unnamed Block' }}</div>
                    
                    <div style="padding: 10px 0;">
                        <span class="info-label">Block Name:</span>
                        <span class="info-value" style="font-weight: 500;">{{ $block['name'] ?? 'N/A' }}</span>
                    </div>

                    @php $systems = $block['systems'] ?? []; @endphp

                    @if(!empty($systems))
                        {{-- System Tabs Navigation --}}
                        <div class="system-tabs-nav" style="display: flex; flex-wrap: wrap; gap: 4px; border-bottom: 1px solid #999; padding-bottom: 8px; margin-bottom: 15px;">
                            @php
                                $sysIdx = 0;
                                $availableSystems = [];
                                foreach ($systemConfig as $sysKey => $sysCfg) {
                                    if (isset($systems[$sysKey]) && amcReportSystemHasDisplayableContent($systems[$sysKey])) {
                                        $availableSystems[$sysKey] = $sysCfg;
                                    }
                                }
                                // Dynamically added custom tabs (task 5) - scoped to this block
                                foreach ($block['custom_tabs'] ?? [] as $tab) {
                                    $tabKey = $tab['key'] ?? null;
                                    if ($tabKey && isset($systems[$tabKey]) && amcReportSystemHasDisplayableContent($systems[$tabKey])) {
                                        $availableSystems[$tabKey] = ['label' => $tab['label'] ?? $tabKey, 'type' => 'complex'];
                                    }
                                }
                            @endphp
                            @foreach($availableSystems as $sysKey => $sysCfg)
                                <button type="button" class="system-tab-button {{ $sysIdx === 0 ? 'active' : '' }}" data-system-tab="{{ $blockId }}-sys-{{ $sysKey }}">
                                    {{ $sysCfg['label'] }}
                                </button>
                                @php $sysIdx++; @endphp
                            @endforeach
                        </div>

                        {{-- System Tab Contents --}}
                        @php $sysIdx = 0; @endphp
                        @foreach($availableSystems as $sysKey => $sysCfg)
                            @php
                                $sysData = $systems[$sysKey];
                                $sysType = $sysCfg['type'];
                                $filtered = amcReportFilterMeaningfulItems(amcReportGroupSystemItems($sysData));
                                $sysUsedItems = amcReportExtractSpecialField($sysData, '_used_items');
                                $sysRequiredItems = amcReportExtractSpecialField($sysData, '_required_items');
                            @endphp
                            @if(count($filtered) > 0 || $sysUsedItems || $sysRequiredItems)
                            <div class="system-tab-content {{ $sysIdx === 0 ? 'active' : '' }}" id="{{ $blockId }}-sys-{{ $sysKey }}">
                                {{-- Print-only system header (hidden on screen, visible when printing) --}}
                                <div class="print-system-header" style="display: none;">{{ $sysCfg['label'] }}</div>

                                <div class="section-header">{{ $sysCfg['label'] }}</div>
                                @if(count($filtered) > 0)
                                    @if($sysType === 'complex')
                                        <table class="system-table">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Total Qty</th>
                                                    <th>Qty Damaged/Defective</th>
                                                    <th>Urgent Action Required</th>
                                                    <th>Remarks</th>
                                                    <th class="attachments-col">Attachments</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($filtered as $itemSlug => $itemData)
                                                    @php
                                                        $itemName = ucwords(str_replace('_', ' ', $itemSlug));
                                                        $total = $itemData['total'] ?? '';
                                                        $defective = $itemData['defective'] ?? '';
                                                        $urgent = $itemData['urgent'] ?? '';
                                                        $remarks = $itemData['remarks'] ?? '';
                                                        $attachments = $itemData['attachment'] ?? [];

                                                        $urgentDisplay = $urgent === 'yes' ? '✓ Yes' : ($urgent === 'no' ? '✓ No' : '-');
                                                        $defectiveDisplay = is_numeric($defective) ? $defective : ($defective === 'yes' ? '✓ Yes' : ($defective === 'no' ? '✓ No' : '-'));
                                                    @endphp
                                                    <tr>
                                                        <td class="text-left">{{ $itemName }}</td>
                                                        <td>{{ $total ?: '-' }}</td>
                                                        <td>{{ $defectiveDisplay }}</td>
                                                        <td>{{ $urgentDisplay }}</td>
                                                        <td class="text-left">{{ $remarks ?: '-' }}</td>
                                                        <td class="attachments-col">
                                                            @if(is_array($attachments) && count($attachments) > 0)
                                                                @foreach($attachments as $filePath)
                                                                    @php
                                                                        $fileUrl = asset('storage/' . $filePath);
                                                                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                                        <a href="{{ $fileUrl }}" target="_blank">
                                                                            <img src="{{ $fileUrl }}" width="80" style="margin:4px;border:1px solid #ccc;">
                                                                        </a>
                                                                    @elseif($ext === 'pdf')
                                                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">View PDF</a>
                                                                    @else
                                                                        <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-secondary me-1">Download</a>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        {{-- Simple / status-type table --}}
                                        <table class="system-table">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Total Qty</th>
                                                    <th>Status</th>
                                                    <th>Urgent Rectification</th>
                                                    <th>Remarks</th>
                                                    <th class="attachments-col">Attachments</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($filtered as $itemSlug => $itemData)
                                                    @php
                                                        $itemName = ucwords(str_replace('_', ' ', $itemSlug));
                                                        $total = $itemData['total'] ?? '';
                                                        $status = $itemData['status'] ?? ($itemData['defective'] ?? '');
                                                        $urgent = $itemData['urgent'] ?? '';
                                                        $remarks = $itemData['remarks'] ?? '';
                                                        $attachments = $itemData['attachment'] ?? [];

                                                        $statusDisplay = $status === 'working' ? '✓ Working' : ($status === 'not_working' ? '✗ Not Working' : '-');
                                                        $urgentDisplay = $urgent === 'yes' ? '✓ Yes' : ($urgent === 'no' ? '✓ No' : '-');
                                                    @endphp
                                                    <tr>
                                                        <td class="text-left">{{ $itemName }}</td>
                                                        <td>{{ $total ?: '-' }}</td>
                                                        <td>{{ $statusDisplay }}</td>
                                                        <td>{{ $urgentDisplay }}</td>
                                                        <td class="text-left">{{ $remarks ?: '-' }}</td>
                                                        <td class="attachments-col">
                                                            @if(is_array($attachments) && count($attachments) > 0)
                                                                @foreach($attachments as $filePath)
                                                                    @php
                                                                        $fileUrl = asset('storage/' . $filePath);
                                                                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                                        <a href="{{ $fileUrl }}" target="_blank">
                                                                            <img src="{{ $fileUrl }}" width="80" style="margin:4px;border:1px solid #ccc;">
                                                                        </a>
                                                                    @elseif($ext === 'pdf')
                                                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">View PDF</a>
                                                                    @else
                                                                        <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-secondary me-1">Download</a>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                @endif

                                @if($sysUsedItems)
                                    <div class="section-header">Used Items</div>
                                    <div style="padding: 10px; background-color: #f9f9f9; border-radius: 4px; min-height: 40px;">
                                        <p style="font-size: 13px; margin: 0; white-space: pre-wrap;">{{ $sysUsedItems }}</p>
                                    </div>
                                @endif

                                @if($sysRequiredItems)
                                    <div class="section-header">Required Items</div>
                                    <div style="padding: 10px; background-color: #f9f9f9; border-radius: 4px; min-height: 40px;">
                                        <p style="font-size: 13px; margin: 0; white-space: pre-wrap;">{{ $sysRequiredItems }}</p>
                                    </div>
                                @endif
                            </div>
                            @php $sysIdx++; @endphp
                            @endif
                        @endforeach

                        @if(count($availableSystems) === 0)
                            <p style="font-size: 13px; color: #999;">No inspection data recorded for this block.</p>
                        @endif
                    @else
                        <p style="font-size: 13px; color: #999;">No inspection data recorded for this block.</p>
                    @endif
                </div>
                @php $blockIdx++; @endphp
            @endforeach
            @endif
        @else
            {{-- Fallback: show old column-based data if no block_info (backward compat) --}}
            @php
                // Old render functions kept for backward compatibility
                function renderSystemTable($data, $title, $naChecked = false) {
                    if ($naChecked) return;
                    if (empty($data) || count($data) == 0) return;

                    $items = [];
                    foreach ($data as $key => $value) {
                        if (preg_match('/^[a-z]+_(.+)_(name|total|defective|urgent|remarks|attachment)$/', $key, $matches)) {
                            $itemSlug = $matches[1];
                            $field = $matches[2];
                            $items[$itemSlug][$field] = $value;
                        }
                    }

                    $filtered = [];
                    foreach ($items as $slug => $fields) {
                        foreach ($fields as $f => $v) {
                            if (amcReportMeaningfulItemValue($f, $v)) {
                                $filtered[$slug] = $fields;
                                break;
                            }
                        }
                    }
                    if (count($filtered) === 0) return;

                    echo '<div class="section-header">' . e($title) . '</div>';
                    echo '<table class="system-table"><thead><tr><th>Item</th><th>Total Qty</th><th>Qty Damaged/Defective</th><th>Urgent Action Required</th><th>Remarks</th><th class="attachments-col">Attachments</th></tr></thead><tbody>';

                    foreach ($filtered as $itemSlug => $itemData) {
                        $itemName = $itemData['name'] ?? ucwords(str_replace('_', ' ', $itemSlug));
                        $total = $itemData['total'] ?? '';
                        $defective = $itemData['defective'] ?? '';
                        $urgent = $itemData['urgent'] ?? '';
                        $remarks = $itemData['remarks'] ?? '';
                        $attachments = $itemData['attachment'] ?? [];

                        $urgentDisplay = $urgent === 'yes' ? '✓ Yes' : ($urgent === 'no' ? '✓ No' : '');
                        $defectiveDisplay = is_numeric($defective) ? $defective : ($defective === 'yes' ? '✓ Yes' : ($defective === 'no' ? '✓ No' : ''));

                        echo '<tr><td class="text-left">' . e($itemName) . '</td><td>' . ($total ?: '-') . '</td><td>' . ($defectiveDisplay ?: '-') . '</td><td>' . ($urgentDisplay ?: '-') . '</td><td class="text-left">' . ($remarks ?: '-') . '</td><td class="attachments-col">';
                        if (is_array($attachments) && count($attachments) > 0) {
                            foreach ($attachments as $fp) {
                                $fileUrl = asset('storage/' . $fp);
                                $ext = strtolower(pathinfo($fp, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg','jpeg','png'])) echo "<a href='{$fileUrl}' target='_blank'><img src='{$fileUrl}' width='80' style='margin:4px;border:1px solid #ccc;'></a>";
                                elseif ($ext === 'pdf') echo "<a href='{$fileUrl}' target='_blank' class='btn btn-sm btn-outline-primary me-1'>View PDF</a>";
                                else echo "<a href='{$fileUrl}' download class='btn btn-sm btn-outline-secondary me-1'>Download</a>";
                            }
                        } else { echo '-'; }
                        echo '</td></tr>';
                    }
                    echo '</tbody></table>';
                }

                function renderStatusTable($data, $title, $naChecked = false) {
                    if ($naChecked) return;
                    if (empty($data) || count($data) == 0) return;

                    $items = [];
                    foreach ($data as $key => $value) {
                        if (preg_match('/^[a-z]+_(.+)_(name|status|urgent|remarks|attachment)$/', $key, $matches)) {
                            $itemSlug = $matches[1];
                            $field = $matches[2];
                            $items[$itemSlug][$field] = $value;
                        }
                    }

                    $filtered = [];
                    foreach ($items as $slug => $fields) {
                        foreach ($fields as $f => $v) {
                            if (amcReportMeaningfulItemValue($f, $v)) {
                                $filtered[$slug] = $fields;
                                break;
                            }
                        }
                    }
                    if (count($filtered) === 0) return;

                    echo '<div class="section-header">' . e($title) . '</div>';
                    echo '<table class="system-table"><thead><tr><th>Item</th><th>Status</th><th>Urgent Rectification</th><th>Remarks</th><th class="attachments-col">Attachments</th></tr></thead><tbody>';

                    foreach ($filtered as $itemSlug => $itemData) {
                        $itemName = $itemData['name'] ?? ucwords(str_replace('_', ' ', $itemSlug));
                        $status = $itemData['status'] ?? '';
                        $urgent = $itemData['urgent'] ?? '';
                        $remarks = $itemData['remarks'] ?? '';
                        $attachments = $itemData['attachment'] ?? [];

                        $statusDisplay = $status === 'working' ? '✓ Working' : ($status === 'not_working' ? '✓ Not Working' : '');
                        $urgentDisplay = $urgent === 'yes' ? '✓ Yes' : ($urgent === 'no' ? '✓ No' : '');

                        echo '<tr><td class="text-left">' . e($itemName) . '</td><td>' . ($statusDisplay ?: '-') . '</td><td>' . ($urgentDisplay ?: '-') . '</td><td class="text-left">' . ($remarks ?: '-') . '</td><td class="attachments-col">';
                        if (is_array($attachments) && count($attachments) > 0) {
                            foreach ($attachments as $fp) {
                                $fileUrl = asset('storage/' . $fp);
                                $ext = strtolower(pathinfo($fp, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg','jpeg','png'])) echo "<a href='{$fileUrl}' target='_blank'><img src='{$fileUrl}' width='80' style='margin:4px;border:1px solid #ccc;'></a>";
                                elseif ($ext === 'pdf') echo "<a href='{$fileUrl}' target='_blank' class='btn btn-sm btn-outline-primary me-1'>View PDF</a>";
                                else echo "<a href='{$fileUrl}' download class='btn btn-sm btn-outline-secondary me-1'>Download</a>";
                            }
                        } else { echo '-'; }
                        echo '</td></tr>';
                    }
                    echo '</tbody></table>';
                }
            @endphp

            @php
                $fireAlarmData = is_array($report->fire_alarm_data) ? $report->fire_alarm_data : json_decode($report->fire_alarm_data, true);
                renderSystemTable($fireAlarmData ?? [], 'Fire Alarm System', isset($fireAlarmData['fire_alarm_na']) && $fireAlarmData['fire_alarm_na'] == '1');

                $fireFightingData = is_array($report->fire_fighting_data) ? $report->fire_fighting_data : json_decode($report->fire_fighting_data, true);
                renderSystemTable($fireFightingData ?? [], 'Fire Fighting Equipment', isset($fireFightingData['fire_fighting_na']) && $fireFightingData['fire_fighting_na'] == '1');

                $fm200Data = is_array($report->fm200_data) ? $report->fm200_data : json_decode($report->fm200_data, true);
                renderSystemTable($fm200Data ?? [], 'FM-200 System', isset($fm200Data['fm200_na']) && $fm200Data['fm200_na'] == '1');

                $foamData = is_array($report->foam_tank_data) ? $report->foam_tank_data : json_decode($report->foam_tank_data, true);
                renderSystemTable($foamData ?? [], 'Foam Bladder Tank System', isset($foamData['foam_tank_na']) && $foamData['foam_tank_na'] == '1');

                $voiceData = is_array($report->voice_evacuation_data) ? $report->voice_evacuation_data : json_decode($report->voice_evacuation_data, true);
                renderSystemTable($voiceData ?? [], 'Voice Evacuation System', isset($voiceData['voice_evacuation_na']) && $voiceData['voice_evacuation_na'] == '1');

                $emergencyData = is_array($report->emergency_lighting_data) ? $report->emergency_lighting_data : json_decode($report->emergency_lighting_data, true);
                renderSystemTable($emergencyData ?? [], 'Emergency Lighting', isset($emergencyData['emergency_lighting_na']) && $emergencyData['emergency_lighting_na'] == '1');

                $siData = is_array($report->system_interfacing_data) ? $report->system_interfacing_data : json_decode($report->system_interfacing_data, true);
                renderStatusTable($siData ?? [], 'System Interfacing', isset($siData['system_interfacing_na']) && $siData['system_interfacing_na'] == '1');

                $exitData = is_array($report->exit_route_data) ? $report->exit_route_data : json_decode($report->exit_route_data, true);
                renderStatusTable($exitData ?? [], 'Exit Route and Storage Condition', isset($exitData['exit_route_na']) && $exitData['exit_route_na'] == '1');

                $storageData = is_array($report->storage_conditions_data) ? $report->storage_conditions_data : json_decode($report->storage_conditions_data, true);
                renderStatusTable($storageData ?? [], 'Storage Conditions', isset($storageData['storage_conditions_na']) && $storageData['storage_conditions_na'] == '1');

                $pumpData = is_array($report->pump_data) ? $report->pump_data : json_decode($report->pump_data, true);
                renderSystemTable($pumpData ?? [], 'Pump', isset($pumpData['pump_na']) && $pumpData['pump_na'] == '1');

                $delugeData = is_array($report->deluge_data) ? $report->deluge_data : json_decode($report->deluge_data, true);
                renderSystemTable($delugeData ?? [], 'Deluge Valve & Water Spray System', isset($delugeData['deluge_na']) && $delugeData['deluge_na'] == '1');
            @endphp
        @endif

            @php
                // Used/Required Items are now captured per system-tab. Only fall back to the
                // legacy top-level columns when no system in this report carries its own value.
                $anyBlockUsedItems = false;
                $anyBlockRequiredItems = false;
                foreach ($blockInfo as $blockCheck) {
                    foreach ($blockCheck['systems'] ?? [] as $sysDataCheck) {
                        if (amcReportExtractSpecialField($sysDataCheck, '_used_items') !== null) $anyBlockUsedItems = true;
                        if (amcReportExtractSpecialField($sysDataCheck, '_required_items') !== null) $anyBlockRequiredItems = true;
                    }
                }
            @endphp

            @if(!$anyBlockUsedItems && !empty($report->used_items))
            <!-- Required Maintenance Work -->
            <div class="section-header">Required Maintenance Work</div>
            <div style="margin-top: 4px;" class="row print-no-break">
                <div class="col-md-12">
                    <table>
                        <tr class="row-red">
                            <th style="font-weight: 500;font-size:13px;padding: 6px;">Used Items</th>
                        </tr>
                        <tr>
                            <td>
                                <textarea class="print-shrink-textarea" style="width: 100%; border: none; padding: 8px; font-size: 13px;" cols="40" rows="10" readonly>{{ $report->used_items ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            @if(!$anyBlockRequiredItems && !empty($report->required_items))
            <div class="section-header">Required Items</div>
            <div style="padding: 10px; background-color: #f9f9f9; border-radius: 4px; min-height: 40px;" class="print-no-break">
                <p style="font-size: 13px; margin: 0; white-space: pre-wrap;">{{ $report->required_items }}</p>
            </div>
            @endif

            <!-- Notes / Used Items Section -->
            <div class="section-header">Notes / Used Items</div>
            <div style="padding: 10px; background-color: #f9f9f9; border-radius: 4px; min-height: 60px;">
                <p style="font-size: 13px; margin: 0; white-space: pre-wrap;">{{ $report->notes_used_items ?? 'No notes provided.' }}</p>
            </div>

            <!-- Photos Section (Equipment-specific photos) -->
            <div class="print-hide-attachments">
            <div class="section-header"> Photos (Equipment-specific)</div>
            @php
                $photosData = is_array($report->photos_data) ? $report->photos_data : json_decode($report->photos_data, true);
            @endphp
            
            @if(!empty($photosData) && count($photosData) > 0)
                <table class="system-table">
                    <thead>
                        <tr>
                            <th>Reference (Item/Equipment)</th>
                            <th>Photo Attachments</th>
                            <th>Remarks / Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($photosData as $photo)
                            <tr>
                                <td class="text-left">{{ $photo['reference'] ?? '-' }}</td>
                                <td>
                                    @if(!empty($photo['files']))
                                        <div class="photo-gallery">
                                            @foreach($photo['files'] as $file)
                                                <div class="photo-item">
                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $file) }}" alt="Photo">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color: #999;">No files</span>
                                    @endif
                                </td>
                                <td class="text-left">{{ $photo['label'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="font-size: 13px; color: #999;">No equipment-specific photos attached.</p>
            @endif
            </div>

            <!-- Scope of Inspection Section -->
            <div class="section-header">Scope of Inspection</div>
            <p style="margin-bottom: 1rem; line-height: 1.6; font-size: 13px;">
                All components designated for inspection at the site, standards of practiced person are inspected except as may be noted in the limitations of inspection sections within this report. 
                This inspection is visual only. A representative sample of site components are viewed in areas that are accessible at the time of inspection. 
                No destructive testing or dismantling of site components is performed. Not all improvements will be identified during this inspection. 
                Unexpected repairs should still be anticipated. The inspection should not be considered a guarantee or warranty of any kind.
            </p>

            <div class="row" style="margin-top: 15px;">
                <div class="col-md-6 info-row">
                    <span class="info-label">Client EID Details:</span>
                    <span class="info-value">{{ $report->client_eid_details ?? 'N/A' }}</span>
                </div>
                <div class="col-md-6 info-row">
                    <span class="info-label">Client Phone Number:</span>
                    <span class="info-value">{{ $report->client_phone ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Scope Attachments Section -->
            <div class="print-no-break print-hide-attachments" style="margin-top: 15px;">
                <span class="info-label">Attachments:</span>

                @php
                    // Decode file_path column safely (handles JSON or array)
                    $attachments = is_array($report->file_path)
                        ? $report->file_path
                        : json_decode($report->file_path, true);

                    $attachments = $attachments ?? [];
                @endphp

                @if(!empty($attachments) && count($attachments) > 0)
                    <div class="attachment-box">
                        @foreach($attachments as $index => $path)
                            <div class="attachment-item" style="margin-bottom: 5px;">
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" style="text-decoration: none; color: #007bff;">
                                    📎 Attachment {{ $index + 1 }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="font-size: 13px; color: #999; margin-top: 5px;">
                        No attachments uploaded.
                    </p>
                @endif
            </div>


            <!-- Signature Section -->
            <div class="row print-no-break print-signature-section" style="margin-top: 20px;">
                <div class="col-md-6">
                    <p style="font-weight: 500;font-size:13px;margin-bottom: 8px;">Client Signature:</p>
                    @if($report->client_signature)
                        <img src="{{ $report->client_signature }}" alt="Client Signature" style="border: 1px solid #ccc; border-radius: 4px; max-width: 250px; height: 70px;">
                    @else
                        <p style="font-size: 13px; color: #999;">No signature provided.</p>
                    @endif
                </div>
            </div>

            <!-- Print Button -->
            <div class="row no-print" style="margin-top: 30px; text-align: center;">
                <div class="col-md-12">
                    <button onclick="printFullReport()" class="btn btn-primary" style="padding: 8px 20px; font-size: 14px;">
                        Print Report
                    </button>
                    @if($report->isDraft())
                    <a href="{{ route('projects.drafts') }}" class="btn btn-secondary" style="padding: 8px 20px; font-size: 14px; margin-left: 10px;">
                        Back to My Drafts
                    </a>
                    @else
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary" style="padding: 8px 20px; font-size: 14px; margin-left: 10px;">
                        Back to Projects
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @section('css')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            /* Override Bootstrap 5 default @page size for this report */
            @media print {
                @page {
                    size: A4 portrait;
                    margin: 10mm 12mm;
                }
                body {
                    min-width: 0 !important;
                }
            }
        </style>
    @endsection

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Block tab switching
        document.querySelectorAll('.block-tab-button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var blockId = this.getAttribute('data-block-tab');

                // Deactivate all block tabs and contents
                document.querySelectorAll('.block-tab-button').forEach(function(b) { b.classList.remove('active'); });
                document.querySelectorAll('.block-tab-content').forEach(function(c) { c.classList.remove('active'); });

                // Activate selected
                this.classList.add('active');
                var content = document.getElementById('view-' + blockId);
                if (content) content.classList.add('active');
            });
        });

        // System tab switching (scoped to parent block)
        document.querySelectorAll('.system-tab-button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var sysTabId = this.getAttribute('data-system-tab');
                var blockContent = this.closest('.block-tab-content');

                if (blockContent) {
                    // Deactivate all system tabs within this block only
                    blockContent.querySelectorAll('.system-tab-button').forEach(function(b) { b.classList.remove('active'); });
                    blockContent.querySelectorAll('.system-tab-content').forEach(function(c) { c.classList.remove('active'); });

                    // Activate selected
                    this.classList.add('active');
                    var sysContent = document.getElementById(sysTabId);
                    if (sysContent) sysContent.classList.add('active');
                }
            });
        });
    });

    function printFullReport() {
        // Print layout is handled by @media print CSS (all tab panes, headers, spacing).
        window.print();
    }
    </script>
@endsection