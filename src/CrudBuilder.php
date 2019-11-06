<?php

namespace CrudBuilder;

use CrudBuilder\Concerns\AddsAttributesToModel;
use CrudBuilder\Concerns\AddsCrudMethodsToModel;
use CrudBuilder\Concerns\AddsDefaultAttributesToModel;
use CrudBuilder\Concerns\AddsRelationsToModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CrudBuilder {

    use AddsAttributesToModel,
        AddsDefaultAttributesToModel,
        AddsRelationsToModel,
        AddsCrudMethodsToModel;

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

    public function getModel()
    {
        return $this->model;
    }

}