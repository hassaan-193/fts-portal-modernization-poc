<?php

namespace App\Http\Controllers;

use App\DataTables\PettyCashDataTable;
use App\Http\Requests;
use App\Http\Requests\CreatePettyCashRequest;
use App\Http\Requests\UpdatePettyCashRequest;
use App\Repositories\PettyCashRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class PettyCashController extends AppBaseController
{
    /** @var  PettyCashRepository */
    private $pettyCashRepository;

    public function __construct(PettyCashRepository $pettyCashRepo)
    {
        $this->pettyCashRepository = $pettyCashRepo;
    }

    /**
     * Display a listing of the PettyCash.
     *
     * @param PettyCashDataTable $pettyCashDataTable
     * @return Response
     */
    public function index(PettyCashDataTable $pettyCashDataTable)
    {
        return $pettyCashDataTable->render('petty_cashes.index');
    }

    /**
     * Show the form for creating a new PettyCash.
     *
     * @return Response
     */
    public function create()
    {
        return view('petty_cashes.create');
    }

    /**
     * Store a newly created PettyCash in storage.
     *
     * @param CreatePettyCashRequest $request
     *
     * @return Response
     */
    public function store(CreatePettyCashRequest $request)
    {
        $input = $request->all();

        $pettyCash = $this->pettyCashRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/petty_cashes.singular')]));

        return redirect(route('pettyCashes.index'));
    }

    /**
     * Display the specified PettyCash.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $pettyCash = $this->pettyCashRepository->find($id);

        if (empty($pettyCash)) {
            Flash::error(__('models/petty_cashes.singular').' '.__('messages.not_found'));

            return redirect(route('pettyCashes.index'));
        }

        return view('petty_cashes.show')->with('pettyCash', $pettyCash);
    }

    /**
     * Show the form for editing the specified PettyCash.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $pettyCash = $this->pettyCashRepository->find($id);

        if (empty($pettyCash)) {
            Flash::error(__('messages.not_found', ['model' => __('models/petty_cashes.singular')]));

            return redirect(route('pettyCashes.index'));
        }

        return view('petty_cashes.edit')->with('pettyCash', $pettyCash);
    }

    /**
     * Update the specified PettyCash in storage.
     *
     * @param  int              $id
     * @param UpdatePettyCashRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePettyCashRequest $request)
    {
        $pettyCash = $this->pettyCashRepository->find($id);

        if (empty($pettyCash)) {
            Flash::error(__('messages.not_found', ['model' => __('models/petty_cashes.singular')]));

            return redirect(route('pettyCashes.index'));
        }

        $pettyCash = $this->pettyCashRepository->updateRecord($pettyCash, $request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/petty_cashes.singular')]));

        return redirect(route('pettyCashes.index'));
    }

    /**
     * Remove the specified PettyCash from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $pettyCash = $this->pettyCashRepository->find($id);

        if (empty($pettyCash)) {
            Flash::error(__('messages.not_found', ['model' => __('models/petty_cashes.singular')]));

            return redirect(route('pettyCashes.index'));
        }

        $status = $this->pettyCashRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/petty_cashes.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('pettyCashes.index'));
    }
}
