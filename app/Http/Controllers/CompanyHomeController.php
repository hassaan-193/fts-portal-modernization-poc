<?php

namespace App\Http\Controllers;

use Laracasts\Flash\Flash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUserRequest;
use App\DataTables\CompanyCards\CompanyLpoinDataTable;
use App\DataTables\CompanyCards\CompanyInvoiceDataTable;
use App\DataTables\CompanyCards\CompanyQuotationDataTable;

class CompanyHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:company');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CompanyQuotationDataTable $companyQuotationDataTable, CompanyInvoiceDataTable $companyInvoiceDataTable, CompanyLpoinDataTable $companyLpoinDataTable)
    {
        if (request()->get('table') == 'companyLpoinDataTable') {
            return $companyLpoinDataTable->render('company_home', compact('companyLpoinDataTable'));
        }

        if (request()->get('table') == 'companyInvoiceDataTable') {
            return $companyInvoiceDataTable->render('company_home', compact('companyInvoiceDataTable'));
        }

        return $companyQuotationDataTable->render('company_home', compact('companyQuotationDataTable','companyInvoiceDataTable','companyLpoinDataTable'));
    }

    // Get User Profile
    public function user_profile($id)
    {
        if(\Auth::guard('company')->user()->id != $id)
            return abort(404);

        $user = \Auth::guard('company')->user();
        return view('companies.profile')->with('user', $user);
    }
    // Update User Profile
    public function update_user_profile($id, ProfileUserRequest $request)
    {
        if(\Auth::guard('company')->user()->id != $id)
            return abort(404);

        $user = \Auth::guard('company')->user();
        $input = $request->all();
        if($request->has('password') && $request->password != null)
            $input['password'] = Hash::make($request->password);
        else
            $input = $request->except(['password','password_confirmation']);

        $user->fill($input);
        $user->save();

        Flash::success('Profile updated successfully.');
        return redirect()->back();
    }
}
