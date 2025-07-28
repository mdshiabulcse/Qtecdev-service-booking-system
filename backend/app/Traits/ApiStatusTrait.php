<?php

namespace App\Traits;


trait ApiStatusTrait
{
    public $successStatus = 200;
    public $failureStatus = 500;
    public $validationFailureStatus = 400;
    public $notAllowedStatus = 401;
    public $notFoundStatus = 404;
    public $alreadyFoundStatus = 409;
    public $accessibleStatus = 209;

    public function successApiResponse($response){
        return response()->json($response,$this->successStatus);
    }

    public function failureApiResponse($response){
        return response()->json($response,$this->failureStatus);
    }

    public function validationfailureApiResponse($response){
        return response()->json($response,$this->validationFailureStatus);
    }

    public function notAllowedApiResponse($response){
        return response()->json($response,$this->notAllowedStatus);
    }

    public function notFoundApiResponse($response){
        return response()->json($response,$this->notFoundStatus);
    }

    public function alreadyFoundApiResponse($response){
        return response()->json($response,$this->alreadyFoundStatus);
    }

    public function accessibleApiResponse($response){
        return response()->json($response,$this->accessibleStatus);
    }
}
