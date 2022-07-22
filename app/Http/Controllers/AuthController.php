<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{    
    /**
     * For user registeration
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        // User::create($request->all());
        if (User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password'])
            ])
        ) {
            return response([
                'message' => 'Registered successfully!'
            ], 201);
        }
    }
    
    /**
     * For user to request new token
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);


        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'Authentication failed'
            ], 401);
        }

        $generated_token = $request->user()->createToken('AccessKey')->plainTextToken;

        return response([
            'message' => 'Logged in successfully',
            'accessKey' => $generated_token
        ], 200);
    }
    
    /**
     * For user logout and delete token from database
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $action = $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Access revoked successfully!'
        ]);
    }
}
