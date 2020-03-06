<?php
namespace exceptions;

class InvalidFileException extends BaseException {

    public function getStatusCode() {
        return 415;
    }
}