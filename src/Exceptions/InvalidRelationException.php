<?php

namespace CrudBuilder\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class InvalidRelationException extends InvalidRequestException
{
    /** @var \Illuminate\Support\Collection */
    public $unknownRelations;

    /** @var \Illuminate\Support\Collection */
    public $allowedRelations;

    public function __construct(Collection $unknownRelations, Collection $allowedRelations)
    {
        $this->unknownRelations = $unknownRelations;
        $this->allowedRelations = $allowedRelations;

        $unknownRelations = $this->unknownRelations->implode(', ');
        $allowedRelations = $this->allowedRelations->implode(', ');

        $message = "Requested attributes(s) `{$unknownRelations}` are not allowed. Allowed relations(s) are `{$allowedRelations}`.";
        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }

    public static function relationsNotAllowed(Collection $unknownRelations, Collection $allowedRelations)
    {
        return new static(...func_get_args());
    }
}