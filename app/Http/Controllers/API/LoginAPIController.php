<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request) {
        $validation = Validator::make($request->all(),[
            'email'   => 'email|required',
            'password' => 'required',
        ]);
        if($validation->fails())
        {
            $errors = $validation->errors();
            return $this->sendError($errors->first(),422);
        }
        $credentials = request(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->sendError('Not Authorized',401);
        }

        $user=auth('api')->user();
        $user->token=$token;
        $user->token_expires= auth('api')->factory()->getTTL() * 60;

        return $this->sendResponse($token, 200,'Login successfully');
       // return $this->sendResponse($user, 200,'Logined successfully');
    }

    public function token(){
        $token = JWTAuth::getToken();
        if(!$token){
            throw new BadRequestHtttpException('Token not provided');
        }
        try{
            $token = JWTAuth::refresh($token);
        }catch(TokenInvalidException $e){
            throw new AccessDeniedHttpException('The token is invalid');
        }
        return $this->sendResponse($token, 200,'Login successfully');
    }
    public function logut(){
        auth()->logout();
        return $this->sendResponse([], 200,'Logined successfully');
    }
}
