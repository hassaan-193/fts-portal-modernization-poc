<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\DriverDues;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Exception;
use App\Models\Invoice;
use App\Models\PaymentInvoice;
use App\Models\Lookup;
use Illuminate\Support\Facades\DB;
use Illuminatech\Balance\Facades\Balance;
use App\Models\Account;
use App\Models\Transaction;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('Lpoouts-QR-Viewer')) {
            return redirect()->route('lpoouts.index');
        }

        if (auth()->user()->hasRole('Staff Requester') && !auth()->user()->can('stafprofile')) {
            return redirect()->route('own_requests.index');
        }

        $exp_date=Carbon::now()->addMonth()->format('Y-m-d');
        return view('home',[
            'companies' => \App\Models\Company::count(),
            'projects' => \App\Models\Project::count(),
            'quotations' => \App\Models\Quotation::count(),
            'users' => \App\User::count(),
            'requests' => \App\Models\InvoiceRequest::whereStatus(0)->count(),
            'invoices' => \App\Models\Invoice::whereStatus(0)->count(),
            'receipts' => \App\Models\Receipt::with('transaction_payment_type','transactionable.quotation.company')
                            ->where('type','Receipt')
                            ->whereNotNull('transactionable_id')
                            ->orderBy('id','desc')
                            ->where('status',0)
                            ->get()
                            ,
            'cheques' => \App\Models\Cheque::with('transaction_payment_type')
                            ->whereHas('transaction_payment_type',function($q){
                                $q->where('name','Cheque');
                            })
                            ->where('status',0)
                            ->orderBy('id','desc')
                             ->get()
                             ,
            'documents' => \App\Models\Document::where('date','<',$exp_date)
                             ->orderBy('id','desc')
                             ->get()
                             ,
'staff_profiles' => \App\Models\StafProfile::where(function($q) use ($exp_date) {
        $q->where('passport_expiry', '<', $exp_date)
        ->orWhere('visa_expiry', '<', $exp_date)
        ->orWhere('emirates_id_expiry', '<', $exp_date)
        ->orWhere('labor_card_expiry', '<', $exp_date)
        ->orWhere('driver_permit_expiry', '<', $exp_date);
    })
    ->where('exclude_from_expiry', 0)
    ->orderBy('id', 'desc')
    ->get(),

            'overdue_invoices' => \App\Models\Invoice::with('quotation.company:id,name')->where('end_date', '<', now()->subDays(30))
                            ->orderBy('id','desc')
                            ->where('status',0)
                            ->get()
                            ,
            'exp_date' => $exp_date,
        ]);
    }

    public static function importData()
    {
        try {
            DB::beginTransaction();
    
            $processed = 0;
            $no_transaction = 0;
            $cheque_not_cleared = 0;
    
            $invoices = Invoice::with('transaction')->get();
    
            $salesAccount = Account::where('code', 'sales')->firstOrFail();
            $receivableAccount = Account::where('code', 'account_receivable')->firstOrFail();
            $vatOutput = Account::where('code', 'vat_out')->first();
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());
    
            $paymentTypes = Lookup::pluck('name', 'id');
    
            foreach ($invoices as $invoice) {
                $invoiceId = $invoice->id;
                $total_amount = $invoice->total_amount;
                $amount = $invoice->amount;
                $vat = $invoice->vat;
                $invoiceNo = $invoice->invoice_no;
                $invoiceCreatedAt = $invoice->created_at;
                $company_name = $invoice->quotation->company->name ?? '';
    
                $processed++;
    
                // Debit: Accounts Receivable
                $resultID = $balance->increase($receivableAccount->id, $total_amount, [
                    'reference_type' => \App\Models\Invoice::class,
                    'reference_id' => $invoiceId,
                    'transaction_detail' => $company_name .' - '.$invoiceNo,
                ]);
                self::setTransactionTimestamp($resultID, $invoiceCreatedAt);
        
                // Credit: Sales
                $resultID = $balance->decrease($salesAccount->id, $amount, [
                    'reference_type' => \App\Models\Invoice::class,
                    'reference_id' => $invoiceId,
                    'transaction_detail' => $company_name .' - '.$invoiceNo,
                ]);
                self::setTransactionTimestamp($resultID, $invoiceCreatedAt);

                // Credit: Vat Output
                $resultID = $balance->decrease($vatOutput->id, $vat, [
                    'reference_type' => \App\Models\Invoice::class,
                    'reference_id' => $invoiceId,
                    'transaction_detail' => $company_name .' - '.$invoiceNo
                ]);
                self::setTransactionTimestamp($resultID, $invoiceCreatedAt);

                // If there's an associated transaction (receipt voucher)
                if ($invoice->transaction) {
                    $transaction = $invoice->transaction;
                    $transactionCreatedAt = $transaction->date_time;

                    $paymentTypeName = $paymentTypes[$transaction->payment_type] ?? null;
    
                    $shouldApplyReceipt = $paymentTypeName !== 'Cheque' || ($paymentTypeName === 'Cheque' && $transaction->status);
                    if ($shouldApplyReceipt) {
                        // Debit: Actual Account
                        $resultID = $balance->increase($transaction->account_id, $amount,[
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Receipt::class,
                            'transaction_detail' => $company_name .' - '.$invoiceNo
                        ]);
                        self::setTransactionTimestamp($resultID, $transactionCreatedAt);

                        // Credit: Accounts Receivable
                        $resultID = $balance->decrease($receivableAccount->id, $amount, [
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Receipt::class,
                            'transaction_detail' => $company_name .' - '.$invoiceNo
                        ]);
                        self::setTransactionTimestamp($resultID, $transactionCreatedAt);

                    } else {
                        $cheque_not_cleared++;
                    }
                } else {
                    $no_transaction++;
                }
            }
    
            DB::commit();
    
            return [
                'processed' => $processed,
                'no_transaction' => $no_transaction,
                'cheque_not_cleared' => $cheque_not_cleared,
            ];
    
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public static function importDataPaymentInvoices()
    {
        try {
            DB::beginTransaction();
    
            $processed = 0;
            $no_transaction = 0;
            $cheque_not_cleared = 0;
    
            $invoices = PaymentInvoice::with('transaction')->get();
    
            $expenseAccount = Account::where('code', 'material_expense')->first();
            $payableAccount = Account::where('code', 'account_payable')->first();
            $vatInput = Account::where('code', 'vat_in')->first();
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());
    
            $paymentTypes = Lookup::pluck('name', 'id');
    
            foreach ($invoices as $invoice) {
                $invoiceId = $invoice->id;
                $total_amount = $invoice->amount + $invoice->vat;
                $amount = $invoice->amount;
                $vat = $invoice->vat;
                $invoiceNo = $invoice->invoice_no;
                $invoiceCreatedAt = $invoice->created_at;
                $vendor_name = ($invoice->lpoout ? ($invoice->lpoout->vendor->name ?? '') : ($invoice->vendor->name ?? ''));
    
                $processed++;
    
                if (($amount + $vat) != $total_amount) {
                    throw new \Exception("Invoice ID $invoiceId is unbalanced: amount + vat != total");
                }

                // Credit: Accounts Payable
                $resultID = $balance->decrease($payableAccount->id, $total_amount, [
                    'reference_type' => \App\Models\PaymentInvoice::class,
                    'reference_id' => $invoiceId,
                    'transaction_detail' => $vendor_name .' - '.$invoiceNo,
                ]);
                self::setTransactionTimestamp($resultID, $invoiceCreatedAt);
        
                // Debit: Materials Expense
                $resultID = $balance->increase($expenseAccount->id, $amount, [
                    'reference_type' => \App\Models\PaymentInvoice::class,
                    'reference_id' => $invoiceId,
                    'transaction_detail' => $vendor_name .' - '.$invoiceNo,
                ]);
                self::setTransactionTimestamp($resultID, $invoiceCreatedAt);

                // Debit: Vat Input
                if($vat){
                    $resultID = $balance->increase($vatInput->id, $vat, [
                        'reference_type' => \App\Models\PaymentInvoice::class,
                        'reference_id' => $invoiceId,
                        'transaction_detail' => $vendor_name .' - '.$invoiceNo
                    ]);
                    self::setTransactionTimestamp($resultID, $invoiceCreatedAt);
                }

                // If there's an associated transaction (payment voucher)
                if ($invoice->transaction) {
                    $transaction = $invoice->transaction;
                    $transactionCreatedAt = $transaction->date_time;

                    $paymentTypeName = $paymentTypes[$transaction->payment_type] ?? null;
    
                    $shouldApplyReceipt = $paymentTypeName !== 'Cheque' || ($paymentTypeName === 'Cheque' && $transaction->status);
                    if ($shouldApplyReceipt) {
                        // Credit: Actual Account
                        $resultID = $balance->decrease($transaction->account_id, $total_amount,[
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Payment::class,
                            'transaction_detail' => $vendor_name .' - '.$invoiceNo
                        ]);
                        self::setTransactionTimestamp($resultID, $transactionCreatedAt);

                        // Debit: Accounts Payable
                        $resultID = $balance->increase($payableAccount->id, $total_amount, [
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Payment::class,
                            'transaction_detail' => $vendor_name .' - '.$invoiceNo
                        ]);
                        self::setTransactionTimestamp($resultID, $transactionCreatedAt);

                    } else {
                        $cheque_not_cleared++;
                    }
                } else {
                    $no_transaction++;
                }
            }
    
            DB::commit();
    
            return [
                'processed' => $processed,
                'no_transaction' => $no_transaction,
                'cheque_not_cleared' => $cheque_not_cleared,
            ];
    
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
    
    /**
     * Updates the created_at of a balance transaction.
     */
    protected static function setTransactionTimestamp($transaction, $timestamp)
    {
        if ($transaction) {
            DB::table('balance_transactions')
                ->where('id', $transaction)
                ->update(['created_at' => $timestamp]);
        }
    }
    
    public static function migrateTransactionReferences()
    {
        $map = [
            'Invoice' => \App\Models\Invoice::class,
            'Receipt' => \App\Models\Receipt::class
        ];

        Transaction::whereNotNull('data')->chunkById(100, function ($transactions) use ($map){
            foreach ($transactions as $transaction) {
                $data = is_array($transaction->data) ? $transaction->data : json_decode($transaction->data, true);
        
                $typeKey = $data['type'] ?? null;
                
                // Disable timestamp updates
                $transaction->timestamps = false;
                
                $transaction->reference_type = $map[$typeKey] ?? null;
                $transaction->reference_id = $data['transaction_id'] ?? null;
                $transaction->save();
            }
        });

        return true;
    }

    public static function setCompanyNameInTransactionDetail()
    {
        $map = [
            'Invoice' => \App\Models\Invoice::class,
            'Receipt' => \App\Models\Receipt::class
        ];

        Transaction::whereNotNull('data')->chunkById(100, function ($transactions) use ($map){
            foreach ($transactions as $transaction) {
                $data = is_array($transaction->data) ? $transaction->data : json_decode($transaction->data, true);
        
                $companyName = $transaction->company->name ?? '';
                if (!empty($data['transaction_detail'])) {
                    $data['transaction_detail'] = $companyName . ' - ' . $data['transaction_detail'];
                }
        
                // Clean unwanted keys
                unset($data['transaction_id'], $data['type']);

                // Assign back to model
                $transaction->data = $data;  
              
                // Disable timestamp updates
                $transaction->timestamps = false;
                
                $transaction->save();
            }
        });

        return true;
    }

    public static function updatePendingInvoicesAndAddVatOutput()
    {
        try {
            DB::beginTransaction();
    
            $vatOutput = Account::where('code', 'vat_out')->first();
            $processed = 0;
    
            // Fetch invoices that are either pending or completed
            $invoices = Invoice::whereIn('status', [0, 1])->get();
    
            foreach ($invoices as $invoice) {
                $company_name = $invoice->quotation->company->name ?? '';
                $invoiceNo = $invoice->invoice_no;
                $invoiceCreatedAt = $invoice->created_at;
    
                $shouldProcessVat = false;
    
                if ($invoice->status == 0) {
                    // ✅ Case 1: Pending invoice
                    $shouldProcessVat = true;
                } elseif ($invoice->status == 1) {
                    // ✅ Case 2: Completed invoice
                    $receiptVoucher = $invoice->transaction()->first();
    
                    if ($receiptVoucher) {
                        if ($receiptVoucher->status == 0 && $receiptVoucher->payment_type == 4) {
                            // Pending Cheque
                            $shouldProcessVat = true;
                        } elseif ($receiptVoucher->status == 1) {
                            // if($invoiceNo == 'IN_FTS_1023_20241016'){
                            //     // Cleared voucher → adjust VAT transaction timestamp
                            //     DB::table('balance_transactions')
                            //     ->where('reference_id', $receiptVoucher->id)
                            //     ->where('account_id', $vatOutput->id)
                            //     ->update(['reference_type' => \App\Models\Invoice::class, 'reference_id' => $invoice->id, 'created_at' => $invoiceCreatedAt]);
                                

                            //     $transactions = DB::table('balance_transactions')
                            //     ->where('reference_id', $invoice->id)
                            //     ->where('reference_type', \App\Models\Invoice::class)
                            //     ->get();

                            //     // dd($transactions->toArray());
                            //     $processed++;
                            // }

                            DB::table('balance_transactions')
                                ->where('reference_type', \App\Models\Receipt::class)
                                ->where('reference_id', $receiptVoucher->id)
                                ->where('account_id', $vatOutput->id)
                                ->update(['reference_type' => \App\Models\Invoice::class, 'reference_id' => $invoice->id, 'created_at' => $invoiceCreatedAt]);
                        }
                    }
                }
    
                if ($shouldProcessVat) {
                    $balance = new \App\Services\CustomBalanceManager(DB::connection());
    
                    $tax_id = $balance->decrease($vatOutput->id, $invoice->vat, [
                        'reference_type' => \App\Models\Invoice::class,
                        'reference_id' => $invoice->id,
                        'transaction_detail' => $company_name . ' - ' . $invoiceNo
                    ]);
    
                    self::setTransactionTimestamp($tax_id, $invoiceCreatedAt);
    
                    $processed++;
                }
            }
    
            DB::commit();
    
            return [
                'processed' => $processed,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public static function updatePendingPaymentInvoicesAndAddVatInput()
    {
        try {
            DB::beginTransaction();
            
            // Fetch pending invoices (adjust if your status column name differs)
            $invoices = PaymentInvoice::where('status', 0)->get();
            $vatInput = Account::where('code', 'vat_in')->first();
            $processed = 0;

            foreach ($invoices as $invoice) {
                $vendor_name = ($invoice->lpoout ? ($invoice->lpoout->vendor->name ?? '') : ($invoice->vendor->name ?? ''));
                $invoiceNo = $invoice->invoice_no;
                $invoiceCreatedAt = $invoice->created_at;

                $shouldProcessVat = false;
    
                if ($invoice->status == 0) {
                    // ✅ Case 1: Pending invoice
                    $shouldProcessVat = true;
                } elseif ($invoice->status == 1) {
                    // ✅ Case 2: Completed invoice, check for pending payment voucher with cheque
                    $paymentVoucher = $invoice->transaction()->where('status', 0)->first();
                    if ($paymentVoucher && $paymentVoucher->payment_type == 4) {
                        $shouldProcessVat = true;
                    }
                }

                if ($shouldProcessVat) {
                    $balance = new \App\Services\CustomBalanceManager(DB::connection());
                    if($invoice->vat){
                        $tax_id = $balance->increase($vatInput->id, $invoice->vat, [
                            'reference_type' => \App\Models\PaymentInvoice::class,
                            'reference_id' => $invoice->id,
                            'transaction_detail' => $vendor_name .' - '.$invoiceNo
                        ]);
                        self::setTransactionTimestamp($tax_id, $invoiceCreatedAt);
                    }
                }
                $processed++;
            }

            DB::commit();

            return [
                'processed' => $processed,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
}
