<?php
namespace exceptions;

class InvalidArgumentException extends BaseException {

    public function getStatusCode() {
        return 400;
    }
}