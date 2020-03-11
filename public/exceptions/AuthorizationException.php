<?php
namespace exceptions;

class AuthorizationException extends BaseException {

    public function getStatusCode() {
        return 401;
    }
}