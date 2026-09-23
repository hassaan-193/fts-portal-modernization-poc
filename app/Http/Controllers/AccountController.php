<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use App\DataTables\AccountDataTable;
use App\Repositories\AccountRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\DataTables\AccountTransactionDataTable;

class AccountController extends AppBaseController
{
    /** @var  AccountRepository */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepo)
    {
        $this->accountRepository = $accountRepo;
    }

    /**
     * Display a listing of the Account.
     *
     * @param AccountDataTable $accountDataTable
     * @return Response
     */
    public function index(AccountDataTable $accountDataTable)
    {
        return $accountDataTable->render('accounts.index');
    }

    /**
     * Show the form for creating a new Account.
     *
     * @return Response
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created Account in storage.
     *
     * @param CreateAccountRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountRequest $request)
    {
        $input = $request->all();

        $account = $this->accountRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/accounts.singular')]));

        return redirect(route('accounts.index'));
    }

    /**
     * Display the specified Account.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id, AccountTransactionDataTable $accountTransactionDataTable)
    {
        $account = $this->accountRepository->find($id);

        if (empty($account)) {
            Flash::error(__('models/accounts.singular').' '.__('messages.not_found'));

            return redirect(route('accounts.index'));
        }

        return $accountTransactionDataTable->with('account', $account)->render('accounts.show',compact('account'));
    }

    /**
     * Show the form for editing the specified Account.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $account = $this->accountRepository->find($id);

        if (empty($account)) {
            Flash::error(__('messages.not_found', ['model' => __('models/accounts.singular')]));

            return redirect(route('accounts.index'));
        }

        return view('accounts.edit')->with('account', $account);
    }

    /**
     * Update the specified Account in storage.
     *
     * @param  int              $id
     * @param UpdateAccountRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountRequest $request)
    {
        $account = $this->accountRepository->find($id);

        if (empty($account)) {
            Flash::error(__('messages.not_found', ['model' => __('models/accounts.singular')]));

            return redirect(route('accounts.index'));
        }

        $account = $this->accountRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/accounts.singular')]));

        return redirect(route('accounts.index'));
    }

    /**
     * Remove the specified Account from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $account = $this->accountRepository->find($id);

        if (empty($account)) {
            Flash::error(__('messages.not_found', ['model' => __('models/accounts.singular')]));

            return redirect(route('accounts.index'));
        }

        $status = $this->accountRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/accounts.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));

        return redirect(route('accounts.index'));
    }
}
