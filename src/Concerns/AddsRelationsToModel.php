<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\Exceptions\InvalidRelationException;

Trait AddsRelationsToModel
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $allowedRelations;

    public function allowedRelations($relations): self
    {
        $relations = is_array($relations) ? $relations : func_get_args();

        $this->allowedRelations = collect($relations)
            ->mapWithKeys(function ($relation) {
                if (!($relation instanceof \CrudBuilder\CrudRelation)) {
                    $relation = \CrudBuilder\CrudRelation::for($this->model, $relation)
                        ->detachRelatedWhenNotPresent();
                }

                return [$relation->getRelationName() => $relation];
            });

        $this->ensureAllRelationsExist();

        return $this;
    }

    protected function ensureAllRelationsExist()
    {
        $requestedRelations = collect($this->request->input('data.relationships'))
            ->keys()
            ->unique();

        if ($this->ignoreRelations){
            $requestedRelations = $requestedRelations->filter(function($requestedRelation) {
                return !in_array($requestedRelation, $this->ignoreRelations->toArray());
            });
        }

        $unknownRelations = $requestedRelations->diff($this->allowedRelations->keys());

        if ($unknownRelations->isNotEmpty()) {
            throw InvalidRelationException::relationsNotAllowed($unknownRelations, $this->allowedRelations->keys());
        }
    }

}