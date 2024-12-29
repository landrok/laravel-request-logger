<?php

namespace Landrok\Laravel\RequestLoggerTest;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Landrok\Laravel\RequestLogger\RequestLog;
use PHPUnit\Framework\Attributes\DataProvider;
use Landrok\Laravel\RequestLogger\RequestLoggerMiddleware;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/*
 * This class runs various tests with default configurations values
 */
class MiddlewareDefaultsTest extends TestCase
{
    protected $requestLoggerMiddleware;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestLoggerMiddleware = new RequestLoggerMiddleware();

        $this->app['config']->set('requestlogger.enabled', true);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        defined('LARAVEL_START') || define('LARAVEL_START', microtime(true));
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
    public static function getScenarios(): array
    {
        return [
            // method, path, user, status_code, route
            'get-200-user_id'   => ['GET', '/check-request-logger', 'testUser', 200, 'route-without-params'],
            'get-200-user_none' => ['GET', '/hello-world' . rand(0, 1024), null, 200, null],
            'post-200-user_none'=> ['POST', '/hello-world' . rand(0, 1024), null, 200, null],
            'get-404-user_none' => ['GET', '/hello-world' . rand(0, 1024), null, 404, null],
            'get-500-user_none' => ['GET', '/hello-world' . rand(0, 1024), null, 500, null],

            // method, path, user, status_code, route
            // Params should not be logged by default
            'get-200-route-with-params'   => ['GET', '/check-request-logger/123456', null, 200, 'route-with-params'],
        ];
    }

    #[DataProvider('getScenarios')]
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
        $sfRequest->headers->set('Referer', 'http://localhost/referer');

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

        $this->assertInstanceOf(RequestLog::class, $log);

        // User
        if (!is_null($user)) {
            $this->assertEquals($log->user_id, $user->id);
            $this->assertEquals($log->user->name, $user->name);
        } else {
            $this->assertEquals($log->user_id, null);
        }
        $this->assertEquals($log->ip, '127.0.0.1');
        $this->assertEquals($log->route, $route);
        $this->assertEquals($log->route_params, null);

        // Performances
        $this->assertIsNumeric($log->duration);
        $this->assertIsNumeric($log->mem_alloc);

        // HTTP
        $this->assertEquals($log->method, $method);
        $this->assertEquals($log->status_code, $status_code);
        $this->assertEquals($log->url, 'http://localhost'. $path);
        $this->assertEquals($log->referer, 'http://localhost/referer');
        $this->assertEquals($log->referer_host, 'localhost');
        $this->assertEquals($log->request_headers, null);
        $this->assertEquals($log->response_headers, null);

        // Device
        $this->assertEquals($log->device, 0);
        $this->assertEquals($log->os, 0);
        $this->assertEquals($log->os_version, 0);
        $this->assertEquals($log->browser, 0);
        $this->assertEquals($log->browser_version, 0);
        $this->assertEquals($log->is_desktop, 1);
        $this->assertEquals($log->is_tablet, 0);
        $this->assertEquals($log->is_mobile, 0);
        $this->assertEquals($log->is_phone, 0);
        $this->assertEquals($log->is_robot, 0);
        $this->assertEquals($log->robot_name, 0);
        $this->assertEquals($log->user_agent, 'Symfony');

        $this->assertEquals($log->meta, null);
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            $log->created_at
        );
    }

    protected function runMiddleware($request, $response)
    {
        $response->setContent('<html></html>');
        $this->requestLoggerMiddleware->terminate($request, $response);
    }
}
