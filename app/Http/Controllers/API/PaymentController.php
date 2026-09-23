<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(Request $request,Payment $payment){
        if ($request->anyFilled([ 'id','date','from_date','to_date','transaction_type','payment_type','status'])) {
            $payment= $payment->newQuery()->with('transaction_payment_type','transactionable')->where('type','=','Payment');
            if($request->filled('id')){
                $data= $payment->where('id','=',$request->id)->get();
                if($data->isEmpty()){
                    return [];
                }
                else{
                    return $data;
                }
            }
            if($request->filled('date')){
                $payment->whereDate('date_time','=',$request->date);
            }
            if($request->filled('from_date')){
                $payment->whereDate('date_time','>=',$request->from_date);
            }
            if($request->filled('to_date')){
                $payment->whereDate('date_time','<=',$request->to_date);
            }
            if($request->filled('transaction_type')){
                $payment->where('transaction_type','=',$request->transaction_type);
            }
            if($request->filled('payment_type')){
                $payment->where('payment_type','=',$request->payment_type);
            }
            if($request->filled('status')){
                $payment->where('status','=',$request->status);
            }
            $data= $payment->get();
            if($data->isEmpty()){
                return [];
            }
            else{
              return $data;  
            }
        }
        else{
            return $payment::where('type','=','Payment')->with('transaction_payment_type','transactionable')->get();
        }
    }
}
