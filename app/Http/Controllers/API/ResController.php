<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Response;
use \Illuminate\Http\Response as Res;

class ResController extends Controller{
    /**
     * @var int
     */
    protected $statusCode = Res::HTTP_OK;
    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    /**
     * @param $message
     * @return json response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respond($data,$message){
        return Response::json([
            "cmd" => request()->path(),
            "success" => true,
            "message" => $message,
            "code" => Res::HTTP_OK,
            "data" =>  $data
        ]);
    }


    public function respondValidationError($error_message){
        return Response::json([
            "cmd" => request()->path(),
            "success" => false,
            "message" => $error_message,
            "code" => Res::HTTP_UNPROCESSABLE_ENTITY,
            "data" =>  null
        ]);
    }

    public function respondWithUnAuthorizedError($message){
        return Response::json([
            "cmd" => request()->path(),
            "success" => false,
            "message" => $message,
            "code" => Res::HTTP_UNAUTHORIZED,
            "data" =>  null
        ]);
    }

     public function respondInternalError($message){
        return Response::json([
            "cmd" => request()->path(),
            "success" => false,
            "message" => $message,
            "code" => Res::HTTP_INTERNAL_SERVER_ERROR,
            "data" =>  null
        ]);
    }

    public function respondNotFound($message = 'Not Found!'){
        return Response::json([
            "cmd" => request()->path(),
            "success" => false,
            "message" => $message,
            "code" => Res::HTTP_NOT_FOUND,
            "data" =>  null
        ]);
    }
}
