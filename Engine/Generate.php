<?php

class Generate
{

    /**
     * @return mixed
     */
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