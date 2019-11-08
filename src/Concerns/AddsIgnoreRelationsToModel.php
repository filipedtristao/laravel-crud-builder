<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\Exceptions\MethodCallException;
use Illuminate\Support\Collection;

trait AddsIgnoreRelationsToModel
{

    /**
     * @var Collection
     */
    protected $ignoreRelations;

    public function ignoreRelations($relations): self
    {
        if ($this->allowedRelations instanceof Collection) {
            throw MethodCallException::mustBeCalledBefore('ignoreRelations', 'allowedRelations');
        }

        $relations = is_array($relations) ? $relations : func_get_args();
        $this->ignoreRelations = collect($relations);

        return $this;
    }

}