<?php
namespace exceptions;
use Exception;
abstract class BaseException extends Exception {

    public abstract function getStatusCode();
}