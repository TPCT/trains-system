<?php
namespace core;

class Router{
    protected array $routes = array();
    protected array $middlewares = array();
    protected string $lastRouteMethod = '';
    protected string $lastRoutePath = '';

    public Request $request;
    public Response $response;

    public function __construct(){
        $this->request = new Request();
        $this->response = new Response();
    }

    public function get(string $route, $callback){
        $this->routes['GET'][$route] = $callback;
        $this->lastRouteMethod = 'GET';
        $this->lastRoutePath = $route;
        return $this;
    }

    public function post(string $route, $callback){
        $this->routes['POST'][$route] = $callback;
        $this->lastRouteMethod = 'POST';
        $this->lastRoutePath = $route;
        return $this;
    }

    public function middleware(MiddleWareBase $middleware){
        $this->middlewares[$this->lastRouteMethod][$this->lastRoutePath] = $middleware;
        return $this;
    }

    public function resolve(){
        $route = $this->request->path();
        $method = $this->request->method();
        $callback = $this->routes[$method][$route] ?? Null;
        $middleware = $this->middlewares[$method][$route] ?? Null;
        
        $middleware?->validate();
        
        if ($callback === Null){
            $this->response->setStatusCode(404);
            return (new View("_404"))->render();
        }

        if (is_array($callback)){
            [$class, $method] = $callback;
            if (\class_exists($class)){
                $class = new $class();
                Application::APP()->setController($class);
                if (\method_exists($class, $method)){
                    return \call_user_func([$class, $method]);
                }
            }
        }
        
        if (\is_callable($callback)){
            return \call_user_func($callback);
        }

        if (\is_string($callback)){
            return $callback;
        }
        
    }

    
}