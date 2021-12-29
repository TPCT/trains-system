<?php
namespace core;

abstract class Controller{
    protected string $Layout = 'main';
    protected Request $request;

    public function layout(){
        return $this->Layout;
    }

    public function setLayout($layout){
        $this->Layout = $layout;
    }

    public function render($view, $params){
        return (new View($view, $params))->render();
    }
}