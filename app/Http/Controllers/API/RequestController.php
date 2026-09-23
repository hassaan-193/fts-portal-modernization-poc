<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class RequestController extends Controller
{
    public function __construct(ResController $response)
    {
        $this->response = $response;
    }

    public function get_request_forms(Request $request)
    {
        $request_forms = \App\Models\RequestForm::query()->whereStatus(0);

        if($request->has('search')){
            $request_forms = \App\Models\RequestForm::whereLike('name', $request->get('search'))
            ->orWhereLike('date_time',$request->get('search'));
        }

        $request_forms = \App\Http\Resources\RequestResource::collection($request_forms->orderBy('id','desc')->get());
        if($request_forms){
            return $this->response->respond($request_forms, []);
        }else{
            return $this->response->respond([], 'No data found.');
        }
    }

    public function get_updated_request_forms(Request $request)
    {
        $request_forms = \App\Models\RequestForm::query()->where('status', '!=', 0);

        if($request->has('search')){
            $request_forms = \App\Models\RequestForm::whereLike('name', $request->get('search'))
            ->orWhereLike('date_time',$request->get('search'));
        }

        $request_forms = \App\Http\Resources\RequestResource::collection($request_forms->orderBy('id','desc')->get());
        if($request_forms){
            return $this->response->respond($request_forms, []);
        }else{
            return $this->response->respond([], 'No data found.');
        }
    }

    public function update_request_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
            'comments' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response->respondValidationError("Please Fill The Required Fields.");
        }

        $request_form = \App\Models\RequestForm::find($request->id);
        if($request_form){
            $request_form->update([
                'status' => $request->status,
                'comments' => $request->comments,
            ]);
            return $this->response->respond($request_form, []);
        }else{
            return $this->response->respondValidationError("Unable to update request.");
        }
    }
}
