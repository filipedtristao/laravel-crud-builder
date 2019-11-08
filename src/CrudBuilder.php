<?php

namespace CrudBuilder;

use CrudBuilder\Concerns\AddsAttributesToModel;
use CrudBuilder\Concerns\AddsCrudMethodsToModel;
use CrudBuilder\Concerns\AddsDefaultAttributesToModel;
use CrudBuilder\Concerns\AddsIgnoreAttributesToModel;
use CrudBuilder\Concerns\AddsIgnoreRelationsToModel;
use CrudBuilder\Concerns\AddsRelationsToModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CrudBuilder
{

    use AddsAttributesToModel,
        AddsDefaultAttributesToModel,
        AddsIgnoreAttributesToModel,
        AddsRelationsToModel,
        AddsCrudMethodsToModel,
        AddsIgnoreRelationsToModel;

    protected $request;
    protected $builder;
    protected $model;

    public function __construct(Builder $builder, Request $request = null)
    {
        $this->builder = $builder;
        $this->model = $builder->getModel();
        $this->request = $request ?? request();
    }

    public static function for(string $builder, Request $request = null): self
    {
        if (is_string($builder)) {
            $builder = $builder::query();
        }

        return new self($builder, $request);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

}