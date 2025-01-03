<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    /**
     * Handle error responses.
     *
     * @param string $message The error message to be logged and returned in the response.
     * @param Request|null $request The HTTP request that triggered the error, if available.
     * @param int $statusCode The HTTP status code for the response (default is 500).
     * @return JsonResponse A JSON response containing the success status and error message.
     */
    protected function handleError(string $message, ?Request $request = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        Log::error($message, [
            'request' => $request ? $request->all() : [],
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Generate a unique filename for an uploaded image.
     *
     * @param UploadedFile $image The uploaded image file.
     * @return string A unique filename based on the original name and current timestamp.
     */
    protected function generateUniqueFileName(UploadedFile $image): string
    {
        $extension = $image->getClientOriginalExtension();
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        return uniqid($originalName . '_', true) . '.' . $extension;
    }
}
