<?php

class Session {

    public static function startSession(){


        if (session_status() === PHP_SESSION_NONE) {
            session_name("PAYMENTGATEWAYSESSID");
            session_start();
        }
    }


    public static function setSession($index, $value) {
        $_SESSION[$index] = $value;
    }

    public static function getSession($index){
        return $_SESSION[$index] ?? null;
    }

    public static function checkSession($index) {
        if(isset($_SESSION[$index])){
            return true;
        } else {
            return false;
        }
    }

    public static function unsetSession($index) {
        unset($_SESSION[$index]);
    }

    public static function destroySession(){
        session_destroy();
    }


}