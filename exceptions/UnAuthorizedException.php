<?php

namespace CustomExceptions;


use Constants\StatusCodes;

class UnAuthorizedException extends BaseException
{
    protected function getMessageException($message = "")
    {
        return $message ?: "Your are not authorized to do that.";
    }

    protected function getCodeException()
    {
        return StatusCodes::FORBIDDEN;
    }
}