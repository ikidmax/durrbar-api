<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\User\App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->only('name', 'first_name', 'last_name', 'email', 'password', 'password_confirmation'), [
            'name' => ['required', 'min:2', 'max:50', 'string'],
            'first_name' => ['required', 'min:2', 'max:50', 'string'],
            'last_name' => ['required', 'min:2', 'max:50', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'max:255', 'confirmed', 'string'],
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);

        $input = $request->only('name', 'first_name', 'last_name', 'email', 'password');

        $input['password'] = Hash::make($request['password']);

        $user = User::create($input);

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('access_token', $token, 60 * 24 * 7); // 7 day
        
        return response(['message' => 'Success', 'data' => ['user' => $user]], Response::HTTP_OK)->withCookie($cookie);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->only('email', 'password'), [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'min:6', 'max:255', 'string'],
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = $request->user();
            $user->photo = Storage::url($user->photo);
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('access_token', $token, 60 * 24 * 7); // 7 day
            return response(['message' => 'Success', 'data' => ['user' => $user]], Response::HTTP_OK)->withCookie($cookie);

            // return response()->json($data, 200);
        }
        
        return response(['message' => 'Invalid credentials!'], Response::HTTP_UNAUTHORIZED);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->photo = Storage::url($user->photo);

        return response(['data' => ['user' => $user]], Response::HTTP_OK);
    }

    public function logout()
    {
        $cookie = Cookie::forget('access_token');

        return response(['message' => 'Success'], Response::HTTP_OK)->withCookie($cookie);
    }
}
