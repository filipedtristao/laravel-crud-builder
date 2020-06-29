<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\Exceptions\MethodCallException;
use Illuminate\Support\Collection;
use function collect;

trait AddsDefaultAttributesToModel
{
    protected $defaultAttributes;
    protected $appliedDefaultAttributes;

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
        $this->appliedDefaultAttributes = collect();

        $this->defaultAttributes
            ->each(function ($attribute, $index) {
                $this->model->{$index} = $attribute;
                $this->appliedDefaultAttributes->offsetSet($index, $attribute);
            });
    }
}