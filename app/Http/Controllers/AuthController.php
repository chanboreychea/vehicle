<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::create(
            [
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
            ]
        );

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return Response($response, 201);
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        //check email
        $user = User::where('name', $fields['name'])->first();

        //check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return Response([
                'message' => 'wrong password'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user->name,
            'token' => $token
        ];

        return Response($response, 201);
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        // $request->tokens()->where('id', $user->id)->delete();

        return ['message' => 'logout'];
    }
}
