<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\Exceptions\MethodCallException;
use Illuminate\Support\Collection;

trait AddsDefaultAttributesToModel
{

    /**
     * @var Collection
     */
    protected $defaultAttributes;


    public function defaultAttributes($attributes): self
    {
        if ($this->allowedAttributes instanceof Collection) {
            throw MethodCallException::mustBeCalledBefore('defaultAttributes', 'allowedAttributes');
        }

        $attributes = is_array($attributes) ? $attributes : func_get_args();
        $this->defaultAttributes = collect($attributes);

        $this->addDefaultAttributesToModel();

        return $this;
    }

    protected function addDefaultAttributesToModel()
    {
        $this->defaultAttributes
            ->each(function ($attribute, $index) {
                $this->model{$index} = $attribute;
            });
    }
}