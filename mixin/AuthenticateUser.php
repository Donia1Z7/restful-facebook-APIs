<?php

namespace Mixin;

use CustomExceptions\BadRequestException;
use CustomExceptions\UnAuthenticatedException;
use Models\User;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
* Note: This trait should be use only and only in Controller classes.
*/
trait AuthenticateUser
{
/**
* @var User $authenticatedUser
*/
private $authenticatedUser;
/**
* @var array $skipHandlers
*/
private $skipHandlers = [];

public function __call($method, $arguments)
{
$handler = key_exists($method, $this->handlerMap) ? $this->handlerMap[$method] : $method;

if (in_array($handler, $this->skipHandlers)) {

return parent::__call($method, $arguments);
}
if (! key_exists("PHP_AUTH_USER", $_SERVER) || ! key_exists("PHP_AUTH_PW", $_SERVER)) {

throw new BadRequestException("This API require the authentication process.");
}

$email = $_SERVER['PHP_AUTH_USER'];
$password =  $_SERVER['PHP_AUTH_PW'];

$authenticatedUser = User::query()->where("email", $email)->first();

if (! $authenticatedUser) {

throw new NotFoundResourceException("Your email isn't match any with user in system.");
}
if (md5($password) != $authenticatedUser->password) {

throw new UnAuthenticatedException();
}

$this->authenticatedUser = $authenticatedUser;
return parent::__call($method, $arguments);
}
}