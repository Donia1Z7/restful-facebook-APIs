<?php

namespace Constants;
use ReflectionClass;
class BaseConstant
{
    public function listOfExistsConstants(){
        return (new ReflectionClass($this))->getConstants();
    }

}