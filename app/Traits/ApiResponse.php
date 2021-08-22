<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

trait ApiResponse
{
    // success response with 200 code
    public function successResponse(string $message, array $data = [], array $headers = []): Response
    {
        $response = [
            'status' => true,
            'message' => $message
        ];

        if (! empty($data)) {

            foreach ($data as $key => $value) {
                $response[$key] = $value;
            }
        }

        $response = response($response);

        if (! empty($headers)) {
            $response->withHeaders($headers);
        }

        return $response->setStatusCode(200);
    }

    // send validation errors response
    public function validationErrors(Validator $validation, int $code = 400): Response
    {
        return $this->failedResponse($validation->errors()->first())->setStatusCode($code);
    }

    // custom error response
    public function failedResponse(string $message, array $data = [], int $code = 403): Response
    {
        $response = [
            'status' => false,
            'message' => $message
        ];

        if (! empty($data)) {
            foreach ($data as $key => $value) {
                $response[$key] = $value;
            }
        }

        $response = response($response);

        return $response->setStatusCode($code);
    }
}
