<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;

if (! function_exists('api_success')) {
    /**
     * Return a successful JSON response.
     *
     * @param  string  $message  = 'Request processed succesfully.'
     * @param  int  $statusCode
     * @param  mixed  $data  = null
     */
    function api_success(string $message = 'Request processed succesfully.', mixed $data = null, $statusCode = 200): JsonResponse
    {
        $res_data = ['status' => 'success', 'message' => $message];

        if (! is_null($data)) {
            $res_data['data'] = $data;
        }

        return response()->json($res_data, $statusCode);
    }
}

if (! function_exists('api_failed_response')) {
    /**
     * Return a failed JSON response.
     *
     * @param  string  $$message  = 'Request was not succesfully.'
     * @param  int  $statusCode
     */
    function api_failed_response(string $message = 'Request was not succesful.', $statusCode = 400): JsonResponse
    {
        return response()->json(['status' => 'failed', 'message' => $message], $statusCode);
    }
}

if (! function_exists('api_failed')) {
    /**
     * Return a failed JSON response.
     *
     * @param  string  $$message  = 'Request was not succesfully.'
     * @param  int  $statusCode
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    function api_failed(string $message = 'Request was not succesful.', $statusCode = 400): JsonResponse
    {
        throw new HttpResponseException(api_failed_response($message, $statusCode));
    }
}

if (! function_exists('api_error')) {
    /**
     * Return an error JSON response.
     *
     * @param  array  $errorBag  = []
     * @param  string  $message  = 'Request failed with error(s).'
     * @param  int  $statusCode
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    function api_error(array $errorBag = [], string $message = 'Request failed with error(s).', $statusCode = 422): JsonResponse
    {
        $response = response()->json(['status' => 'error', 'message' => $message, 'errors' => $errorBag], $statusCode);
        throw new HttpResponseException($response);
    }
}

if (!function_exists('collection_response')) {
    /**
     * Return a response for API collection resources
     *
     * @param \Illuminate\Support\Collection $data
     * @param string $message
     * @param array $additonalData = []
     *
     * @return array
     */
    function collection_response(Collection $data, string $message = 'Data fetched successfully.', array $additonalData = []): array
    {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            ...$additonalData,
        ];
    }
}
