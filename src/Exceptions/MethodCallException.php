<?php

namespace CrudBuilder\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class MethodCallException extends \BadMethodCallException
{

    public static function mustBeCalledBefore($method, $beforeMethod)
    {
        $message = "The CrudBuilder's `$method` method must be called before the `$beforeMethod` method.";
        return new static($message);
    }

    public static function undefinedRelation($model, $relation)
    {
        $message = "Undefined relation `$relation` on model `$model`";
        return new static($message);
    }
}