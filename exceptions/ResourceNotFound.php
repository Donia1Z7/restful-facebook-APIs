<?php

namespace CustomExceptions;

use Constants\StatusCodes;
use Exception;

class ResourceNotFound extends BaseException
{
    protected function getMessageException($message = "")
    {
        return $message ?: "Resource Not found.";
    }

    protected function getCodeException()
    {
        return StatusCodes::NOT_FOUND;
    }
}