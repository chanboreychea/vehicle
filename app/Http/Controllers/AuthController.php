<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required','email',Rule::unique('users', 'email')],
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        $user = User::create(
            [
                'name' => $request['name'],
                'email' => $request->email,
                'password' => bcrypt($request['password']),
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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError(self::BAD_REQUEST, $validator->errors()->messages());
        }

        //check email
        $user = User::where('name', $request['name'])->first();

        //check password
        if (!$user || !Hash::check($request['password'], $user->password)) {
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
