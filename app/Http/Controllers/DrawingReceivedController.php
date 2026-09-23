<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateDrawingReceivedRequest;
use App\Http\Requests\UpdateDrawingReceivedRequest;
use App\Repositories\DrawingReceivedRepository;
use App\DataTables\DrawingReceivedDataTable;
use App\DataTables\EngineerDrawingDataTable;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Models\DrawingReceived;
use App\Models\Lpoin;
use App\User;
use Illuminate\Http\Request;

class DrawingReceivedController extends AppBaseController
{
    /** @var DrawingReceivedRepository */
    private $drawingReceivedRepository;

    public function __construct(DrawingReceivedRepository $drawingReceivedRepo)
    {
        $this->drawingReceivedRepository = $drawingReceivedRepo;
    }

    /**
     * Display a listing of the DrawingReceived.
     *
     * @param DrawingReceivedDataTable $dataTable
     * @return Response
     */
    public function index(DrawingReceivedDataTable $dataTable)
    {
        return $dataTable->render('drawing-receiveds.index');
    }

    /**
     * Show the form for creating a new DrawingReceived.
     *
     * @return Response
     */
    public function create()
    {
        // Get all lpoins
        $lpoins = Lpoin::with('quotation')->get()->pluck('ref_no', 'id');
        
        // Get all users (engineers)
        $engineers = User::pluck('name', 'id');
        
        // Get enum values for type_of_work
        $typeOfWork = config('enum.drawing_type_of_work');
        
        // Get enum values for status
        $statusOptions = config('enum.drawing_status');

        return view('drawing-receiveds.create', compact(
            'lpoins',
            'engineers',
            'typeOfWork',
            'statusOptions'
        ));
    }

