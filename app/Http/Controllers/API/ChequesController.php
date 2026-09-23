<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cheque;

class ChequesController extends Controller
{
    public function index(Request $request,Cheque $cheque){
        if ($request->anyFilled([ 'clearance_date','date','from_date','to_date','status'])) {
            $cheque= $cheque->newQuery()->with('transaction_payment_type')->whereHas('transaction_payment_type',function($q){
                $q->where('name','Cheque');
            });
            if ($request->filled('status')) {
                $cheque->where('status','=',$request->status);
            }
            else{
                $cheque->where('status',0);
            }
            if ($request->filled('clearance_date')) {
                $cheque->where('clearance_date','=',$request->clearance_date);
            }
            if($request->filled('date')){
                $cheque->whereDate('date_time','>=',$request->date);
            }
            if($request->filled('from_date')){
                $cheque->whereDate('date_time','>=',$request->from_date);
            }
            if($request->filled('to_date')){
                $cheque->whereDate('date_time','<=',$request->to_date);
            }
            $data = $cheque->get();
            if($data->isEmpty()){
                return [];
            }
            else{
              return $data;  
            }
        }
        else{
            $cheque= $cheque->newQuery()->with('transaction_payment_type')->whereHas('transaction_payment_type',function($q){
                $q->where('name','Cheque');
            })->where('status',0)->get();
            return $cheque;
        }
    }
}
