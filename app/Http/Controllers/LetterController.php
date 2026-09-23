<?php
namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use App\Models\Letter;
use App\Models\StafProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DataTables\LetterDataTable;
use Illuminate\Support\Facades\DB;

class LetterController extends Controller
{
    protected $whatsAppService;
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(LetterDataTable $dataTable)
    {
        return $dataTable->render('letters.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stafProfiles = StafProfile::all();
        return view('letters.create', compact('stafProfiles'));
    }

    /**
     * Store a newly created letter for multiple staff members.
     */
public function store(Request $request)
{
    $request->validate([
        'staff_profile_ids' => 'required|array|min:1',
        'staff_profile_ids.*' => 'exists:staf_profile,id',
        'type' => 'required|in:warning,appreciation,general_notice,poor_performance_notice,accommodation_notice,vehicle_notice,attendance_notice,weather_notice,eid_holidays_notice',
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'issued_by' => 'required|string|max:255',
        'issued_at' => 'required|date',
        'ref_no' => 'nullable|string|max:255',
        'days_deduct' => 'nullable|integer|min:1|max:365',
        'file' => 'nullable|file|max:10240',
    ]);

    // Log request without file to avoid temporary file issues
    Log::info('Letter Store Request (Multiple Users)', [
        'staff_profile_ids' => $request->staff_profile_ids,
        'type' => $request->type,
        'title' => $request->title,
    ]);

    $staffProfileIds = $request->staff_profile_ids;
    $createdLetters = [];
    $whatsappResults = [];

    // Uploaded file info
    $uploadedFile = $request->file('file');
    $originalFilePath = $uploadedFile ? $uploadedFile->getPathname() : null;
    $originalFileName = $uploadedFile ? $uploadedFile->getClientOriginalName() : null;

    DB::beginTransaction();

    try {
        foreach ($staffProfileIds as $index => $staffProfileId) {
            // Generate unique ref_no
            $refNo = $request->ref_no;
            if (empty($refNo)) {
                $datePart = now()->format('Ymd');
                $count = Letter::whereDate('created_at', now()->toDateString())->count() + 1;
                $refNo = sprintf('LET-%s-%04d', $datePart, $count);
            } else {
                $refNo = $index === 0 ? $refNo : $refNo . '-' . ($index + 1);
            }

            // Create letter without triggering UploadFile trait
            $letter = new Letter([
                'staff_profile_id' => $staffProfileId,
                'type' => $request->type,
                'title' => $request->title,
                'content' => $request->content,
                'issued_by' => $request->issued_by,
                'issued_at' => $request->issued_at,
                'ref_no' => $refNo,
                'days_deduct' => $request->input('days_deduct'),
            ]);

            Letter::withoutEvents(function () use ($letter) {
                $letter->save();
            });

            // Add the uploaded file to the letter (same file for all)
            if ($uploadedFile && file_exists($originalFilePath)) {
                $letter->addMedia($originalFilePath)
                       ->usingFileName($originalFileName)
                       ->toMediaCollection();
            }

            Log::info('Letter Created', [
                'id' => $letter->id,
                'ref_no' => $letter->ref_no,
                'staff_id' => $staffProfileId
            ]);

            $createdLetters[] = $letter;
        }

        DB::commit();

        // Send WhatsApp messages AFTER transaction is committed
        // This ensures database isn't affected by WhatsApp API failures
        foreach ($createdLetters as $index => $letter) {
            if ($letter->type === 'warning') {
                $staffProfile = $letter->stafProfile;

                if ($staffProfile && $staffProfile->mobile_no) {
                    $mobile = trim($staffProfile->mobile_no);
                    
                    // Validate mobile number format (at least 7 digits)
                    if (!preg_match('/\d{7,}/', $mobile)) {
                        Log::warning('Staff has invalid mobile number format for letter', [
                            'letter_id' => $letter->id,
                            'staff_id' => $staffProfile->id,
                            'staff_name' => $staffProfile->name . ' ' . $staffProfile->last_name,
                            'mobile_no' => $mobile
                        ]);

                        $whatsappResults[] = [
                            'staff' => $staffProfile->name . ' ' . $staffProfile->last_name,
                            'status' => 'invalid_mobile',
                            'error' => 'Invalid mobile number format'
                        ];
                        continue;
                    }
                    
                    try {
                        // Add delay between messages
                        // FREE TRIAL: 1 msg/minute = 60 seconds delay
                        // PAID PLAN: 1 msg/5 sec = 10 seconds delay (or even lower with Account Protection)
                        // Change the sleep value below based on your plan
                        $delaySeconds = 60; // ← CHANGE THIS TO 10 WHEN ON PAID PLAN
                        
                        if ($index > 0) {
                            sleep($delaySeconds);
                        }

                        Log::info('Attempting to send WhatsApp', [
                            'letter_id' => $letter->id,
                            'staff_id' => $staffProfile->id,
                            'mobile' => $mobile
                        ]);

                        $this->whatsAppService->sendMessage(
                            $mobile,
                            $letter->title,
                            $letter->content,
                            $letter->issued_by,
                            $letter->issued_at,
                            $letter
                        );

                        $whatsappResults[] = [
                            'staff' => $staffProfile->name . ' ' . $staffProfile->last_name,
                            'status' => 'success'
                        ];

                        Log::info('WhatsApp message sent successfully', [
                            'letter_id' => $letter->id,
                            'staff_id' => $staffProfile->id,
                            'staff_name' => $staffProfile->name . ' ' . $staffProfile->last_name
                        ]);
                    } catch (\Exception $e) {
                        Log::error('WhatsApp sending failed', [
                            'letter_id' => $letter->id,
                            'staff_id' => $staffProfile->id,
                            'mobile' => $staffProfile->mobile_no,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        $whatsappResults[] = [
                            'staff' => $staffProfile->name . ' ' . $staffProfile->last_name,
                            'status' => 'failed',
                            'error' => $e->getMessage()
                        ];

                        // Don't throw - continue to next staff member
                    }
                } else {
                    Log::warning('Staff profile or mobile missing', [
                        'letter_id' => $letter->id,
                        'staff_profile_id' => $letter->staff_profile_id,
                        'has_profile' => $staffProfile ? true : false,
                        'has_mobile' => $staffProfile && $staffProfile->mobile_no ? true : false
                    ]);

                    $whatsappResults[] = [
                        'staff' => $staffProfile ? $staffProfile->name . ' ' . $staffProfile->last_name : 'Unknown',
                        'status' => 'no_mobile'
                    ];
                }
            }
        }

        // Prepare success message
        $successMessage = count($createdLetters) . ' letter(s) created successfully.';
        if (!empty($whatsappResults)) {
            $successCount = count(array_filter($whatsappResults, fn($r) => $r['status'] === 'success'));
            $failedCount = count(array_filter($whatsappResults, fn($r) => $r['status'] === 'failed'));
            $noMobileCount = count(array_filter($whatsappResults, fn($r) => $r['status'] === 'no_mobile'));
            $invalidMobileCount = count(array_filter($whatsappResults, fn($r) => $r['status'] === 'invalid_mobile'));

            if ($successCount > 0) $successMessage .= " WhatsApp sent to {$successCount} staff member(s).";
            if ($failedCount > 0) $successMessage .= " Failed to send WhatsApp to {$failedCount} staff member(s).";
            if ($noMobileCount > 0) $successMessage .= " {$noMobileCount} staff member(s) have no mobile number.";
            if ($invalidMobileCount > 0) $successMessage .= " {$invalidMobileCount} staff member(s) have invalid mobile numbers.";
        }

        return redirect()->route('letters.index')->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Letter Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return redirect()->route('letters.create')->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
    }
}






    /**
     * Display the specified resource.
     */
    public function show(Letter $letter)
    {
        return view('letters.show', compact('letter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Letter $letter)
    {
        $stafProfiles = StafProfile::all();
        return view('letters.edit', compact('letter', 'stafProfiles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Letter $letter)
    {
        $request->validate([
            'staff_profile_id' => 'required|exists:staf_profile,id',
            'type' => 'required|in:warning,appreciation,general_notice,poor_performance_notice,accommodation_notice,vehicle_notice,attendance_notice,weather_notice,eid_holidays_notice',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'issued_by' => 'required|string|max:255',
            'issued_at' => 'required|date',
            'ref_no' => 'nullable|string|max:255|unique:letters,ref_no,' . $letter->id,
            'days_deduct' => 'nullable|integer|min:1|max:365',
        ]);

        $letter->update($request->only([
            'staff_profile_id',
            'type',
            'title',
            'content',
            'issued_by',
            'issued_at',
            'ref_no',
            'days_deduct',
        ]));

        return redirect()->route('letters.index')->with('success', 'Letter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Letter $letter)
    {
        $letter->delete();
        return redirect()->route('letters.index')->with('success', 'Letter deleted successfully.');
    }
}