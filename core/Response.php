<?php
namespace core;

class Response{
    public function setStatusCode(int $code){
        \http_response_code($code);
    }

    public function redirect(string $route, array $params, int $code=302){
        Application::APP()->session->setFlashMessage('redirection_data', $params);
        header("Location: {$route}", true, $code);
        exit();
    }

    public function json(mixed $data){
        header('Content-Type: application/json');
        return json_encode($data);
    }
}