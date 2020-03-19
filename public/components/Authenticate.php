<?php


namespace components;


use exceptions\AuthorizationException;

class Authenticate
{
    public function authenticate()
    {
        if (!isset($_SESSION['logged_user'])) {
            throw new AuthorizationException('Requires authorisation!');
        }
    }
}