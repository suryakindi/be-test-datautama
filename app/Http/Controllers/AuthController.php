<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function storelogin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('token-name')->plainTextToken;
            return response([
                            'email' => $user->email,
                            'token' => $token,
                            
                            ], 200);
        }else{
            return response('Gagal Login', 401);
        }
    
        
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($request){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            return response([
                'message'=>'Sukses Register',
                'email' => $user->email,
                ], 200);
        }else{
            return response('Invalid Request', 200);
        }        

    }

    public function logout(){
        $user = Auth::user(); // Retrieve the authenticated user
    
        if($user->tokens != null){
            foreach ($user->tokens as $token) {
                $token->delete();
            }
           
            return response('Succes Logout', 200);
            
        }else{
            return response('Not Authorized', 401);
           
        }

       

        
    }
}
