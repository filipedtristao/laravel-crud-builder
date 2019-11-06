<?php
namespace CrudBuilder\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class InvalidRequestException extends HttpException
{

}