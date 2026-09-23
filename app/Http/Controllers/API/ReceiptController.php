<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receipt;


class ReceiptController extends Controller
{
    public function index(Request $request, Receipt $receipt){
        if ($request->anyFilled([ 'id','date','from_date','to_date','transaction_type','payment_type','status'])) {
            $receipt= $receipt->newQuery()->with('transaction_payment_type','transactionable.quotation.company','account')->where('type','=','Receipt');
            if($request->filled('id')){
                $data= $receipt->where('id','=',$request->id)->get();
                if($data->isEmpty()){
                    return [];
                }
                else{
                    return $data;
                }
            }
            if($request->filled('date')){
                $receipt->whereDate('date_time','=',$request->date);
            }
            if($request->filled('from_date')){
                $receipt->whereDate('date_time','>=',$request->from_date);
            }
            if($request->filled('to_date')){
                $receipt->whereDate('date_time','<=',$request->to_date);
            }
            if($request->filled('transaction_type')){
                $receipt->where('transaction_type','=',$request->transaction_type);
            }
            if($request->filled('payment_type')){
                $receipt->where('payment_type','=',$request->payment_type);
            }
            if($request->filled('status')){
                $receipt->where('status','=',$request->status);
            }
            $data = $receipt->get();
            if($data->isEmpty()){
                return [];
            }
            else{
              return $data;  
            }
        }
        else{
            return $receipt::where('type','=','Receipt')->with('transaction_payment_type','transactionable.quotation.company','account')->get();
        }
    }
}
