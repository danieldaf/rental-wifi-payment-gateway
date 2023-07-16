<?php

class CSRF
{
    private string $sessionKey = 'csrf_token';

    public function generateToken()
    {
        if (!Session::checkSession($this->sessionKey)) {
           Session::setSession($this->sessionKey, bin2hex(random_bytes(32)));
        }
        return Session::getSession($this->sessionKey);
    }

    public function validateToken($submittedToken): bool
    {
        if (!Session::checkSession($this->sessionKey) || $submittedToken !== Session::getSession($this->sessionKey)) {
            return false;
        }

        Session::unsetSession($this->sessionKey);
        return true;
    }
}