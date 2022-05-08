<?php

namespace app\core;

use app\core\middlewares\Middleware;
use app\core\Response;

class Controller
{

    protected array $middlewares = [];
    public string $action = '';
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

    public function registerMiddleware(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddleware()
    {
        return $this->middlewares;
    }

}