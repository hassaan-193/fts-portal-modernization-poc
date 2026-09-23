<?php
namespace App\Http\Controllers;

use Flash;
use Response;
use App\Models\Company;
use App\Models\Project;
use App\Models\VisitSchedule;
use App\Models\ProjectReport;
use App\Models\AmcReportSystemItem;
use App\DataTables\AmcReportDraftDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectReportController extends Controller
{
    public function showForm()
    {
        $this->authorizeAmcReportAccess();
        $companies = Company::all();
        $extraSystemItems = $this->getExtraSystemItems();
        $draftCount = ProjectReport::where('status', ProjectReport::STATUS_DRAFT)
            ->where('created_by', auth()->id())
            ->count();
        return view('projects.form', compact('companies', 'extraSystemItems', 'draftCount'));
    }

    /**
     * Extra system items previously added by users via "Add New Row",
     * now treated as defaults for every future form. Grouped by system key (fa, ff, ...).
     */
    private function getExtraSystemItems()
    {
        return AmcReportSystemItem::all()
            ->groupBy('system_key')
            ->map(function ($group) {
                return $group->pluck('item_label')->values();
            });
    }

    public function generateReferenceNumber()
    {
        // Get the count of reports for today
        $today = date('Y-m-d');
        $count = ProjectReport::whereDate('created_at', $today)->count() + 1;
        
        // Format: AMC-NNN-YYMMDDHHmmss
        $refNumber = 'AMC-' . str_pad($count, 3, '0', STR_PAD_LEFT) . '-' . date('YmdHis');
        
        return response()->json(['reference_number' => $refNumber]);
    }

    public function getClients(Request $request)
    {
        $request->validate([
            'amc_type' => 'required|in:existing,new'
        ]);
    
        $amcType = $request->amc_type;
        
        if ($amcType === 'existing') {
            // Get clients that have AMC projects
            $companies = Company::whereHas('quotations', function ($query) {
                $query->whereHas('projects', function ($subQuery) {
                    $subQuery->where('category', 'amc');
                });
            })->get(['id', 'name']);
        } else {
            // Get clients that DON'T have AMC projects
            $companies = Company::whereDoesntHave('quotations', function ($query) {
                $query->whereHas('projects', function ($subQuery) {
                    $subQuery->where('category', 'amc');
                });
            })->get(['id', 'name']);
        }
        
        return response()->json($companies);
    }

    public function getProjects(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id'
        ]);
    
        $projects = Project::whereHas('quotation', function ($query) use ($request) {
            $query->where('company_id', $request->company_id);
        })
        ->where('category', 'amc')
        ->get();
        if ($projects->isEmpty()) {
            return response()->json(['error' => 'No AMC projects found for this company'], 404);
        }
        return response()->json($projects);
    }
    
    public function getVisitSchedules(Request $request)
    {
        $projectId = $request->input('project_id');
        $visitSchedules = VisitSchedule::where('project_id', $projectId)
            ->whereIn('status', ['pending', 'upcoming'])
            ->get(['id', 'visit_date', 'status']);
        return response()->json($visitSchedules);
    }

    public function store(Request $request)
    {
        $this->authorizeAmcReportAccess();

        // "Save as Draft" keeps the report with its author to finish later; it only
        // reaches the report-status page once submitted.
        $isDraft = $request->has('save_draft');

        $amcType = $request->input('amc_type', 'existing');
        $isManualClient = $amcType === 'new' && $request->boolean('is_manual_client');
        $isEmergencyVisit = $amcType === 'existing' && $request->boolean('is_emergency_visit');

        // Conditional validation based on AMC type
        $rules = [
            'amc_type' => 'required|in:existing,new',
            'date' => 'required|date',
            'inspector_visiting_time' => 'nullable|date_format:H:i',
            'inspector_leaving_time' => 'nullable|date_format:H:i',
            'site_location' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'scope_attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ];

        // Client: either a real company, or (for new AMC only) a manually typed name
        if ($isManualClient) {
            $rules['manual_client_name'] = 'required|string|max:255';
            $rules['company_id'] = 'nullable|exists:companies,id';
        } else {
            $rules['company_id'] = 'required|exists:companies,id';
        }

        // If existing AMC, project_id is required; visit_schedule_id stays optional,
        // but an emergency visit requires its own date instead
        if ($amcType === 'existing') {
            $rules['project_id'] = 'required|exists:projects,id';
            $rules['visit_schedule_id'] = 'nullable|exists:visit_schedules,id';
            $rules['emergency_visit_date'] = $isEmergencyVisit ? 'required|date' : 'nullable|date';
        } else {
            // If new, they are not needed
            $rules['project_id'] = 'nullable|exists:projects,id';
            $rules['visit_schedule_id'] = 'nullable|exists:visit_schedules,id';
            $rules['emergency_visit_date'] = 'nullable|date';
        }

        $request->validate($rules);

        // Process blocks data and organize by block
        $blockInfo = $this->processBlockData($request);

        // Step 4: Create the report
        $report = new ProjectReport();
        $report->company_id = $isManualClient ? null : $request->company_id;
        $report->is_manual_client = $isManualClient;
        $report->manual_client_name = $isManualClient ? $request->manual_client_name : null;
        $report->project_id = $amcType === 'existing' ? $request->project_id : null;
        $report->visit_schedule_id = ($amcType === 'existing' && !$isEmergencyVisit) ? $request->visit_schedule_id : null;
        $report->is_emergency_visit = $isEmergencyVisit;
        $report->emergency_visit_date = $isEmergencyVisit ? $request->emergency_visit_date : null;
        $report->amc_type = $amcType;
        $report->reference_number = $request->input('reference_number');
        $report->date = $request->input('date');
        $report->inspector_visiting_time = $request->input('inspector_visiting_time') ?? null;
        $report->inspector_leaving_time = $request->input('inspector_leaving_time') ?? null;
        $report->site_location = $request->input('site_location');
        $report->site_name = $request->input('site_name');
        $report->used_items = $request->input('Used_Items');
        $report->required_items = $request->input('Required_Items');
        $report->notes_used_items = $request->input('notes_used_items');
        $report->next_inspection_due = $request->input('next_inspection_due');
        $report->expiry_update_required_on = $request->input('expiry_update_required_on');
        $report->client_eid_details = $request->input('client_eid_details');
        $report->client_phone = $request->input('client_phone');
        $report->client_signature = $request->input('client_signature');

        // Step 5: Store block info
        $report->block_info = $blockInfo;

        // Step 6: Urgent summary
        if ($request->urgent_issue) {
            $urgentSummary = [];
            foreach ($request->urgent_issue as $i => $issue) {
                if ($issue) {
                    $urgentSummary[] = [
                        'issue' => $issue,
                        'location' => $request->urgent_location[$i] ?? null,
                        'action' => $request->urgent_action[$i] ?? null,
                        'deadline' => $request->urgent_deadline[$i] ?? null,
                    ];
                }
            }
            $report->urgent_summary = $urgentSummary;
        }

        // Step 7: Photo attachments
        if ($request->photo_reference) {
            $photos = [];
            foreach ($request->photo_reference as $i => $ref) {
                $files = [];
                if ($request->hasFile("photo_files.$i")) {
                    foreach ($request->file("photo_files.$i") as $file) {
                        $files[] = $file->store('report_photos', 'public');
                    }
                }
                $photos[] = [
                    'reference' => $ref,
                    'files' => $files,
                    'label' => $request->photo_label[$i] ?? null,
                ];
            }
            $report->photos_data = $photos;
        }

        // Step 8: Scope attachments
        if ($request->hasFile('scope_attachment')) {
            $filePaths = [];
            foreach ($request->file('scope_attachment') as $file) {
                $filePaths[] = $file->store('scope_attachments', 'public');
            }
            $report->file_path = $filePaths;
        }

        $report->status = $isDraft ? ProjectReport::STATUS_DRAFT : 'pending';
        $report->created_by = auth()->id();
        $report->save();

        if ($isDraft) {
            Flash::success('Draft saved. You can continue it any time from My AMC Drafts.');

            return redirect()->route('projects.editReport', $report->id);
        }

        Flash::success('Project Report submitted successfully.');

        return redirect()->back();

    }

    /**
     * Authorize: allow users with 'projects' OR 'amc_report' permission.
     */
    private function authorizeAmcReportAccess()
    {
        $user = auth()->user();
        if ($user && ($user->can('projects') || $user->can('amc_report'))) {
            return;
        }
        abort(403, 'Unauthorized.');
    }

    /**
     * Authorize the report-status side (review, edit, approve submitted reports):
     * allow users with 'projects' OR 'approve_amc_reports' permission.
     */
    private function authorizeReportApprovalAccess()
    {
        $user = auth()->user();
        if ($user && ($user->can('projects') || $user->can('approve_amc_reports'))) {
            return;
        }
        abort(403, 'Unauthorized.');
    }

    /**
     * Load a report for view/edit/update. A draft is private to the user who started it;
     * a submitted report is open to the report-status reviewers.
     */
    private function findAccessibleReport($id, array $with = [])
    {
        $report = ProjectReport::with($with)->findOrFail($id);

        if ($report->isDraft()) {
            $this->authorizeAmcReportAccess();
            if ((int) $report->created_by !== (int) auth()->id()) {
                abort(404);
            }
        } else {
            $this->authorizeReportApprovalAccess();
        }

        return $report;
    }

    /**
     * Process block data from the request (text fields + file uploads).
     * Merges newly uploaded files with existing attachment paths when provided.
     */
    private function processBlockData(Request $request, array $existingBlockInfo = []): array
    {
        $blockInfo = [];
        $blocks = $request->input('blocks', []);
        $blockFiles = $request->file('blocks') ?? [];

        foreach ($blocks as $blockNum => $blockData) {
            $blockName = $blockData['name'] ?? "Block {$blockNum}";
            $blockId = $blockData['id'] ?? "block-{$blockNum}";

            $blockInfo[$blockId] = [
                'name' => $blockName,
                'id' => $blockId,
                'systems' => [],
            ];

            // Preserve existing system attachment paths for this block (edit scenario)
            $existingSystemAttachments = [];
            foreach ($existingBlockInfo as $existingId => $existingBlock) {
                if ($existingId === $blockId || ($existingBlock['id'] ?? null) === $blockId) {
                    foreach ($existingBlock['systems'] ?? [] as $sysName => $sysData) {
                        foreach ($sysData as $k => $v) {
                            if (Str::endsWith($k, '_attachment') && is_array($v)) {
                                $existingSystemAttachments[$sysName][$k] = $v;
                            }
                        }
                    }
                }
            }

            // Ad-hoc rows added via "Add New Row" arrive under a temporary "{prefix}_newitemN" token
            // with an explicit "_name" field. Rename them to the real slugified item name (so they land
            // in block_info in the exact same shape as a hardcoded default item) and, for the 11 fixed
            // systems only, persist the item as a default for all future forms.
            $renameMap = [];
            foreach ($blockData as $key => $value) {
                if (!Str::endsWith($key, '_name') || $value === null || $value === '') {
                    continue;
                }
                $token = Str::beforeLast($key, '_name');
                $resolved = $this->resolveSystemBucket($token);
                if (!$resolved) {
                    continue;
                }
                $slug = $this->slugifyItemName($value);
                if ($slug === '') {
                    continue;
                }
                $newToken = $resolved['prefix'] . '_' . $slug;
                $renameMap[$token] = $newToken;

                if (!$resolved['is_custom']) {
                    AmcReportSystemItem::firstOrCreate(
                        ['system_key' => $resolved['prefix'], 'item_slug' => $slug],
                        ['item_label' => $value]
                    );
                }
            }

            if (!empty($renameMap)) {
                $renamedBlockData = [];
                foreach ($blockData as $key => $value) {
                    $renamedBlockData[$this->applyRename($key, $renameMap)] = $value;
                }
                foreach (array_values($renameMap) as $newToken) {
                    unset($renamedBlockData[$newToken . '_name']);
                }
                $blockData = $renamedBlockData;

                if (isset($blockFiles[$blockNum])) {
                    $renamedFiles = [];
                    foreach ($blockFiles[$blockNum] as $fileKey => $fileValue) {
                        $renamedFiles[$this->applyRename($fileKey, $renameMap)] = $fileValue;
                    }
                    $blockFiles[$blockNum] = $renamedFiles;
                }
            }

            // Extract text data (fixed systems + dynamic custom-tab systems)
            foreach ($blockData as $key => $value) {
                if (in_array($key, ['name', 'id', 'custom_tabs', 'used_items', 'required_items'])) {
                    continue;
                }
                $resolved = $this->resolveSystemBucket($key);
                if ($resolved) {
                    $bucket = $resolved['bucket'];
                    if (!isset($blockInfo[$blockId]['systems'][$bucket])) {
                        $blockInfo[$blockId]['systems'][$bucket] = [];
                    }
                    $blockInfo[$blockId]['systems'][$bucket][$key] = $value;
                }
            }

            // Restore preserved existing attachments (only if no new upload for that key)
            foreach ($existingSystemAttachments as $sysName => $attachments) {
                if (!isset($blockInfo[$blockId]['systems'][$sysName])) {
                    $blockInfo[$blockId]['systems'][$sysName] = [];
                }
                foreach ($attachments as $attachKey => $paths) {
                    if (!isset($blockInfo[$blockId]['systems'][$sysName][$attachKey])) {
                        $blockInfo[$blockId]['systems'][$sysName][$attachKey] = $paths;
                    }
                }
            }

            // Process new file uploads
            $blockFileData = $blockFiles[$blockNum] ?? [];
            foreach ($blockFileData as $fileKey => $fileValue) {
                if (!Str::endsWith($fileKey, '_attachment')) {
                    continue;
                }
                $storedFiles = [];
                $fileList = is_array($fileValue) ? $fileValue : [$fileValue];
                foreach ($fileList as $file) {
                    if ($file && $file->isValid()) {
                        $storedFiles[] = $file->store('block_attachments', 'public');
                    }
                }
                if (!empty($storedFiles)) {
                    $resolved = $this->resolveSystemBucket($fileKey);
                    if ($resolved) {
                        $bucket = $resolved['bucket'];
                        if (!isset($blockInfo[$blockId]['systems'][$bucket])) {
                            $blockInfo[$blockId]['systems'][$bucket] = [];
                        }
                        // New upload replaces existing
                        $blockInfo[$blockId]['systems'][$bucket][$fileKey] = $storedFiles;
                    }
                }
            }

            // Block-level metadata: custom tabs (task 5). Used/Required Items (task 6) are
            // per-system-tab now, submitted as "{prefix}_used_items"/"{prefix}_required_items"
            // and already swept into their system bucket by the bucketing loop above.
            $blockInfo[$blockId]['custom_tabs'] = $blockData['custom_tabs'] ?? [];
        }

        return $blockInfo;
    }

    /**
     * Resolve which system bucket a block field key belongs to: one of the 11 fixed
     * systems (fa_, ff_, ...) or a dynamic per-block custom tab (custom1_, custom2_, ...).
     * Returns null for unrecognized keys.
     */
    private function resolveSystemBucket(string $key): ?array
    {
        $systemPrefixes = ['fa_', 'ff_', 'fm200_', 'foam_', 'voice_', 'emergency_', 'si_', 'exit_', 'storage_', 'pump_', 'deluge_'];

        foreach ($systemPrefixes as $prefix) {
            if (Str::startsWith($key, $prefix)) {
                return [
                    'bucket' => $this->getSystemName($prefix),
                    'prefix' => rtrim($prefix, '_'),
                    'is_custom' => false,
                ];
            }
        }

        if (preg_match('/^(custom\d+)_/', $key, $matches)) {
            return [
                'bucket' => $matches[1],
                'prefix' => $matches[1],
                'is_custom' => true,
            ];
        }

        return null;
    }

    /**
     * Rewrite a field key from its temporary ad-hoc-row token to the renamed token,
     * e.g. "fa_newitem3_total" -> "fa_sprinkler_test_total".
     */
    private function applyRename(string $key, array $renameMap): string
    {
        foreach ($renameMap as $oldToken => $newToken) {
            if (Str::startsWith($key, $oldToken . '_')) {
                return $newToken . '_' . Str::after($key, $oldToken . '_');
            }
        }
        return $key;
    }

    /**
     * Slugify an item name exactly the way the client-side JS does, so ad-hoc rows
     * land under the same key an equivalent hardcoded default item would use.
     */
    private function slugifyItemName(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/\s+/', '_', $slug);
        $slug = preg_replace('/[^\w_]/', '', $slug);
        return $slug;
    }

    /**
     * Get the display name for a system prefix
     */
    private function getSystemName($prefix)
    {
        $systemNames = [
            'fa_' => 'fire_alarm',
            'ff_' => 'fire_fighting',
            'fm200_' => 'fm200',
            'foam_' => 'foam_tank',
            'voice_' => 'voice_evacuation',
            'emergency_' => 'emergency_lighting',
            'si_' => 'system_interfacing',
            'exit_' => 'exit_route',
            'storage_' => 'storage_conditions',
            'pump_' => 'pump',
            'deluge_' => 'deluge',
        ];
        return $systemNames[$prefix] ?? $prefix;
    }

    /**
     * Process attachments for a specific section
     */
    private function processAttachments(Request $request, $prefix)
    {
        $attachments = [];
        
        foreach ($request->allFiles() as $key => $files) {
            // Check if this file belongs to this section
            if (Str::startsWith($key, $prefix) && Str::endsWith($key, '_attachment')) {
                $storedFiles = [];
                
                // Handle array of files
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if ($file->isValid()) {
                            $storedFiles[] = $file->store('section_attachments/' . trim($prefix, '_'), 'public');
                        }
                    }
                }
                
                // Store the file paths with the key
                if (!empty($storedFiles)) {
                    $attachments[$key] = $storedFiles;
                }
            }
        }

        // Also process dynamically added rows attachments
        $tableId = $this->getSectionTableId($prefix);
        if ($tableId) {
            foreach ($request->allFiles() as $key => $files) {
                if (Str::startsWith($key, $tableId . '_new_item_attachment')) {
                    $storedFiles = [];
                    
                    if (is_array($files)) {
                        foreach ($files as $fileArray) {
                            if (is_array($fileArray)) {
                                foreach ($fileArray as $file) {
                                    if ($file->isValid()) {
                                        $storedFiles[] = $file->store('section_attachments/' . $tableId, 'public');
                                    }
                                }
                            } elseif ($fileArray->isValid()) {
                                $storedFiles[] = $fileArray->store('section_attachments/' . $tableId, 'public');
                            }
                        }
                    }
                    
                    if (!empty($storedFiles)) {
                        $attachments[$key] = $storedFiles;
                    }
                }
            }
        }

        return $attachments;
    }

    /**
     * Get table ID from section prefix
     */
    private function getSectionTableId($prefix)
    {
        $mapping = [
            'fa_' => 'fire-alarm-table',
            'ff_' => 'fire-fighting-table',
            'fm200_' => 'fm200-table',
            'foam_' => 'foam-tank-table',
            'voice_' => 'voice-evacuation-table',
            'emergency_' => 'emergency-lighting-table',
            'si_' => 'system-interfacing-table',
            'exit_' => 'exit-route-table',
            'storage_' => 'storage-conditions-table',
            'pump_' => 'pump-table',
            'deluge_' => 'deluge-table',
        ];

        return $mapping[$prefix] ?? null;
    }

    public function showReportStatusPage(Request $request)
    {
        $this->authorizeReportApprovalAccess();
        $companies = Company::all();
        // Drafts are still being filled in by their author and are not up for review yet
        $reportsQuery = ProjectReport::with(['visitSchedule', 'company', 'project'])
            ->where('status', '!=', ProjectReport::STATUS_DRAFT);
    
        $status = $request->input('status', 'pending');
        if ($status !== 'all') {
            $reportsQuery->where('status', $status);
        }
    
        if ($request->filled('company_id')) {
            $reportsQuery->where('company_id', $request->company_id);
        }
    
        if ($request->filled('project_id')) {
            $reportsQuery->where('project_id', $request->project_id);
        }
    
        $reports = $reportsQuery->get();
    
        return view('projects.project-report-status', [
            'companies' => $companies,
            'reports' => $reports,
            'selectedCompanyId' => $request->company_id,
            'selectedProjectId' => $request->project_id,
            'selectedStatus' => $status,
        ]);
    }
    
    public function getProjectsByCompany(Request $request)
    {
        $companyId = $request->get('company_id');
        if (!$companyId) {
            return response()->json(['error' => 'Company ID is required'], 400);
        }
        
        $projects = Project::whereHas('quotation', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->get();
        
        if ($projects->isEmpty()) {
            return response()->json(['error' => 'No projects found for this company.'], 404);
        }
        
        return response()->json($projects);
    }
    
    public function updateReportStatus(Request $request, $id)
    {
        $this->authorizeReportApprovalAccess();
        $request->validate(['status' => 'required|in:pending,approved,disapproved']);

        $report = ProjectReport::where('status', '!=', ProjectReport::STATUS_DRAFT)->findOrFail($id);
        $report->status = $request->status;
        $report->save();
    
        Flash::success('Project Report status updated successfully.');

        return redirect()->back();    }

    public function viewReport($reportId)
    {
        $report = $this->findAccessibleReport($reportId, ['company', 'project', 'visitSchedule']);

        return view('projects.viewReport', compact('report'));
    }

    public function editReport($id)
    {
        $report = $this->findAccessibleReport($id, ['company', 'project', 'visitSchedule']);
        $extraSystemItems = $this->getExtraSystemItems();
        return view('projects.edit-report', compact('report', 'extraSystemItems'));
    }


    public function updateReport(Request $request, $id)
    {
        $report = $this->findAccessibleReport($id);

        $rules = [
            'date' => 'required|date',
            'inspector_visiting_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'inspector_leaving_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'site_name' => 'nullable|string|max:255',
            'site_location' => 'nullable|string|max:255',
            'notes_used_items' => 'nullable|string',
            'client_eid_details' => 'nullable|string|max:255',
            'client_phone' => 'nullable|string|max:255',
            'scope_attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ];

        // Emergency visit date is only editable/required when the report was created as an emergency visit
        $rules['emergency_visit_date'] = $report->is_emergency_visit ? 'required|date' : 'nullable|date';

        $request->validate($rules);

        // Process blocks (merge new uploads with existing preserved attachments)
        $blockInfo = $this->processBlockData($request, $report->block_info ?? []);

        $report->date = $request->input('date');
        $report->inspector_visiting_time = $request->input('inspector_visiting_time') ?? null;
        $report->inspector_leaving_time = $request->input('inspector_leaving_time') ?? null;
        $report->site_name = $request->input('site_name');
        $report->site_location = $request->input('site_location');
        $report->notes_used_items = $request->input('notes_used_items');
        $report->client_eid_details = $request->input('client_eid_details');
        $report->client_phone = $request->input('client_phone');
        $report->block_info = $blockInfo;

        if ($report->is_emergency_visit) {
            $report->emergency_visit_date = $request->input('emergency_visit_date');
        }

        // Update signature if a new one was submitted
        if ($request->filled('client_signature')) {
            $report->client_signature = $request->input('client_signature');
        }

        // Update photos if submitted
        if ($request->photo_reference) {
            $photos = [];
            foreach ($request->photo_reference as $i => $ref) {
                $files = [];
                if ($request->hasFile("photo_files.$i")) {
                    foreach ($request->file("photo_files.$i") as $file) {
                        $files[] = $file->store('report_photos', 'public');
                    }
                } else {
                    // Preserve existing photo files
                    $existingFiles = $request->input("existing_photo_files.$i", []);
                    $files = is_array($existingFiles) ? $existingFiles : [];
                }
                $photos[] = [
                    'reference' => $ref,
                    'files' => $files,
                    'label' => $request->photo_label[$i] ?? null,
                ];
            }
            $report->photos_data = $photos;
        }

        // Update scope attachments
        if ($request->hasFile('scope_attachment')) {
            $filePaths = [];
            foreach ($request->file('scope_attachment') as $file) {
                $filePaths[] = $file->store('scope_attachments', 'public');
            }
            $report->file_path = $filePaths;
        } elseif ($request->has('existing_scope_attachments')) {
            $existing = $request->input('existing_scope_attachments', []);
            if (is_array($existing) && count($existing) > 0) {
                $report->file_path = $existing;
            }
        }

        // On a draft, "Save Draft" keeps it with its author; "Submit Report" sends it for approval
        $submitDraft = $report->isDraft() && !$request->has('save_draft');
        if ($submitDraft) {
            $report->status = 'pending';
        }

        $report->save();

        if ($submitDraft) {
            Flash::success('Project Report submitted successfully.');

            return redirect()->route('projects.drafts');
        }

        Flash::success($report->isDraft() ? 'Draft saved successfully.' : 'Project Report updated successfully.');

        return redirect()->back();
    }

    /**
     * The signed-in user's AMC reports that are saved as draft and not yet submitted.
     */
    public function drafts(AmcReportDraftDataTable $dataTable)
    {
        $this->authorizeAmcReportAccess();

        return $dataTable->render('projects.drafts');
    }

    /**
     * Discard one of the signed-in user's own drafts. Submitted reports cannot be deleted here.
     */
    public function destroyDraft($id)
    {
        $this->authorizeAmcReportAccess();

        $report = ProjectReport::where('status', ProjectReport::STATUS_DRAFT)
            ->where('created_by', auth()->id())
            ->findOrFail($id);
        $report->delete();

        Flash::success('Draft deleted successfully.');

        return redirect()->route('projects.drafts');
    }
}
