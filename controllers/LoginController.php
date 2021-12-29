<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use helpers\EncryptionHelper;
use models\AuthModel;

class LoginController extends Controller{
    public function __construct(){
        $this->Layout = 'auth';
        $this->request = new Request();
    }
    
    public function login(){
        $login_error = Null;
        if ($this->request->isPost()){
            $authModel = new AuthModel();
            $authModel->loadData($this->request->body());
            if ($authModel->Validate() && $authModel->login()){
                $record = $authModel->getUserData();
                Application::APP()->session->set("logged", true);
                Application::APP()->session->set("privileges", $record->privileges);
                Application::APP()->session->set("username", $record->username);  
                Application::APP()->session->set('id', $record->id);
                return Application::APP()->response->redirect('/posts', []);    
            }else{
                $login_error = "wrong username or password";
            }
        }            
        
        return $this->render('login', ['login_error' => $login_error]);
    }

    public function api(){
        $reply = ['status' => 1, "response" => "please login to continue"];
        if ($this->request->isPost()){
            $authModel = new AuthModel();
            $encryptionHelper = new EncryptionHelper();
            $body = $this->request->body();
            $encryptionHelper->decryptArray($body);
            $authModel->loadData($body);
            if ($authModel->Validate() && $authModel->login()){
                $record = $authModel->getUserData();
                Application::APP()->session->set("logged", true);
                Application::APP()->session->set("privileges", $record->privileges);
                Application::APP()->session->set("username", $record->username);  
                Application::APP()->session->set('id', $record->id);
                $encryptionHelper->loadKeys();
                return Application::APP()->response->redirect('/posts', []);    
            }else{
                $reply['status'] = 0;
                $reply['response'] = "Wrong username or password";
            }
        }            
        return Application::APP()->response->json($reply);
    }

    public function getPublicKey(){
        $encryptionHelper = new EncryptionHelper();
        $reply = ['status' => 1, 'PUBLIC_KEY' => $encryptionHelper->publicKey()];
        return Application::APP()->response->json($reply);
    }
    
    public function logout(){
        session_destroy();
        header('Location: /');
    }
}