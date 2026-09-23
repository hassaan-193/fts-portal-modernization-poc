<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\DataTables\CompanyDataTable;
use App\Repositories\CompanyRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Exports\CompanySOAExport;
use Maatwebsite\Excel\Facades\Excel;
use  Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class CompanyController extends AppBaseController
{
    /** @var  CompanyRepository */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepository = $companyRepo;
    }

    /**
     * Display a listing of the Company.
     *
     * @param CompanyDataTable $companyDataTable
     * @return Response
     */
    public function index(CompanyDataTable $companyDataTable)
    {
        return $companyDataTable->render('companies.index');
    }

    /**
     * Show the form for creating a new Company.
     *
     * @return Response
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created Company in storage.
     *
     * @param CreateCompanyRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyRequest $request)
    {
        $input = $request->all();

        $company = $this->companyRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/companies.singular')]));

        return redirect(route('companies.index'));
    }

    /**
     * Display the specified Company.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error(__('models/companies.singular').' '.__('messages.not_found'));

            return redirect(route('companies.index'));
        }

        return view('companies.show')->with('company', $company);
    }

    /**
     * Show the form for editing the specified Company.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error(__('messages.not_found', ['model' => __('models/companies.singular')]));

            return redirect(route('companies.index'));
        }

        return view('companies.edit')->with('company', $company);
    }

    /**
     * Update the specified Company in storage.
     *
     * @param  int              $id
     * @param UpdateCompanyRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyRequest $request)
    {
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error(__('messages.not_found', ['model' => __('models/companies.singular')]));

            return redirect(route('companies.index'));
        }

        $company = $this->companyRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/companies.singular')]));

        return redirect(route('companies.index'));
    }

    /**
     * Remove the specified Company from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error(__('messages.not_found', ['model' => __('models/companies.singular')]));

            return redirect(route('companies.index'));
        }

        $status = $this->companyRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/companies.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));

        return redirect(route('companies.index'));
    }
    // Add company by ajax
    public function add_company_ajax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'company_name' => 'required',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->messages()->get('*');
            return response()->json($errors, 422);
        }

        $input = $request->all();
        $company = $this->companyRepository->create([
            'name' => $input['company_name']
        ]);

        if($company)
            return response()->json($company);
        else
            return response()->json('Unable to add company!', 423);
    }
    /**
     * Display the Company report.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function get_company_report($id)
    {
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error(__('models/companies.singular').' '.__('messages.not_found'));
            return redirect(route('companies.index'));
        }
        // load all relationships
        $company->load(['quotations.invoices.invoice_type','quotations.invoices.invoice_service_details','quotations.invoices.transaction.transaction_payment_type']);
        return view('companies.report')->with('company', $company);
    }
    public function export_company_report ($id,$type)
    {
        $company = $this->companyRepository->find($id);
        if (!empty($company)) {
            // load all relationships
            $company->load(['quotations.invoices.invoice_type','quotations.invoices.invoice_service_details','quotations.invoices.transaction.transaction_payment_type']);
        }
        if($type=='excel'){
            return Excel::download(new CompanySOAExport($company), 'report.xlsx');
        }  
        elseif($type=='csv'){
            return Excel::download(new CompanySOAExport($company), 'report.csv');
        }
        elseif($type=='pdf'){
            return PDF::loadView('exports.companies_report', compact('company'))->setPaper('a3')->setOrientation('landscape')->setOption('margin-right', 2)->setOption('margin-left', 2)->download('SOA_report.pdf');
        }
    }
}
