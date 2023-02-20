<?php

namespace App\Exceptions\Customs;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomException extends Exception
{
    /**
     * @return void
     */
    public function report(): void
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'error' => null,
            ]
        );
    }
}
