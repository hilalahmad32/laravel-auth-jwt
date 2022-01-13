<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|between:4,30',
            'email'=>'required|email|max:100|unique:users',
            'password'=>'required|string|min:4'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }else{
            $user=new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $result=$user->save();
            if($result){
                return response()->json([
                    'message'=>'User Add Successfully',
                    'user'=>$user
                ]);
            }
        }

    }

    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|string|min:4'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        if(!$token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])){
            return response()->json(['error'=>'UnAuth'],401);
        }else{
            return $this->createToken($token);
        }
    }

    public function createToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'user'=>auth()->user()
        ]);
    }
}
