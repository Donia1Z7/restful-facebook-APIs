<?php

namespace CustomExceptions;

use Exception;
use Constants\StatusCodes;

abstract class BaseException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct(
            $this->getMessageException($message),
            $this->getCodeException(),
            null
        );
    }

    protected abstract function getMessageException($message = "");
    protected abstract function getCodeException();
}