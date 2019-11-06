<?php

namespace CrudBuilder\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class InvalidAttributeException extends InvalidRequestException
{
    /** @var \Illuminate\Support\Collection */
    public $unknownAttributes;

    /** @var \Illuminate\Support\Collection */
    public $allowedAttributes;

    public function __construct(Collection $unknownAttributes, Collection $allowedAttributes)
    {
        $this->unknownAttributes = $unknownAttributes;
        $this->allowedAttributes= $allowedAttributes;

        $unknownAttributes = $this->unknownAttributes->implode(', ');
        $allowedAttributes = $this->allowedAttributes->implode(', ');

        $message = "Requested attributes(s) `{$unknownAttributes}` are not allowed. Allowed attributes(s) are `{$allowedAttributes}`.";
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }

    public static function attributesNotAllowed(Collection $unknownAttributes, Collection $allowedAttributes)
    {
        return new static(...func_get_args());
    }
}