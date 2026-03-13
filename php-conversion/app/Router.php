<?php
/**
 * Simple Router for API Endpoints
 * Maps URLs to controller methods
 */

class Router
{
    private $routes = [];
    private $controllers = [];

    public function register($method, $path, $callback)
    {
        $this->routes[strtoupper($method)][$this->normalizePath($path)] = $callback;
    }

    public function get($path, $callback)
    {
        $this->register('GET', $path, $callback);
    }

    public function post($path, $callback)
    {
        $this->register('POST', $path, $callback);
    }

    public function put($path, $callback)
    {
        $this->register('PUT', $path, $callback);
    }

    public function delete($path, $callback)
    {
        $this->register('DELETE', $path, $callback);
    }

    /**
     * Normalize path by removing trailing slash and converting to lowercase
     */
    private function normalizePath($path)
    {
        return rtrim($path, '/');
    }

    /**
     * Match URL to route and extract parameters
     */
    public function dispatch($method, $path)
    {
        $method = strtoupper($method);
        $path = $this->normalizePath($path);

        // Exact match
        if (isset($this->routes[$method][$path])) {
            return $this->callRoute($this->routes[$method][$path]);
        }

        // Try pattern matching
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            if ($this->matchesPattern($route, $path, $params)) {
                return $this->callRoute($callback, $params);
            }
        }

        return json_response(['error' => 'Route not found'], 404);
    }

    /**
     * Match URL pattern to path and extract parameters
     */
    private function matchesPattern($route, $path, &$params = [])
    {
        $params = [];
        $route_regex = preg_replace_callback(
            '/:([a-zA-Z_][a-zA-Z0-9_]*)/i',
            function ($matches) {
                return '(?P<' . $matches[1] . '>[^/]+)';
            },
            $route
        );

        $route_regex = '#^' . $route_regex . '$#';

        if (preg_match($route_regex, $path, $matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Call route callback
     */
    private function callRoute($callback, $params = [])
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        return json_response(['error' => 'Invalid route handler'], 500);
    }
}
