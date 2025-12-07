<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\AuthUserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function store(StoreUserRequest $request){
        if($request-> validated()){
            User::create($request->validated());
            return response()->json(['message'=>'account created successfully']);
        }
    }
    public function auth(AuthUserRequest $request){
        if($request-> validated()){
          $user =  User::whereEmail($request->email)->first();
          if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json(['message'=>'The provided credentials are incorrect.'], 401);
          }
          else{
             return response()->json([
                'user' => UserResource::make($user),
                'access_token'=>$user->createToken('auth_token')->plainTextToken
             ]);
          }
           
        }
    }
    public function logout(Request $request){
        
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'logged out successfully']);
    }
}
