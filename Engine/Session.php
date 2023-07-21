<?php

class Session {

    /**
     * Start the session
     * @return void
     */
    public static function startSession(){


        if (session_status() === PHP_SESSION_NONE) {
            session_name("PAYMENTGATEWAYSESSID");
            session_start();
        }
    }


    /**
     * Set the session
     * @param $index
     * @param $value
     * @return void
     */
    public static function setSession($index, $value) {
        $_SESSION[$index] = $value;
    }

    /**
     * Get the session value
     * @param $index
     * @return mixed|null
     */
    public static function getSession($index){
        return $_SESSION[$index] ?? null;
    }

    /**
     * Check if the session is set
     * @param $index
     * @return bool
     */
    public static function checkSession($index) {
        if(isset($_SESSION[$index])){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Unset a session
     * @param $index
     * @return void
     */
    public static function unsetSession($index) {
        unset($_SESSION[$index]);
    }

    /**
     * Destroy all sessions
     * @return void
     */
    public static function destroySession(){
        session_destroy();
    }


}