    /**
     * Store a newly created DrawingReceived in storage.
     *
     * @param CreateDrawingReceivedRequest $request
     *
     * @return Response
     */
    public function store(CreateDrawingReceivedRequest $request)
    {
        $input = $request->all();
        unset($input['attachments']); // prevent raw file in DB

        // Create the drawing received record
        $drawingReceived = $this->drawingReceivedRepository->create($input);

        // Handle file uploads for attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    try {
                        // 1. Store the uploaded file in storage/app/temp-uploads
                        $storedPath = $file->store('temp-uploads');

                        // 2. Build full file path
                        $fullPath = storage_path('app/' . $storedPath);

                        // 3. Confirm file exists
                        if (file_exists($fullPath)) {
                            // 4. Use original name and extension
                            $originalName = $file->getClientOriginalName();
                            $originalNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                            // 5. Pass to Media Library
                            $drawingReceived->addMedia($fullPath)
                                ->usingName($originalNameWithoutExt)
                                ->usingFileName($originalName)
                                ->toMediaCollection();

                            // 6. Delete temp file
                            unlink($fullPath);
                        } else {
                            \Log::error('Temp file not found after storing: ' . $fullPath);
                        }
                    } catch (\Exception $e) {
                        \Log::error('File upload failed: ' . $e->getMessage());
                    }
                }
            }
        }

        Flash::success(__('messages.saved', ['model' => 'Drawing Received']));

        return redirect(route('drawing-receiveds.index'));
    }

    /**
     * Display the specified DrawingReceived.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $drawingReceived = $this->drawingReceivedRepository->find($id);

        if (empty($drawingReceived)) {
            Flash::error('Drawing Received not found');

            return redirect(route('drawing-receiveds.index'));
        }

        return view('drawing-receiveds.show')->with('drawingReceived', $drawingReceived);
    }

    /**
     * Show the form for editing the specified DrawingReceived.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $drawingReceived = $this->drawingReceivedRepository->find($id);

        if (empty($drawingReceived)) {
            Flash::error('Drawing Received not found');

            return redirect(route('drawing-receiveds.index'));
        }

        // Get all lpoins
        $lpoins = Lpoin::with('quotation')->get()->pluck('ref_no', 'id');
        
        // Get all users (engineers)
        $engineers = User::pluck('name', 'id');
        
        // Get enum values for type_of_work
        $typeOfWork = config('enum.drawing_type_of_work');
        
        // Get enum values for status
        $statusOptions = config('enum.drawing_status');

        return view('drawing-receiveds.edit')->with(compact(
            'drawingReceived',
            'lpoins',
            'engineers',
            'typeOfWork',
            'statusOptions'
        ));
    }

    /**
     * Update the specified DrawingReceived in storage.
     *
     * @param int $id
     * @param UpdateDrawingReceivedRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDrawingReceivedRequest $request)
    {
        $drawingReceived = $this->drawingReceivedRepository->find($id);

        if (empty($drawingReceived)) {
            Flash::error('Drawing Received not found');

            return redirect(route('drawing-receiveds.index'));
        }

        $input = $request->all();
        unset($input['attachments']); // prevent raw file in DB

        // Update the drawing received record
        $this->drawingReceivedRepository->update($input, $id);

        // Handle file uploads for attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    try {
                        // 1. Store the uploaded file in storage/app/temp-uploads
                        $storedPath = $file->store('temp-uploads');

                        // 2. Build full file path
                        $fullPath = storage_path('app/' . $storedPath);

                        // 3. Confirm file exists
                        if (file_exists($fullPath)) {
                            // 4. Use original name and extension
                            $originalName = $file->getClientOriginalName();
                            $originalNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                            // 5. Pass to Media Library
                            $drawingReceived->addMedia($fullPath)
                                ->usingName($originalNameWithoutExt)
                                ->usingFileName($originalName)
                                ->toMediaCollection();

                            // 6. Delete temp file
                            unlink($fullPath);
                        } else {
                            \Log::error('Temp file not found after storing: ' . $fullPath);
                        }
                    } catch (\Exception $e) {
                        \Log::error('File upload failed: ' . $e->getMessage());
                    }
                }
            }
        }

        Flash::success(__('messages.saved', ['model' => 'Drawing Received']));

        return redirect(route('drawing-receiveds.index'));
    }

    /**
     * Remove the specified DrawingReceived from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $drawingReceived = $this->drawingReceivedRepository->find($id);

        if (empty($drawingReceived)) {
            Flash::error('Drawing Received not found');

            return redirect(route('drawing-receiveds.index'));
        }

        // Delete media files
        $drawingReceived->clearMediaCollection();

        // Delete the record
        $this->drawingReceivedRepository->delete($id);

        Flash::success(__('messages.deleted', ['model' => 'Drawing Received']));

        return redirect(route('drawing-receiveds.index'));
    }

    /**
     * Show engineer dashboard with assigned drawings
     * 
     * @return Response
     */
    /**
     * Show engineer's assigned drawings dashboard
     *
     * @return Response
     */
    public function engineerDashboard(EngineerDrawingDataTable $dataTable)
    {
        return $dataTable->render('drawing-receiveds.engineer-dashboard');
    }

    /**
     * Show contribution form for engineer
     *
     * @param int $id
     *
     * @return Response
     */
    public function contributeForm($id)
    {
        $drawingReceived = $this->drawingReceivedRepository->find($id);

        if (empty($drawingReceived)) {
            Flash::error('Drawing not found');
            return redirect(route('drawing-receiveds.engineer-dashboard'));
        }

        // Check if current user is the assigned engineer
        if ($drawingReceived->responsible_engineer_id != auth()->user()->id) {
            Flash::error('Unauthorized access');
            return redirect(route('drawing-receiveds.engineer-dashboard'));
        }

        $contributionTypes = config('enum.drawing_contribution_types');
        $statusOptions = config('enum.drawing_status');
        $contributions = $drawingReceived->contributions()->paginate(10);

        return view('drawing-receiveds.contribute-form', compact(
            'drawingReceived',
            'contributionTypes',
            'statusOptions',
            'contributions'
        ));
    }

    /**
     * Store engineer contribution
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function storeContribution($id, Request $request)
    {
        $drawingReceived = $this->drawingReceivedRepository->find($id);

        if (empty($drawingReceived)) {
            Flash::error('Drawing not found');
            return redirect(route('drawing-receiveds.engineer-dashboard'));
        }

        // Check if current user is the assigned engineer
        if ($drawingReceived->responsible_engineer_id != auth()->user()->id) {
            Flash::error('Unauthorized access');
            return redirect(route('drawing-receiveds.engineer-dashboard'));
        }

        // Validate
        $validated = $request->validate([
            'contribution_type' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'contribution_files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,dwg|max:20480',
        ]);

        try {
            // Create contribution record
            $contribution = new \App\Models\DrawingReceivedContribution([
                'drawing_received_id' => $id,
                'contributed_by_id' => auth()->user()->id,
                'contribution_type' => $validated['contribution_type'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);
            $contribution->save();

            // Update the main drawing status
            $drawingReceived->status = $validated['status'];
            if ($validated['status'] === 'Approved') {
                $drawingReceived->approval_date = now();
            } elseif ($validated['status'] === 'Comments Received') {
                $drawingReceived->review_comments_date = now();
            }
            $drawingReceived->save();

            // Handle file uploads
            if ($request->hasFile('contribution_files')) {
                foreach ($request->file('contribution_files') as $file) {
                    if ($file->isValid()) {
                        try {
                            $storedPath = $file->store('temp-uploads');
                            $fullPath = storage_path('app/' . $storedPath);

                            if (file_exists($fullPath)) {
                                $originalName = $file->getClientOriginalName();
                                $originalNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                                $contribution->addMedia($fullPath)
                                    ->usingName($originalNameWithoutExt)
                                    ->usingFileName($originalName)
                                    ->toMediaCollection();

                                unlink($fullPath);
                            }
                        } catch (\Exception $e) {
                            \Log::error('File upload failed: ' . $e->getMessage());
                        }
                    }
                }
            }

            Flash::success('Contribution submitted successfully!');

        } catch (\Exception $e) {
            \Log::error('Error storing contribution: ' . $e->getMessage());
            Flash::error('Error submitting contribution: ' . $e->getMessage());
        }

        return redirect(route('drawing-receiveds.contribute-form', $id));
    }

    /**
     * View contribution history
     *
     * @param int $id
     *
     * @return Response
     */
    public function contributionHistory($id)
    {
        $drawingReceived = $this->drawingReceivedRepository->find($id);

        if (empty($drawingReceived)) {
            Flash::error('Drawing not found');
            return redirect(route('drawing-receiveds.index'));
        }

        $contributions = $drawingReceived->contributions()->paginate(20);

        return view('drawing-receiveds.contribution-history', compact('drawingReceived', 'contributions'));
    }
}

