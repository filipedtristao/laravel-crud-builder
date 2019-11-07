<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\Exceptions\MethodCallException;
use Illuminate\Support\Collection;

trait AddsIgnoreAttributesToModel
{

    /**
     * @var Collection
     */
    protected $ignoreAttributes;

    public function ignoreAttributes($attributes): self
    {
        if ($this->ignoreAttributes instanceof Collection) {
            throw MethodCallException::mustBeCalledBefore('ignoreAttributes', 'allowedAttributes');
        }

        $attributes = is_array($attributes) ? $attributes : func_get_args();
        $this->ignoreAttributes = collect($attributes);

        return $this;
    }

}