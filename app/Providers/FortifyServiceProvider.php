<?php

namespace App\Providers;



use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\{LoginResponse, LogoutResponse, PasswordUpdateResponse, RegisterResponse, ProfileInformationUpdatedResponse, TwoFactorConfirmedResponse, TwoFactorDisabledResponse, TwoFactorEnabledResponse};
use Modules\User\App\Actions\Fortify\{CreateNewUser, ResetUserPassword, UpdateUserPassword, UpdateUserProfileInformation};
use Modules\User\App\Http\Resources\UserResource;
use Modules\User\App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //customized login response
        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request)
            {

                $user = new UserResource(User::where('email', $request->email)->first());
                $token = $user->createToken('token')->plainTextToken;
                $cookie = cookie('access_token', $token, 60 * 24 * 7, secure: true); // 7 day

                return $request->wantsJson()
                    ? response()->json([
                        "message" => "You are successfully logged in",
                        'user' => $user,
                        'cookie' => $cookie,
                        'access_token' => $token
                    ], Response::HTTP_OK)->withCookie($cookie)
                    :   redirect()->intended(Fortify::redirects('login'));
            }
        });

        //customized register response
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse
        {
            public function toResponse($request)
            {
                $user = new UserResource(User::where('email', $request->email)->first());
                $token = $user->createToken('token')->plainTextToken;
                $cookie = cookie('access_token', $token, 60 * 24 * 7, secure: true); // 7 day
                return $request->wantsJson()
                    ? response()->json([
                        "message" => "Registration successful, verify your email address.",
                        'user' => $user,
                        'cookie' => $cookie,
                        'access_token' => $token
                    ], Response::HTTP_OK)->withCookie($cookie)
                    : redirect()->intended(Fortify::redirects('register'));
            }
        });

        //customized logout response
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request)
            {
                $cookie = Cookie::forget('access_token');

                return $request->wantsJson()
                    ? response()->json(['message' => 'Succesfully logged out'], Response::HTTP_OK)->withCookie($cookie)
                    : redirect(Fortify::redirects('logout', '/'));
            }
        });

        //customized profile update response
        $this->app->instance(ProfileInformationUpdatedResponse::class, new class implements ProfileInformationUpdatedResponse
        {
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['message' => 'Profile information updated successfully'], Response::HTTP_OK)
                    : back()->with('status', Fortify::PROFILE_INFORMATION_UPDATED);
            }
        });


        //customized password update response
        $this->app->instance(PasswordUpdateResponse::class, new class implements PasswordUpdateResponse

        {
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['message' => 'Password updated successfully'], Response::HTTP_OK)
                    : back()->with('status', Fortify::PASSWORD_UPDATED);
            }
        });

        //customized 2fa confirmd response
        $this->app->instance(TwoFactorConfirmedResponse::class, new class implements TwoFactorConfirmedResponse
        {
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['message' => '2FA confirmed successfully'], Response::HTTP_OK)
                    : back()->with('status', Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED);
            }
        });

        //customized 2fa disabled response
        $this->app->instance(TwoFactorDisabledResponse::class, new class implements TwoFactorDisabledResponse
        {
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['message' => '2FA disabled successfully'], Response::HTTP_OK)
                    : back()->with('status', Fortify::TWO_FACTOR_AUTHENTICATION_DISABLED);
            }
        });

        //customized 2fa enabled response
        $this->app->instance(TwoFactorEnabledResponse::class, new class implements TwoFactorEnabledResponse
        {
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['message' => '2FA enabled successfully'], Response::HTTP_OK)
                    : back()->with('status', Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fortify::authenticateUsing(function (Request $request) {
        //     $user = User::where('email', $request->email)->first();

        //     if ($user &&
        //         Hash::check($request->password, $user->password)) {
        //         return $user;
        //     }
        // });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
