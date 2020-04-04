<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use JWTFactory;
use JWTAuth;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class RegisterAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        $validation =  Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validation->fails()) {
            return response()->json(['errorMessage' => $validation->errors()->all()], 400);
        }

       User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);

        $user = User::first();
        $token = JWTAuth::fromUser($user);
        return $this->sendResponse($user, 200,'Registration Success');
   }
}
