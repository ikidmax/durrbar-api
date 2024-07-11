<?php

namespace Modules\User\App\Http\Responses;

use Modules\User\App\Contracts\BaseResponse;
use Symfony\Component\HttpFoundation\Response;

class UpdatePhotoResponse implements BaseResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json([
                'message' => 'Profile photo updated',
                'm2' => $request,
            ], Response::HTTP_OK)
            : back()->with('status', 'profile photo updated');
    }
}
