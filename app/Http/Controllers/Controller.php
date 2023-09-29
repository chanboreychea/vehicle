<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    const UNAUTHORIZED      = 'unauthorized';
    const BAD_REQUEST       = 'bad_request';
    const NOT_FOUND         = 'not_found';
    const FORBIDDEN         = 'forbidden';
    const INTERNAL_SERVER   = 'internal_server';

    public function response($data)
    {
        return response()->json([
            'data' => $data
        ]);
    }

    public function responses($datas)
    {
        return response()->json([
            'data' => $datas,
        ]);
    }


    public function responseSuccess()
    {
        return response()->json([
            'data' => [
                'success' => true
            ]
        ], 201);
    }

    public function responseError($type, $message = null, $code = null, $data = [])
    {
        $errors = [
            'unauthorized'      => 401,
            'not_found'         => 404,
            'forbidden'         => 403,
            'bad_request'       => 400,
            'internal_server'   => 500
        ];

        $messages = [
            'unauthorized'      => 'Unauthorized',
            'not_found'         => 'Not Found',
            'forbidden'         => 'Forbidden',
            'bad_request'       => 'Bad Request',
            'internal_server'   => 'Internal Server Error'
        ];

        return response()->json([
            'error' => [
                'message' => $message ? $message : $messages[$type],
                'code'    => $code ? $code : $errors[$type],
                'data'    => $data
            ],
        ], $errors[$type]);
    }

    public function  internalServerError($message)
    {
        return $this->responseError('internal_server', $message);
    }



    protected function uuid()
    {
        return Str::orderedUuid();
    }


}
