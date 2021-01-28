<?php

namespace Landrok\Laravel\RequestLoggerTest;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Landrok\Laravel\RequestLogger\RequestLog;
use Landrok\Laravel\RequestLogger\RequestLoggerMiddleware;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/*
 * This class runs various tests with disabled middleware
 */
class MiddlewareDisabledTest extends TestCase
{
    protected $requestLoggerMiddleware;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestLoggerMiddleware = new RequestLoggerMiddleware();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('requestlogger.enabled', false);

        $this->setUpRoutes();
    }

    /**
     * Create routes to test route logging
     */
    public function setUpRoutes(): void
    {
        app('router')->get('/check-request-logger', function (Request $request) {
            return [];
        })->name('route-without-params');
        app('router')->get('/check-request-logger/{id}', function (Request $request) {
            return [];
        })->name('route-with-params');
    }

    /**
     * Scenarios
     */
    public function getScenarios(): array
    {
        return [
            // method, path, user, status_code, route
            'get-200-user_id'   => ['GET', '/check-request-logger', 'testUser', 200, 'route-without-params'],
            'get-200-user_none' => ['GET', '/hello-world' . rand(0, 1024), null, 200, null],
            'post-200-user_none'=> ['POST', '/hello-world' . rand(0, 1024), null, 200, null],
            'get-404-user_none' => ['GET', '/hello-world' . rand(0, 1024), null, 404, null],
            'get-500-user_none' => ['GET', '/hello-world' . rand(0, 1024), null, 500, null],

            // method, path, user, status_code, route
            // Params won't be logged by default
            'get-200-route-with-params'   => ['GET', '/check-request-logger/123456', null, 200, 'route-with-params'], 
        ];
    }

    /**
     * @dataProvider getScenarios
     */
    public function test_middleware($method, $path, $user = null, $status_code = null, $route = null): void
    {
        if (!is_null($user)) {
            $user = $this->$user;
            Auth::login($user);
        }

        // Create request & response
        $sfRequest = SymfonyRequest::create(
            $path,
            $method
        );
        $response = new Response('', $status_code);

        $request = Request::createFromBase($sfRequest);
        $request->setRouteResolver(function() { return 'check'; });

        // Try to dispatch route (may not exist)
        try {
            app('router')->dispatch($request);
        } catch(\Exception $e) {}

        // Execute middleware
        $this->runMiddleware($request, $response);

        // Get stored log
        $log = RequestLog::orderby('id', 'desc')->first();

        $this->assertNull($log);
    }

    protected function runMiddleware($request, $response)
    {
        $response->setContent('<html></html>');
        $this->requestLoggerMiddleware->terminate($request, $response);
    }
}
