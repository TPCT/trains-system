<?php

namespace core;

class Request{
    public function method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function path(){
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $mark_position = strpos($path, '?');
        if ($mark_position !== False)
            $path = substr($path, 0, $mark_position);
        return $path;
    }

    public function isGet(){
        return $this->method() === 'GET';
    }

    public function isPost(){
        return $this->method() === 'POST';
    }

    public function body(){
        $body = array();
        if ($this->isGet()){
            foreach($_GET as $key => $value){
                $body[$key] = \filter_input(\INPUT_GET, $key, \FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        if ($this->isPost()){
            foreach($_POST as $key => $value){
                $body[$key] = \filter_input(\INPUT_POST, $key, \FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}