<?php

namespace CrudBuilder;

use CrudBuilder\Exceptions\MethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class CrudRelation
{

    /**
     * @var Model
     */
    protected $model;
    /**
     * @var string
     */
    protected $relationName;
    /**
     * @var Relation
     */
    protected $relation;
    /**
     * @var boolean
     */
    protected $dropRelatedWhenNotPresent;
    /**
     * @var boolean
     */
    protected $detachRelatedWhenNotPresent;

    public function __construct(Model $model, string $relationName)
    {
        $this->model = $model;
        $this->relationName = $relationName;

        if (!method_exists($this->model, $relationName)){
            throw MethodCallException::undefinedRelation(get_class($this->model), $relationName);
        }

        $this->relation = $this->model->{$relationName}();
    }

    public static function for(Model $model, string $relationName): self
    {
        return new self($model, $relationName);
    }

    public function getRelation(): Relation
    {
        return $this->relation;
    }

    public function getRelationName(): string
    {
        return $this->relationName;
    }

    public function dropRelatedWhenNotPresent(bool $drop = true): self
    {
        $this->dropRelatedWhenNotPresent = $drop;
        return $this;
    }

    public function detachRelatedWhenNotPresent(bool $detach = true): self
    {
        $this->detachRelatedWhenNotPresent = $detach;
        return $this;
    }
}