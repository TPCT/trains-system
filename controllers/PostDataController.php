<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use helpers\ActionsHelper;
use helpers\EncryptionHelper;
use models\PostDataModel;

class PostDataController extends Controller{
    public function __construct(){
        $this->Layout = '';
        $this->request = new Request();
    }

    public function index(){
        $postDataModel = new PostDataModel();
        $body = $this->request->body();
        $encryptionHelper = new EncryptionHelper();
        $encryptionHelper->decryptArray($body, false);
        $postDataModel->loadData($body);
        
        $reply = [
            'status' => 0,
            'response' => ''
        ];

        if ($this->request->isPost()){
            if ($postDataModel->Validate() && $postDataModel->save()){
                $reply['status'] = 1;
                $reply['response'] = ActionsHelper::getAction($postDataModel->getSensors());
            }else{
                $reply['response'] = "There was an error, your data has not recorded";
            }
        }else{
            $reply['status'] = 1;
            $reply['response'] = "You must post sensors data here";
        }

        return Application::APP()->response->json($reply);
    }

    public function encryptionTest(){
        $body = $this->request->body();
        $encrypted_data = $body['encrypted_text'];
        $encryptionHelper = new EncryptionHelper();
        $return_data = [
            'decrypted_data' => $encryptionHelper->symmetricDecryption($encrypted_data),
            'encrypted_data' => $encryptionHelper->symmetricEncryption("hello world 1", $encryption_data),
        ];
        return json_encode($return_data);
    }
    
}