<?php

namespace core;

abstract class MiddleWareBase{
    abstract function routes(): array;
    abstract function verifier(): bool;
    abstract function forbidden();
    abstract function allowed();

    public function validate(){
        foreach ($this->routes() as $route){
            $requestedRoute = Application::APP()->request->path();
            if (str_starts_with($requestedRoute, $route) && $this->verifier())
                return $this->allowed();
        }
        return $this->forbidden();
    }    
}