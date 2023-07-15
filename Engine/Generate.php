<?php

class Generate
{

    /**
     * Generate key used for confession unique id.
     * @return string Generated key.
     * @throws Exception
     */

    public static function generateUniqueID($length = 12){
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $length);
    }

    /**
     * Generate key used for token.
     * @return string Generated key.
     */
    public static function _generateKey() {

        $unique_key = self::_generateLoginString();

        return md5(time() . $unique_key . time());
    }


    public static function getUserIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Generate string that will be used as fingerprint.
     * This is actually string created from user's browser name and user's IP
     * address, so if someone steal users session, he won't be able to access.
     * @return string Generated string.
     */
    public static function _generateLoginString() {
        $userIP = self::getUserIpAddr();
        $userBrowser = $_SERVER['HTTP_USER_AGENT'];
        return hash('sha512',$userIP . $userBrowser);
    }



}