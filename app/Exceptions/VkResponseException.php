<?php

namespace App\Exceptions;

use Exception;

class VkResponseException extends Exception
{
    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'response_error' => $this->getMessage()
        ], 422);
    }
}
