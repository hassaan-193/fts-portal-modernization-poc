<?php

namespace App\Http\Controllers;

use App\DataTables\RequestFormDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateRequestFormRequest;
use App\Http\Requests\UpdateRequestFormRequest;
use App\Repositories\RequestFormRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class RequestFormController extends AppBaseController
{
    /** @var  RequestFormRepository */
    private $requestFormRepository;

    public function __construct(RequestFormRepository $requestFormRepo)
    {
        $this->requestFormRepository = $requestFormRepo;
    }

    /**
     * Display a listing of the RequestForm.
     *
     * @param RequestFormDataTable $requestFormDataTable
     * @return Response
     */
    public function index(RequestFormDataTable $requestFormDataTable)
    {
        return $requestFormDataTable->render('request_forms.index');
    }

    /**
     * Show the form for creating a new RequestForm.
     *
     * @return Response
     */
    public function create()
    {
        return view('request_forms.create');
    }

    /**
     * Store a newly created RequestForm in storage.
     *
     * @param CreateRequestFormRequest $request
     *
     * @return Response
     */
    public function store(CreateRequestFormRequest $request)
    {
        $input = $request->all();

        $requestForm = $this->requestFormRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/request_forms.singular')]));

        return redirect(route('requestForms.index'));
    }

    /**
     * Display the specified RequestForm.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $requestForm = $this->requestFormRepository->find($id);

        if (empty($requestForm)) {
            Flash::error(__('models/request_forms.singular').' '.__('messages.not_found'));

            return redirect(route('requestForms.index'));
        }

        return view('request_forms.show')->with('requestForm', $requestForm);
    }

    /**
     * Show the form for editing the specified RequestForm.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $requestForm = $this->requestFormRepository->find($id);

        if (empty($requestForm)) {
            Flash::error(__('messages.not_found', ['model' => __('models/request_forms.singular')]));

            return redirect(route('requestForms.index'));
        }

        return view('request_forms.edit')->with('requestForm', $requestForm);
    }

    /**
     * Update the specified RequestForm in storage.
     *
     * @param  int              $id
     * @param UpdateRequestFormRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRequestFormRequest $request)
    {
        $requestForm = $this->requestFormRepository->find($id);

        if (empty($requestForm)) {
            Flash::error(__('messages.not_found', ['model' => __('models/request_forms.singular')]));

            return redirect(route('requestForms.index'));
        }

        $requestForm = $this->requestFormRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/request_forms.singular')]));

        return redirect(route('requestForms.index'));
    }

    /**
     * Remove the specified RequestForm from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $requestForm = $this->requestFormRepository->find($id);

        if (empty($requestForm)) {
            Flash::error(__('messages.not_found', ['model' => __('models/request_forms.singular')]));

            return redirect(route('requestForms.index'));
        }

        $status = $this->requestFormRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/request_forms.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('requestForms.index'));
    }
}
