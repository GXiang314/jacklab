<?php 

class Request
{
    public $num = 10;
}

class Response
{
    public $code = -10;
}

interface MiddleWareInterface
{
    /**
     * @param Request $request
     * @param $closure
     * @return Response
     */
    public function execute(Request $request, $closure);
}

class MiddleWareA implements MiddleWareInterface
{
    /**
     * @param Request $request
     * @param $closure
     * @return Response
     */
    public function execute(Request $request, $closure)
    {
        $request->num ++;
        $response = $closure($request);
        $response->code --;
        return $response;
    }
}

class MiddleWareB implements MiddleWareInterface
{
    /**
     * @param Request $request
     * @param $closure
     * @return Response
     */
    public function execute(Request $request, $closure)
    {
        $request->num ++;
        $response = $closure($request);
        $response->code --;
        return $response;
    }
}


function get_global_middleware()
{
    return [
        MiddleWareA::class,
        MiddleWareB::class,
    ];
}

class App
{

    public function run($request)
    {
        $global_middle_ware = get_global_middleware();
        
        $next = function () use ($request) {
            return $this->do($request);
        };

        foreach ($global_middle_ware as $middleware) {
            $m = new $middleware;

            $next = function () use ($request, $middleware, $next, $m) {
                return $m->execute($request, $next) ;
            };

        }

        $response = $next();
        return $response;
    }

    public function do($request)
    {
        $response = new Response();
        return $response;
    }

}

$app = new App();
$request = new Request();
$response = $app->run($request);

var_dump($request);
var_dump($response);


?>