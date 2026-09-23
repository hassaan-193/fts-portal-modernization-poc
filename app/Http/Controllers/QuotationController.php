<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\DataTables\QuotationDataTable;
use App\Repositories\QuotationRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;

class QuotationController extends AppBaseController
{
    /** @var  QuotationRepository */
    private $quotationRepository;

    public function __construct(QuotationRepository $quotationRepo)
    {
        $this->middleware('can:quotations')->except(['getAmcProjects']);
        $this->quotationRepository = $quotationRepo;
    }

    /**
     * Display a listing of the Quotation.
     *
     * @param QuotationDataTable $quotationDataTable
     * @return Response
     */
    public function index(QuotationDataTable $quotationDataTable)
    {
        return $quotationDataTable->render('quotations.index');
    }

    /**
     * Show the form for creating a new Quotation.
     *
     * @return Response
     */
    public function create()
    {
        return view('quotations.create');
    }

    /**
     * Store a newly created Quotation in storage.
     *
     * @param CreateQuotationRequest $request
     *
     * @return Response
     */
    public function store(CreateQuotationRequest $request)
    {
        $input = $request->all();

        // Check if quotation_type_id exists and equals 6 (AMC)
        if (isset($input['quotation_type_id']) && in_array((int)$input['quotation_type_id'], [6, 7, 16, 22])) {
            $input['category'] = $request->input('category', 'amc');
            $input['number_of_visits'] = $request->input('number_of_visits', 4);
        } else {
            $input['category'] = null;
            $input['number_of_visits'] = null;
        }
        $quotation = $this->quotationRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/quotations.singular')]));

        return redirect(route('quotations.index'));
    }

    /**
     * Display the specified Quotation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $quotation = $this->quotationRepository->find($id);

        if (empty($quotation)) {
            Flash::error(__('models/quotations.singular').' '.__('messages.not_found'));

            return redirect(route('quotations.index'));
        }

        if ($quotation->quotation_type->name === 'Annual Maintenance Contract' || $quotation->quotation_type->name === 'Maintenance') {
            $quotation->with('products');
            return view('quotations.show_product_quotation')->with('quotation', $quotation);
        }

        return view('quotations.show')->with('quotation', $quotation);
    }

    /**
     * Show the form for editing the specified Quotation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $quotation = $this->quotationRepository->find($id);

        if (empty($quotation)) {
            Flash::error(__('messages.not_found', ['model' => __('models/quotations.singular')]));

            return redirect(route('quotations.index'));
        }

        return view('quotations.edit')->with('quotation', $quotation);
    }

    /**
     * Update the specified Quotation in storage.
     *
     * @param  int              $id
     * @param UpdateQuotationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQuotationRequest $request)
    {
        $quotation = $this->quotationRepository->find($id);
    
        if (empty($quotation)) {
            Flash::error(__('messages.not_found', ['model' => __('models/quotations.singular')]));
            return redirect(route('quotations.index'));
        }
    
        $input = $request->all();
    
        // Only set category and number_of_visits if it's an AMC type quotation
        if (isset($input['quotation_type_id']) && in_array((int)$input['quotation_type_id'], [6, 7, 16, 22])) {
            $input['category'] = $request->input('category', 'amc');
            $input['number_of_visits'] = $request->input('number_of_visits', 4);
        } else {
            $input['category'] = null;
            $input['number_of_visits'] = null;
        }
    
        $quotation = $this->quotationRepository->update($input, $id);
    
        Flash::success(__('messages.updated', ['model' => __('models/quotations.singular')]));
    
        return redirect(route('quotations.index'));
    }
    

    /**
     * Remove the specified Quotation from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $quotation = $this->quotationRepository->find($id);

        if (empty($quotation)) {
            Flash::error(__('messages.not_found', ['model' => __('models/quotations.singular')]));

            return redirect(route('quotations.index'));
        }

        $status = $this->quotationRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/quotations.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('quotations.index'));
    }
    // Update status
    public function update_status($id)
    {
        $quotation = $this->quotationRepository->find($id);

        if (empty($quotation)) {
            Flash::error(__('messages.not_found', ['model' => __('models/quotations.singular')]));
            return redirect()->back();
        }
        // ccheck if customerr dataa exists
        if($quotation->status == 0){
            $quotation->load('company');
            $company = $quotation->company;
            if(!$company->contact_person ||
                !$company->billing_address ||
                !$company->shipping_address
            ){
                Flash::error("Please fill company information before approving the quotation.");
                return redirect()->back();
            }
        }
        $approving = ! $quotation->status;

        $quotation->update([
            'status' => $approving ? 1 : 0,
            'approved_by' => $approving ? auth()->id() : null,
        ]);

        Flash::success(__('messages.updated', ['model' => __('models/quotations.singular')]));
        return redirect(route('quotations.index'));
    }
    // Add company by ajax
    public function add_type_ajax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'type_name' => 'required|unique:lookups,name',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->messages()->get('*');
            return response()->json($errors, 422);
        }

        $input = $request->all();
        $type = \App\Models\Lookup::create([
            'name' => $input['type_name'],
            'tag' => 'quotation_type',
        ]);

        if($type)
            return response()->json($type);
        else
            return response()->json('Unable to add Type!', 423);
    }

    public function getAmcProjects(Request $request)
    {
        $companyId = $request->input('company_id');

        if (!$companyId) {
            return response()->json([]);
        }

        // Get AMC projects by joining with quotations to match company
        $projects = \App\Models\Project::join('quotations', 'projects.quotation_id', '=', 'quotations.id')
            ->where('quotations.company_id', $companyId)
            ->where('projects.category', 'amc')
            ->select('projects.id', 'projects.subject')
            ->get()
            ->pluck('subject', 'id');

        return response()->json($projects);
    }
}
