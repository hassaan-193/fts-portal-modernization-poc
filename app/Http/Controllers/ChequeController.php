<?php

namespace App\Http\Controllers;

use App\DataTables\ChequeDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateChequeRequest;
use App\Http\Requests\UpdateChequeRequest;
use App\Repositories\ChequeRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class ChequeController extends AppBaseController
{
    /** @var  ChequeRepository */
    private $chequeRepository;

    public function __construct(ChequeRepository $chequeRepo)
    {
        $this->chequeRepository = $chequeRepo;
    }

    /**
     * Display a listing of the Cheque.
     *
     * @param ChequeDataTable $chequeDataTable
     * @return Response
     */
    public function index(ChequeDataTable $chequeDataTable)
    {
        return $chequeDataTable->render('cheques.index');
    }

    /**
     * Show the form for creating a new Cheque.
     *
     * @return Response
     */
    public function create()
    {
        return view('cheques.create');
    }

    /**
     * Store a newly created Cheque in storage.
     *
     * @param CreateChequeRequest $request
     *
     * @return Response
     */
    public function store(CreateChequeRequest $request)
    {
        $input = $request->all();

        $cheque = $this->chequeRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/cheques.singular')]));

        return redirect(route('cheques.index'));
    }

    /**
     * Display the specified Cheque.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $cheque = $this->chequeRepository->find($id);

        if (empty($cheque)) {
            Flash::error(__('models/cheques.singular').' '.__('messages.not_found'));

            return redirect(route('cheques.index'));
        }

        return view('cheques.show')->with('cheque', $cheque);
    }

    /**
     * Show the form for editing the specified Cheque.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $cheque = $this->chequeRepository->find($id);

        if (empty($cheque)) {
            Flash::error(__('messages.not_found', ['model' => __('models/cheques.singular')]));

            return redirect(route('cheques.index'));
        }

        return view('cheques.edit')->with('cheque', $cheque);
    }

    /**
     * Update the specified Cheque in storage.
     *
     * @param  int              $id
     * @param UpdateChequeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChequeRequest $request)
    {
        $cheque = $this->chequeRepository->find($id);

        if (empty($cheque)) {
            Flash::error(__('messages.not_found', ['model' => __('models/cheques.singular')]));

            return redirect(route('cheques.index'));
        }

        $cheque = $this->chequeRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/cheques.singular')]));

        return redirect(route('cheques.index'));
    }

    /**
     * Remove the specified Cheque from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $cheque = $this->chequeRepository->find($id);

        if (empty($cheque)) {
            Flash::error(__('messages.not_found', ['model' => __('models/cheques.singular')]));

            return redirect(route('cheques.index'));
        }

        $status = $this->chequeRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/cheques.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('cheques.index'));
    }
}
