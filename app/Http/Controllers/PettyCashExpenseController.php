<?php

namespace App\Http\Controllers;

use App\DataTables\PettyCashExpenseDataTable;
use App\Http\Requests;
use App\Http\Requests\CreatePettyCashExpenseRequest;
use App\Http\Requests\UpdatePettyCashExpenseRequest;
use App\Repositories\PettyCashExpenseRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class PettyCashExpenseController extends Controller
{
    /** @var  PettyCashExpenseRepository */
    private $PettyCashExpenseRepository;

    public function __construct(PettyCashExpenseRepository $pettyCashExpenseRepo)
    {
        $this->PettyCashExpenseRepository = $pettyCashExpenseRepo;
    }

    /**
     * Display a listing of the PettyCash.
     *
     * @param PettyCashExpenseDataTable $pettyCashExpenseDataTable
     * @return Response
     */
    public function index(PettyCashExpenseDataTable $pettyCashExpenseDataTable)
    {
        return $pettyCashExpenseDataTable->render('petty_cash_expenses.index');
    }

    /**
     * Show the form for creating a new PettyCash.
     *
     * @return Response
     */
    public function create()
    {
        return view('petty_cash_expenses.create');
    }

    /**
     * Store a newly created PettyCash in storage.
     *
     * @param CreatePettyCashExpenseRequest $request
     *
     * @return Response
     */
    public function store(CreatePettyCashExpenseRequest $request)
    {
        $input = $request->all();

        $pettyCash = $this->PettyCashExpenseRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/petty_cash_expenses.singular')]));

        return redirect(route('pettyCashExpenses.index'));
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
        $pettyCashExpense = $this->PettyCashExpenseRepository->find($id);

        if (empty($pettyCashExpense)) {
            Flash::error(__('models/petty_cash_expenses.singular').' '.__('messages.not_found'));

            return redirect(route('pettyCashExpenses.index'));
        }

        return view('petty_cash_expenses.show')->with('pettyCashExpense', $pettyCashExpense);
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
        $pettyCashExpense = $this->PettyCashExpenseRepository->find($id);

        if (empty($pettyCashExpense)) {
            Flash::error(__('messages.not_found', ['model' => __('models/petty_cash_expenses.singular')]));

            return redirect(route('pettyCashExpenses.index'));
        }

        return view('petty_cash_expenses.edit')->with('pettyCashExpense', $pettyCashExpense);
    }

    /**
     * Update the specified PettyCash in storage.
     *
     * @param  int $id
     * @param UpdatePettyCashExpenseRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePettyCashExpenseRequest $request)
    {
        $pettyCashExpenses = $this->PettyCashExpenseRepository->find($id);

        if (empty($pettyCashExpenses)) {
            Flash::error(__('messages.not_found', ['model' => __('models/petty_cash_expenses.singular')]));

            return redirect(route('pettyCashExpenses.index'));
        }

        $pettyCashExpenses = $this->PettyCashExpenseRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/petty_cash_expenses.singular')]));

        return redirect(route('pettyCashExpenses.index'));
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
        $pettyCashExpense = $this->PettyCashExpenseRepository->find($id);

        if (empty($pettyCashExpense)) {
            Flash::error(__('messages.not_found', ['model' => __('models/petty_cash_expenses.singular')]));

            return redirect(route('pettyCashExpenses.index'));
        }

        $status = $this->PettyCashExpenseRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/petty_cash_expenses.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('pettyCashExpenses.index'));
    }
}
