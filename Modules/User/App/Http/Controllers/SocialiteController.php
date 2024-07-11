<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Modules\User\App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the OAuth provider.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\Response
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return Redirect::to('/login')->withErrors('Unable to login using ' . $provider . '. Please try again.');
        }

        // Check if the user already exists
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Create a new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('access_token', $token, 60 * 24 * 7, secure: true); // 7 day

        // Log the user in
        Auth::login($user, true);

        return redirect()->away(env('FRONTEND_URL'))->withCookie($cookie);
    }
}
