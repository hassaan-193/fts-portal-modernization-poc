<?php

namespace App\Providers;
use View;
use App\User;
use Carbon\Carbon;
use App\Models\Lpoin;
use App\Models\Lookup;
use App\Models\Lpoout;
use App\Models\Vendor;
use App\Models\Account;
use App\Models\Company;
use App\Models\Product;
use App\Models\Project;
use App\Models\Employee;

use App\Models\PettyCash;
use App\Models\Quotation;
use App\Models\LpoOutType;
use App\Models\Permission;
use App\Models\InvoiceBank;
use App\Models\InvoiceType;
use App\Models\ProjectType;
use App\Models\StafProfile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // account report
        View::composer(["reports.account"], function ($view) {
            $Accounts = Account::orderBy('id','desc')->get()->toArray();
            $view->with('accounts', $Accounts);
        });
        //balanaces for petty cash
        View::composer(['petty_cashes.index'], function ($view) {
            $account = Account::where('type','PettyCash')->first();
            if($account)
                $view->with('total_amount', $account->balance);
            else
                $view->with('total_amount', 0);
        });
        View::composer(['petty_cashes.index'], function ($view) {
            $account = Account::where('type','Advance')->first();
            if($account)
                $view->with('total_advance', $account->balance);
            else
                $view->with('total_advance', 0);
        });
        // end of balances

        View::composer(['projects.fields','projects.edit_fields'], function ($view) {
            $users = User::pluck('name','id')->toArray();
            $users = array_replace(['' => ''] , $users);
            $view->with('users', $users);
        });
        View::composer(['lpoouts.fields','payment_invoices.fields'], function ($view) {
            $projectItems = Quotation::has('project')->with('project')->get()->pluck('name','project.id')->toArray();
            $projectItems = array_replace(['' => ''] ,$projectItems);
            $view->with('projectItems', $projectItems);
        });
        View::composer(['petty_cashes.fields'], function ($view) {
            $userItems = Employee::get(['name','id', 'balance']);
            $users = [ '' => '' ];
            foreach ($userItems as $key => $value) {
                $users[$value->id] =  $value->name . ' : '. $value->balance;
            }
            $view->with('userItems', $users);
        });
        View::composer(['receipts.fields','cheques.fields','payments.fields'], function ($view) {
            $accountItems = Account::pluck('type','id')->toArray();
            $accountItems = array_replace(['' => ''] ,$accountItems);
            $view->with('accountItems', $accountItems);
        });
        // for petty cash
        View::composer(['petty_cashes.fields'], function ($view) {
            $accountItems = Account::where('type', '!=', 'PettyCash')->pluck('type','id')->toArray();
            $accountItems = array_replace(['' => ''] ,$accountItems);
            $view->with('accountItems', $accountItems);
        });
        View::composer(['petty_cashes.fields'], function ($view) {
            $lookupItems = Lookup::where('tag' , 'transaction_type')->pluck('name','id')->toArray();
            $lookupItems = array_replace(['' => ''] ,$lookupItems);
            $view->with('lookupItems', $lookupItems);
        });
        View::composer(['receipts.fields','payments.fields'], function ($view) {
            $lookupItems = Lookup::where('tag' , 'transaction_payment_type')->pluck('name','id')->toArray();
            // $lookupItems = array_replace(['' => ''] ,$lookupItems);
            $view->with('lookupItems', $lookupItems);
        });
        View::composer(['quotations.fields'], function ($view) {
            $typeItems = Lookup::where('tag' , 'quotation_type')->pluck('name','id')->toArray();
            $typeItems = array_replace(['' => ''] ,$typeItems);
            $view->with('typeItems', $typeItems);
        });
        View::composer(['quotations.fields'], function ($view) {
            $typeCompany = Lookup::where('tag' , 'quotation_company')->pluck('name','id')->toArray();
            $view->with('typeCompany', $typeCompany);
        });
        View::composer(['quotations.fields'], function ($view) {
            $products = Product::select('id', 'name', 'price')->get();
            $view->with('products', $products);
        });
        View::composer(['invoice_requests.fields'], function ($view) {
            $lpooutItems = Lpoout::pluck('name','id')->toArray();
            $view->with('lpooutItems', $lpooutItems);
        });
        View::composer(['payment_invoices.fields'], function ($view) {
            // Only allow payment invoices against the latest active revision
            $lpooutItems = Lpoout::where('is_latest_revision', true)->pluck('name', 'id')->toArray();
            $lpooutItems = array_replace(['' => ''] ,$lpooutItems);
            $view->with('lpooutItems', $lpooutItems);
        });
        View::composer(['invoices.fields'], function ($view) {
            $invoiceBanks = InvoiceBank::pluck('bank_name','id')->toArray();
            $view->with('invoiceBanks', $invoiceBanks);
        });
        View::composer(['projects.projects_partials.project_lpoout'], function ($view) {
            $lpooutItems = Lpoout::whereHas('lpo_out_type',function($query){
                $query->whereName('Project');
            })->pluck('name','id')->toArray();
            $view->with('lpooutItems', $lpooutItems);
        });
        View::composer(['invoices.fields'], function ($view) {
            $invoice_typeItems = InvoiceType::pluck('name','id')->toArray();
            $view->with('invoice_typeItems', $invoice_typeItems);
        });
        View::composer(['projects.fields','invoice_requests.fields'], function ($view) {
            $project_typeItems = ProjectType::pluck('name','id')->toArray();
            $view->with('project_typeItems', $project_typeItems);
        });
        View::composer(['lpoouts.fields','payments.fields','petty_cashes.fields','payment_invoices.fields'], function ($view) {
            $vendors = Vendor::select('vat_no','name','id')->get();
            $vendorItems=[];

            foreach ($vendors as $key => $vendor) {
                $vendorItems[$vendor->id] = $vendor->name.' ('.$vendor->vat_no.')';
            }
            $vendorItems = array_replace(['' => ''] ,$vendorItems);
            $view->with('vendorItems', $vendorItems);
        });
        View::composer(['lpoouts.fields'], function ($view) {
            $lpo_out_typeItems = LpoOutType::pluck('name','id')->toArray();
            $view->with('lpo_out_typeItems', $lpo_out_typeItems);
        });
        View::composer(['invoices.fields'], function ($view) {
            $quotationItems = Quotation::pluck('name','id')->toArray();
            $view->with('quotationItems', $quotationItems);
        });
        // for projectrequest
        View::composer(['projects.fields'], function ($view) {
            $lpoinItems = Lpoin::with('quotation')->doesnthave('quotation.project')->get();
            $lpoins = [];
            foreach ($lpoinItems as $key => $lpoin) {
                $lpoins[$lpoin->quotation_id] =  'Lpoin Ref #:  '.$lpoin->ref_no.' ( Quotation: '.$lpoin->quotation->name.' )';
            }
            $lpoins = array_replace(['' => ''] ,$lpoins);
            $view->with('lpoinItems', $lpoins);
        });
        // for invoice request
        View::composer(['invoice_requests.fields'], function ($view) {

            $lpoinItems = Lpoin::with('quotation.invoices')->get();
            $lpoins = [];

            foreach ($lpoinItems as $lpoin) {

                $quotation = $lpoin->quotation;

                if (!$quotation) continue;

                // SUM only completed invoices (status = 1)
                $completedInvoicesTotal = $quotation->invoices()
                    ->where('status', 1)  // completed invoices
                    ->sum('amount');

                // Remaining = CV - completed invoices total
                $remaining = $quotation->amount - $completedInvoicesTotal;
                if ($remaining <= 0) continue;

                // Dropdown label
                $lpoins[$lpoin->quotation_id] =
                    'Ref #: '.$lpoin->ref_no.
                    ' ( Name: '.$quotation->name.
                    ' , CV: '.$quotation->amount.
                    ' , Remaining: '.$remaining.' )';
            }

            // Add blank option at the top
            $lpoins = array_replace(['' => ''], $lpoins);

            $view->with('lpoinItems', $lpoins);
        });
        View::composer(['quotations.fields','receipts.fields'], function ($view) {
            $companyItems = Company::pluck('name','id')->toArray();
            $view->with('companyItems', $companyItems);
        });
        View::composer(['petty_cashes.fields'], function ($view) {
            $lpoinItems = Lpoin::with('quotation')->get();
            $lpoins = [];
            foreach ($lpoinItems as $key => $lpoin) {
                $lpoins[$lpoin->quotation_id] =  'Lpoin Ref #:  '.$lpoin->ref_no.' ( Quotation: '.$lpoin->quotation->name.' )';
            }
            $lpoins = array_replace(['' => ''] ,$lpoins);
            $view->with('projectItems', $lpoins);
        });
        View::composer(['roles.fields'], function ($view) {
            $permissions = Permission::all();

            if( sizeof($permissions) > 0)
                $view->with('permissions', $permissions);
            else
                $view->with('permissions', []);
        });

        View::composer(['users.fields'], function ($view) {
            $roles = Role::all();

            if( sizeof($roles) > 0)
                $view->with('roles', $roles);
            else
                $view->with('roles', []);

            $view->with('stafProfiles', StafProfile::orderBy('name')->get());
        });
    }
}
