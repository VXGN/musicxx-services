<?php

namespace App\Helpers;

class ApiFormater
{
    protected static $response = [
        'code'      => null,
        'message'   => null,
        'data'      => [],
    ];
    public static function createJSON($code, $message, $data = [])
    {
        self::$response['code']     = $code;
        self::$response['message']  = $message;
        self::$response['data']     = $data;

        return response()->json(self::$response, self::$response['code']);
    }
}
