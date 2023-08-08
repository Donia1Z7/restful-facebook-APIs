<?php

namespace CustomExceptions;

use Constants\StatusCodes;

class UnAuthenticatedException extends BaseException
{
    protected function getMessageException($message = "")
    {
        return $message ?: "Wrong Credentials.";
    }

    protected function getCodeException()
    {
        return StatusCodes::UNAUTHORIZED;
    }
}