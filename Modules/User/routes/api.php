<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\{Request, Response};
use Modules\User\App\Http\Controllers\{AddressController, EmailVerificationNotificationController, ProfilePhotoController, LogoutController, SocialiteController};
use Laravel\Fortify\Http\Controllers\{AuthenticatedSessionController, PasswordController, RegisteredUserController, PasswordResetLinkController, ProfileInformationController};



//

use Laravel\Fortify\Features;
// use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
// use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
// use Laravel\Fortify\Http\Controllers\PasswordController;
// use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
// use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
// use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
// use Laravel\Fortify\RoutePath;


/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Authentication routes
    Route::prefix('auth')->group(function () {

        // Retrieve the verification limiter configuration for verification attempts
        $verificationLimiter = config('fortify.limiters.verification', '6,1');

        Route::withoutMiddleware('auth:sanctum')->group(function () {
            // Route for user login
            Route::prefix('login')->group(function () {
                // Retrieve the limiter configuration for login attempts
                $limiter = config('fortify.limiters.login');
                Route::post('/', [AuthenticatedSessionController::class, 'store'])->middleware(array_filter([
                    'guest:' . config('fortify.guard'),  // Only guests (non-authenticated users) are allowed
                    $limiter ? 'throttle:' . $limiter : null,  // Throttle login attempts if limiter is configured
                ]));
                Route::post('/callback', [SocialiteController::class, 'handleProviderCallback']);
                Route::middleware('web')->get('/redirect/{provider}', function ($provider) {
                    return Socialite::driver($provider)->redirect();
                });
            });

            // Registration...
            Route::post('/register', [RegisteredUserController::class, 'store'])
                ->middleware(['guest:' . config('fortify.guard')]);  // Only guests (non-authenticated users) are allowed
            // Password Reset...
            Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware(['guest:' . config('fortify.guard')])  // Only guests (non-authenticated users) are allowed
                ->name('password.email');  // Name for the route
            Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware(['guest:' . config('fortify.guard')])  // Only guests (non-authenticated users) are allowed
                ->name('password.update');  // Name for the route

            // Two Factor Authentication...
            $twoFactorLimiter = config('fortify.limiters.two-factor');
            Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
                ->middleware(array_filter([
                    'guest:' . config('fortify.guard'),
                    $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
                ]));
        });

        // Email Verification...
        // Route to v email verification notification
        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->name('verification.verify');
        // Route to resend email verification notification
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware([
            'throttle:' . $verificationLimiter // Throttle resend email attempts
        ]);

        Route::post('/logout', [LogoutController::class, 'destroy']);
    });

    // User routes
    Route::prefix('user')->middleware(['verified'])->group(function () {

        Route::get('/me', function (Request $request) {
            $user = $request->user();
            return response()->json(['data' => ['user' => $user]], Response::HTTP_OK);
        });

        // Profile Information...
        Route::post('/profile-photo', [ProfilePhotoController::class, 'update']);
        Route::post('/remove-photo', [ProfilePhotoController::class, 'delete']);
        Route::put('/profile-information', [ProfileInformationController::class, 'update']);

        // Passwords...
        Route::put('/update-password', [PasswordController::class, 'update']);

        // Password Confirmation...
        Route::get('/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show']);
        Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

        //
        // Route::resource('address', AddressController::class)->names('address');

        // Two Factor Authentication...
        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'password.confirm']
            : [config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')];

        Route::post('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.enable');

        Route::post('/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.confirm');

        Route::delete('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.disable');

        Route::get('/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.qr-code');

        Route::get('/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.secret-key');

        Route::get('/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.recovery-codes');

        Route::post('/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
            ->middleware($twoFactorMiddleware);
    });
});
