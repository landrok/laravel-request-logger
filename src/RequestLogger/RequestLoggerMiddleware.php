<?php

namespace Landrok\Laravel\RequestLogger;

use Closure;
use Exception;
use Illuminate\Support\Facades\Route;
use Jenssegers\Agent\Agent;

class RequestLoggerMiddleware
{
    /**
     * @var \Jenssegers\Agent\Agent
     */
    private $agent;

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * @var \Illuminate\Http\Response
     */
    private $response;
    
    /**
     * Log various informations before sending response.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Http\Response $response
     */
    public function terminate($request, $response)
    {
        if (!config('requestlogger.enabled')) {
            return;
        }

        $this->agent    = new Agent();
        $this->request  = $request;
        $this->response = $response;

        $fields = config('requestlogger.fields');

        $log = new RequestLog();

        foreach ($fields as $field => $enabled) {
            if ($enabled) {
                $log->$field = $this->getValue($field);
            }
        }

        $log->created_at = date('Y-m-d H:i:s');

        $log->save();
    }

    private function getValue(string $key)
    {
        switch($key) {
            // User
            case 'session_id':
                return session()->getId();
            case 'user_id':
                return auth()->user() ? auth()->user()->id : null;
            case 'ip':
                return $this->request->getClientIp();
            case 'route':
                return Route::currentRouteName();
            case 'route_params':
                if ($this->request->route() instanceof \Illuminate\Routing\Route) {
                    return json_encode($this->request->route()->parameters());
                }
                // Route did not match anything (For instance, 404)
                return "[]";

            // Performances
            case 'duration':
                $starttime = defined('LARAVEL_START')
                    ? LARAVEL_START
                    : microtime(true);
                return microtime(true) - $starttime;
            case 'mem_alloc':
                return memory_get_peak_usage(true);

            // HTTP
            case 'method':
                return $this->request->getMethod();
            case 'status_code':
                return $this->response->getStatusCode();
            case 'url':
                return $this->request->url();
            case 'referer':
                return $this->request->headers->has('referer')
                    ? substr($this->request->headers->get('referer'), 0, 255)
                    : null;
            case 'referer_host':
                return $this->request->headers->has('referer')
                    ? parse_url($this->request->headers->get('referer'),  PHP_URL_HOST)
                    : null;
            case 'request_headers':
                return json_encode($this->request->headers->all());
            case 'response_headers':
                return json_encode($this->response->headers->all());

            // User Agent
            case 'device':
                return $this->agent->device();
            case 'os':
                return $this->agent->platform();
            case 'os_version':
                return $this->agent->version($this->agent->platform());
            case 'browser':
                return $this->agent->browser();
            case 'browser_version':
                return $this->agent->version($this->agent->browser());
            case 'is_desktop':
                return $this->agent->isDesktop();
            case 'is_mobile':
                return $this->agent->isMobile();
            case 'is_phone':
                return $this->agent->isPhone();
            case 'is_robot':
                return $this->agent->isRobot();
            case 'robot_name':
                return $this->agent->robot();

            case 'user_agent':
                return $this->request->header('user-agent');
            case 'meta':
                return Meta::toJson();
        }
    }
}
