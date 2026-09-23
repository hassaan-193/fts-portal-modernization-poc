<?php

namespace App\Repositories;

use App\Models\PettyCash;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminatech\Balance\Facades\Balance;

/**
 * Class PettyCashRepository
 * @package App\Repositories
 * @version September 15, 2020, 11:00 pm PKT
*/

class PettyCashRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'account_id',
        'user_id',
        'description'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PettyCash::class;
    }

    public function create($input)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            $input['vat'] = $input['vat'] ? $input['amount'] * config('enum.tax_rate') : 0;
            $input['total_amount'] = $input['amount'] + $input['vat'];

            $petty_cash = $this->model->newInstance($input);
            $petty_cash->save();

            //payment type
            $payment_type = \App\Models\Lookup::where('name','Cash')->first();

            $data = [
                'model_id' => $petty_cash->id,
                'model_type' => "PettyCash",
                'transaction_type' => $petty_cash->payment_type->name,
                'transaction_payment_type' => $payment_type->name,
                'total_amount' => $petty_cash->total_amount,
            ];

            if($petty_cash->payment_type->name == 'Receipt'){
                Balance::transfer($petty_cash->account_id, $petty_cash->petty_cash_account_id ,$petty_cash->total_amount,$data);

                //update user balance
                if($input['trans_type'] == 'Advance')
                    $petty_cash->user()->update([
                        'balance' => $petty_cash->user->balance - $petty_cash->total_amount
                    ]);
            }
            else{
                if(isset($input['is_user_balance']) && $input['is_user_balance']){
                    $account = \App\Models\Account::whereType('Advance')->first();
                    if($account){
                        Balance::transfer($account->id, $petty_cash->account_id ,$petty_cash->total_amount,$data);

                        //update user balance
                        $petty_cash->user()->update([
                            'balance' => $petty_cash->user->balance - $petty_cash->total_amount
                        ]);

                        //update advance status
                        $petty_cash->update(['is_user_deduction' => 1]);
                    }else {
                        return false;
                    }
                }else{
                    Balance::transfer($petty_cash->petty_cash_account_id ,$petty_cash->account_id, $petty_cash->total_amount,$data);

                    //update user balance
                    if($petty_cash->account->name = 'Advance'){
                        $petty_cash->user()->update([
                            'balance' => $petty_cash->user->balance + $petty_cash->total_amount
                        ]);

                        //update advance status
                        $petty_cash->update(['is_advance' => 1]);
                    }
                }
            }

            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function updateRecord($petty_cash, $input, $id)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            if($petty_cash->payment_type->name == 'Received')
                Balance::transfer($petty_cash->petty_cash_account_id, $petty_cash->account_id ,$petty_cash->total_amount);
            else
                Balance::transfer($petty_cash->account_id ,$petty_cash->petty_cash_account_id, $petty_cash->total_amount);

            $input['vat'] = $input['vat'] ? $input['amount'] * config('enum.tax_rate') : 0;
            $input['total_amount'] = $input['amount'] + $input['vat'];

            $petty_cash->fill($input)->save();

            //payment type
            $payment_type = \App\Models\Lookup::where('name','Cash')->first();

            $data = [
                'model_id' => $petty_cash->id,
                'model_type' => "PettyCash",
                'transaction_type' => $petty_cash->payment_type->name,
                'transaction_payment_type' => $payment_type->name,
                'total_amount' => $petty_cash->total_amount,
            ];

            if($petty_cash->payment_type()->first()->name == 'Received')
                Balance::transfer($petty_cash->account_id, $petty_cash->petty_cash_account_id ,$petty_cash->total_amount,$data);
            else
                Balance::transfer($petty_cash->petty_cash_account_id ,$petty_cash->account_id, $petty_cash->total_amount,$data);


            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function delete($id)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            $query = $this->model->newQuery();
            $petty_cash = $query->findOrFail($id)->load('payment_type');

            //Clear balance
            if($petty_cash->payment_type->name == 'Received')
                Balance::transfer($petty_cash->petty_cash_account_id, $petty_cash->account_id ,$petty_cash->total_amount);
            else{

                if($petty_cash->is_advance){
                    Balance::transfer($petty_cash->account_id ,$petty_cash->petty_cash_account_id, $petty_cash->total_amount);

                    // update user balance
                    $petty_cash->user()->update([
                        'balance' => $petty_cash->user->balance - $petty_cash->total_amount
                    ]);
                }elseif ($petty_cash->is_user_deduction){
                    $account = \App\Models\Account::whereType('Advance')->first();
                    if($account){
                        Balance::transfer($petty_cash->account_id ,$account->id, $petty_cash->total_amount);

                        // update user balance
                        $petty_cash->user()->update([
                            'balance' => $petty_cash->user->balance + $petty_cash->total_amount
                        ]);
                    }else
                        return false;
                }else{
                    Balance::transfer($petty_cash->account_id ,$petty_cash->petty_cash_account_id, $petty_cash->total_amount);
                }
            }

            //delete invoice
            $petty_cash->delete();
            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }
}
