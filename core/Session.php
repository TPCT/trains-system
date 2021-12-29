<?php

namespace core;

class Session{
    protected const FLASH_KEY = 'flash_messages';
    public function __construct()
    {
        session_start();
        $flash_messages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flash_messages as $key => &$flash_message){
            $flash_message['remove'] = true;
        }
        if ($flash_messages)
            $_SESSION[self::FLASH_KEY] = $flash_messages;

    }
    public function setFlashMessage($key, $message){
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlashMessage($key){
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? False;
    }

    public function destroy(){
        session_destroy();
    }

    public function __destruct(){
        $flash_messages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flash_messages as $key => &$flash_message){
            if ($flash_message['remove'])
                unset($flash_messages[$key]);
        }
        if ($flash_messages)
            $_SESSION[self::FLASH_KEY] = $flash_messages;
    }

    public function set($key, $value){
        $_SESSION[$key] = $value;
    }

    public function get($key){
        return $_SESSION[$key] ?? Null;
    }
}