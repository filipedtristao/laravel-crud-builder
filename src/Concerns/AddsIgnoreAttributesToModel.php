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

        $this->initIgnoreAttributes();

        $attributes = is_array($attributes) ? $attributes : func_get_args();
        $this->ignoreAttributes = $this->ignoreAttributes->merge(collect($attributes));

        return $this;
    }

    public function ignoreSoftDelete(): self
    {
        $this->initIgnoreAttributes();
        $this->ignoreAttributes = $this->ignoreAttributes->merge(['deleted_at']);

        return $this;
    }

    public function ignoreTimestamps(): self
    {
        $this->initIgnoreAttributes();
        $this->ignoreAttributes = $this->ignoreAttributes->merge(['created_at', 'updated_at']);
        return $this;
    }

    protected function initIgnoreAttributes()
    {
        if (!$this->ignoreAttributes) {
            $this->ignoreAttributes= collect([]);
        }
    }


}