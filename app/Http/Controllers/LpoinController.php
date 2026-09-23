<?php

namespace App\Http\Controllers;

use App\DataTables\LpoinDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateLpoinRequest;
use App\Http\Requests\UpdateLpoinRequest;
use App\Repositories\LpoinRepository;
use App\Events\LpoinCreated;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class LpoinController extends AppBaseController
{
    /** @var  LpoinRepository */
    private $lpoinRepository;

    public function __construct(LpoinRepository $lpoinRepo)
    {
        $this->middleware('can:lpoins');
        $this->lpoinRepository = $lpoinRepo;
    }

    /**
     * Display a listing of the Lpoin.
     *
     * @param LpoinDataTable $lpoinDataTable
     * @return Response
     */
    public function index(LpoinDataTable $lpoinDataTable)
    {
        return $lpoinDataTable->render('lpoins.index');
    }

    /**
     * Show the form for creating a new Lpoin.
     *
     * @return Response
     */
    public function create()
    {
        // Get quotations without lpoins and with status = 1
        $quotationItemsQuery = \App\Models\Quotation::doesnthave('lpoins')->whereStatus(1)->with('quotation_type');
    
        // Build the dropdown display labels
        $quotations = [];
        foreach ($quotationItemsQuery->get() as $value) {
            $type = $value->quotation_type ? $value->quotation_type->name : '';
            $quotations[$value->id] = $value->name . ' (' . $value->id . ') ( ' . $type . ' ) ( ' . $value->amount . ' ) ';
        }
    
        // Add an empty option at the top
        $quotations = array_replace(['' => ''], $quotations);
    
        // Build quotation ID => category map for JS
        $quotationCategories = $quotationItemsQuery->pluck('category', 'id')->toArray();
    
        // Pass both to the view
        return view('lpoins.create', [
            'quotationItems'      => $quotations,
            'quotationCategories' => $quotationCategories,
            'isCreate' => true, 

        ]);
    }
    

    /**
     * Store a newly created Lpoin in storage.
     *
     * @param CreateLpoinRequest $request
     *
     * @return Response
     */
    public function store(CreateLpoinRequest $request)
    {
        $input = $request->all();
        unset($input['file']); // prevent raw file in DB

        // Create the lpoin record
        $lpoin = $this->lpoinRepository->create($input);

        // Handle file upload safely
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
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
                            $lpoin->addMedia($fullPath)
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

        // Fire the event AFTER everything succeeds which will create amc project and its visits 
        // if the quotation is amc type 
        event(new LpoinCreated($lpoin, $request->date_issue, $request->subject));

        Flash::success(__('messages.saved', ['model' => __('models/lpoins.singular')]));

        return redirect(route('lpoins.index'));
    }

    /**
     * Display the specified Lpoin.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $lpoin = $this->lpoinRepository->find($id);

        if (empty($lpoin)) {
            Flash::error(__('models/lpoins.singular').' '.__('messages.not_found'));

            return redirect(route('lpoins.index'));
        }

        return view('lpoins.show')->with('lpoin', $lpoin);
    }

    /**
     * Show the form for editing the specified Lpoin.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $lpoin = $this->lpoinRepository->find($id);

        if (empty($lpoin)) {
            Flash::error(__('messages.not_found', ['model' => __('models/lpoins.singular')]));

            return redirect(route('lpoins.index'));
        }
        // quotations for lpoin
        $quotationItems = \App\Models\Quotation::doesnthave('lpoins')->whereStatus(1)->get(['id','name','quotation_type_id']);
        $quotations = [];
        $type = '';

        foreach ($quotationItems as $key => $value) {
            if($value->quotation_type)
                $type = $value->quotation_type->name;

            $quotations[$value->id] =  $value->name . ' ('.$value->id.')' . ' ( '. $type .' ) ';
        }
        $quotations = array_replace([ $lpoin->quotation->id  => $lpoin->quotation->name .' ('.$lpoin->quotation->id.')' . ' ( '. $lpoin->quotation->quotation_type->name .' ) '] ,$quotations);
        return view('lpoins.edit')->with([
            'lpoin' =>  $lpoin,
            'quotationItems' => $quotations,
            'isCreate' => false, 
        ]);
    }

    /**
     * Update the specified Lpoin in storage.
     *
     * @param  int              $id
     * @param UpdateLpoinRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLpoinRequest $request)
    {
        $lpoin = $this->lpoinRepository->find($id);

        if (empty($lpoin)) {
            Flash::error(__('messages.not_found', ['model' => __('models/lpoins.singular')]));

            return redirect(route('lpoins.index'));
        }

        $lpoin = $this->lpoinRepository->update($request->all(), $id);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
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
                            $lpoin->addMedia($fullPath)
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

        Flash::success(__('messages.updated', ['model' => __('models/lpoins.singular')]));

        return redirect(route('lpoins.index'));
    }

    /**
     * Remove the specified Lpoin from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $lpoin = $this->lpoinRepository->find($id);

        if (empty($lpoin)) {
            Flash::error(__('messages.not_found', ['model' => __('models/lpoins.singular')]));

            return redirect(route('lpoins.index'));
        }

        $status = $this->lpoinRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/lpoins.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('lpoins.index'));
    }
}
