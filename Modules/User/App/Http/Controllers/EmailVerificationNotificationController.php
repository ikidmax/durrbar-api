<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\App\Http\Responses\{HasEmailResponse, SendEmailResponse};
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController as BaseController;

class EmailVerificationNotificationController extends BaseController
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return app(HasEmailResponse::class);
        }

        $request->user()->sendEmailVerificationNotification(); 

        return app(SendEmailResponse::class);
    }

}