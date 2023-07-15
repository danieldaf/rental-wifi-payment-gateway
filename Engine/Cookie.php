<?php

class Cookie {

    /**
     * Set a cookie with a specific name and value
     * @param string $cookiename Cookie name
     * @param string $cookievalue Cookie value
     * @return bool
     */
    public static function _set(string $cookiename, string $cookievalue): bool
    {
        return setcookie($cookiename, $cookievalue, time()+30*24*60*60, '/');
    }

    /**
     * Get the value of a cookie with a specific name
     * @param string $cookiename Cookie name
     * @return string Cookie value
     */

    public static function _get(string $cookiename): string {
        return $_COOKIE[$cookiename];
    }

    /**
     * Check if the cookie set
     * @param string $cookiename Name of the cookie
     * @return bool TRUE if set, FALSE otherwise
     */
    public static function _check(string $cookiename): bool {
        if(isset($_COOKIE[$cookiename]))
            return true;

        return false;
    }

    public static function _destroy($cookiename): bool
    {
        if (isset($_COOKIE[$cookiename])) {
            unset($_COOKIE[$cookiename]);
            setcookie($cookiename, null, -1, '/');
            return true;
        } else {
            return false;
        }

    }
}