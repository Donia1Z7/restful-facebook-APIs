<?php

namespace CustomExceptions;

use Constants\StatusCodes;
use Exception;

class BadRequestException extends BaseException
{
    protected function getMessageException($message = "")
    {
        return $message ?: "Bad usage exception.";
    }

    protected function getCodeException()
    {
        return StatusCodes::VALIDATION_ERROR;
    }
}