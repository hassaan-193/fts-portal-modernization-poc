<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    public function __construct(ResController $response)
    {
        $this->response = $response;
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response->respondValidationError("Please Fill The Required Fields.");
        }

        $status = \Auth::attempt(['email' => $request->email, 'password' => $request->password]);

        if($status){
            return $this->response->respond(\Auth::user(), []);
        }else{
            return $this->response->respondValidationError("Incoreect email or password.");
        }
    }

    public function update_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response->respondValidationError("Please Fill The Required Fields.");
        }

        $user = new \App\User;

        $user->where('id',$request->user_id)
        ->update([
            'firebase_token' => $request->token
        ]);

        if($user){
            return $this->response->respond($user, []);
        }else{
            return $this->response->respondValidationError("Unable to update request.");
        }
    }
}
