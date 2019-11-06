<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\Exceptions\InvalidAttributeException;
use Illuminate\Support\Collection;

trait AddsAttributesToModel
{

    /**
     * @var Collection
     */
    protected $allowedAttributes;

    /**
     * @var Collection
     */
    protected $attributeTransformers;

    public function allowedAttributes($attributes): self
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        $this->allowedAttributes = collect($attributes)
            ->map(function ($attribute, $index) {
                return $this->prependAttribute($index, $attribute);
            });

        $this->attributeTransformers = collect($attributes)
            ->filter(function($attribute) {
                return is_callable($attribute);
            });

        $this->ensureAllAttributesExist();
        $this->addRequestedAttributesToModel();

        return $this;
    }

    protected function prependAttribute($index, $attribute): string
    {
        if (is_callable($attribute)) {
            return $index;
        }

        return $attribute;
    }

    protected function getAttributeFromRequest($attributeName)
    {
        return $this->request->input('data.attributes.' . $attributeName);
    }

    protected function getAttributesFromRequest()
    {
        return collect($this->request->input('data.attributes', []));
    }

    protected function addRequestedAttributesToModel()
    {
        $this->getAttributesFromRequest()
            ->each(function ($attribute, $index) {
                $transform = $this->attributeTransformers->get($index);

                if ($transform) {
                    $this->model{$index} = $transform($attribute);
                } else {
                    $this->model{$index} = $attribute;
                }
            });
    }

    protected function ensureAllAttributesExist()
    {
        $requestedAttributes = collect($this->request->input('data.attributes'))
            ->keys()
            ->unique();

        $unknownFields = $requestedAttributes->diff($this->allowedAttributes);

        if ($unknownFields->isNotEmpty()) {
            throw InvalidAttributeException::attributesNotAllowed($unknownFields, $this->allowedAttributes);
        }
    }

}