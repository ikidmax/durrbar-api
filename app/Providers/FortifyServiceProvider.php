<?php

namespace App\Providers;



use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\{LoginResponse, LogoutResponse, PasswordUpdateResponse, RegisterResponse, ProfileInformationUpdatedResponse};
use Modules\User\App\Actions\Fortify\{CreateNewUser, ResetUserPassword, UpdateUserPassword, UpdateUserProfileInformation};
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
                if ($request->wantsJson()) {
                    $user = User::where('email', $request->email)->first();
                    // $token = $user->createToken('token')->plainTextToken;
                    // $cookie = cookie('access_token', $token, 60 * 24 * 7); // 7 day
                    return response()->json([
                        "message" => "You are successfully logged in",
                        'data' => ['user' => $user],
                    ], Response::HTTP_OK);
                }
                return redirect()->intended(Fortify::redirects('login'));
            }
        });


        //customized register response
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse
        {
            public function toResponse($request)
            {
                $user = User::where('email', $request->email)->first();
                $token = $user->createToken('token')->plainTextToken;
                $cookie = cookie('access_token', $token, 60 * 24 * 7); // 7 day
                return $request->wantsJson()
                    ? response()->json([
                        "message" => "Registration successful, verify your email address.",
                        'data' => ['user' => $user],
                    ], Response::HTTP_OK)->withCookie($cookie)
                    : redirect()->intended(Fortify::redirects('register'));
            }
        });

        //customized logout response
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['message' => 'Succesfully logged out'], Response::HTTP_OK)
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
