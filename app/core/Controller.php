<?php

namespace app\core;

use app\core\Response;

class Controller
{
    /**
     * return success response.
     *
     * @return \app\core\Response
     */
    public static function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return Response::json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \app\core\Response
     */
    public static function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return Response::json($response, $code);
    }

}