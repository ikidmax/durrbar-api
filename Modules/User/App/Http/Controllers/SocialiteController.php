<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class SocialiteController extends Controller
{
    public function handleProviderCallback(Request $request)
    {
        $validator = Validator::make($request->only('provider', 'access_provider_token'), [
            'provider' => ['required', 'string'],
            'access_provider_token' => ['required', 'string']
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);

        $provider = $request->provider;

        $validated = $this->validateProvider($provider);

        if (!is_null($validated)) return $validated;

        $providerUser = Socialite::driver($provider)->userFromToken($request->access_provider_token);

        $user = User::firstOrCreate(
            [
                'email' => $providerUser->getEmail()
            ],
            [
                'name' => $providerUser->getName(),
            ]
        );

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('access_token', $token, 60 * 24 * 7); // 7 day

        return response(['message' => 'Success', 'data' => ['user' => $user]], Response::HTTP_OK)->withCookie($cookie);
    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'google'])) {
            return response()->json(["message" => 'You can only login via facebook or google account'], Response::HTTP_BAD_REQUEST);
        }
    }
}
