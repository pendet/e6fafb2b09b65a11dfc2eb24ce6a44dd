<?php

namespace App;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Router
{
    private $routes = [];

    public function get($uri, Closure $callback)
    {
        $this->routes['GET'][$uri] = $callback;
    }

    public function post($uri, Closure $callback)
    {
        $this->routes['POST'][$uri] = $callback;
    }

    public function put($uri, Closure $callback)
    {
        $this->routes['PUT'][$uri] = $callback;
    }

    public function patch($uri, Closure $callback)
    {
        $this->routes['PATCH'][$uri] = $callback;
    }

    public function delete($uri, Closure $callback)
    {
        $this->routes['DELETE'][$uri] = $callback;
    }

    public function run(Request $request)
    {
        $method = $request->getMethod();
        $url = $request->getPathInfo();

        $parameters = $this->matchRoute($method, $url);

        if (!is_null($parameters)) {
            return $parameters['callback']($request, ...$parameters['args']);
        }

        return new Response('Route not found', 404);
    }

    private function matchRoute($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route => $callback) {
            if ($this->compareRoutes($route, $uri, $parameters)) {
                return ['callback' => $callback, 'args' => $parameters];
            }
        }

        return null;
    }

    private function compareRoutes($route, $uri, &$parameters)
    {
        $routeParts = explode('/', trim($route, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $parameters = [];

        foreach ($routeParts as $index => $routePart) {
            if (strpos($routePart, '{') === 0 && strrpos($routePart, '}') === strlen($routePart) - 1) {
                $parameters[] = $uriParts[$index];
            } elseif ($routePart !== $uriParts[$index]) {
                return false;
            }
        }

        return true;
    }
}
