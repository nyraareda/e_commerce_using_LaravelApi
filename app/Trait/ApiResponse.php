<?php

namespace App\Trait;

trait ApiResponse
{
    /**
     * Success response method.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message = null, $code = 200)
    {
        $response = [
            'status' => 'success',
            'data' => $data,
        ];

        if (!is_null($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response method.
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ], $code);
    }

    /**
     * Validation error response method.
     *
     * @param array $errors
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationErrorResponse($errors, $message = 'Validation Error', $code = 422)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
