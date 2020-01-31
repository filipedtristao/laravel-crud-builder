<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\CrudRelation;
use CrudBuilder\Exceptions\MissingIdException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

Trait AddsCrudMethodsToModel
{

    public function createOrUpdate(): Model
    {
        return $this->save();
    }

    public function save(): Model
    {
        $id = $this->getRequestId();

        if ($id) {
            return $this->update();
        }

        return $this->create();
    }

    protected function getRequestId()
    {
        return $this->request->input('data.id');
    }

    public function update(): Model
    {
        $id = $this->getRequestId();

        if ($id) {
            $modelAttributes = $this->model->getAttributes();
            $this->model = $this->model->query()->find($id);
            $this->model->fill($modelAttributes);

            $this->saveModelAndRelations();

            return $this->model;
        }

        throw new MissingIdException();
    }

    protected function saveModelAndRelations()
    {
        $this->syncLocalRelations();
        $this->model->save();
        $this->syncRelatedRelations();
    }

    protected function syncLocalRelations()
    {
        if (!$this->allowedRelations) {
            return;
        }

        $this
            ->allowedRelations
            ->only($this->getRequestRelationKeys())
            ->filter(function (CrudRelation $crudRelation) {
                return $crudRelation->getRelation() instanceof BelongsTo;
            })
            ->each(function (CrudRelation $crudRelation) {
                $requestRelation = $this->getRequestRelation($crudRelation->getRelationName());

                $relationId = ($requestRelation && array_key_exists('id', $requestRelation))
                    ? $requestRelation['id']
                    : null;

                if ($relationId) {
                    $crudRelation->getRelation()->getRelated()->findOrFail($relationId);
                }

                $foreignKeyName = $this->getForeignKeyName($crudRelation->getRelation());
                $this->model->{$foreignKeyName} = $relationId;
            });
    }

    protected function getRequestRelationKeys()
    {
        $relations = $this->request->input('data.relationships');

        if ($relations) {
            return array_keys($relations);
        } else {
            return [];
        }
    }

    protected function getRequestRelation(string $relationName)
    {
        return $this->request->input('data.relationships.' . $relationName . '.data');
    }

    protected function getForeignKeyName($relation)
    {
        if (method_exists($relation, 'getForeignKeyName')) {
            return $relation->getForeignKeyName();
        } else if (method_exists($relation, 'getForeignKey')) {
            return $relation->getForeignKey();
        }
    }

    protected function syncRelatedRelations()
    {
        if (!$this->allowedRelations) {
            return;
        }

        $this
            ->allowedRelations
            ->only($this->getRequestRelationKeys())
            ->filter(function (CrudRelation $crudRelation) {
                return !($crudRelation->getRelation() instanceof BelongsTo);
            })
            ->each(function (CrudRelation $crudRelation) {

                $requestRelation = $this->getRequestRelation($crudRelation->getRelationName());

                if ($crudRelation->getRelation() instanceof HasOne || $crudRelation->getRelation() instanceof MorphOne) {
                    $this->syncRelatedRelation($crudRelation, $requestRelation);
                } else if ($crudRelation->getRelation() instanceof HasMany || $crudRelation->getRelation() instanceof MorphMany) {

                    foreach ($requestRelation as $relation) {
                        $this->syncRelatedRelation($crudRelation, $relation);
                    }
                } else {
                    throw new Exception('Relation type [' . get_class($crudRelation->getRelation()) . '] not suported');
                }
            });
    }

    protected function syncRelatedRelation($crudRelation, $requestRelation)
    {
        $relationId = ($requestRelation && array_key_exists('id', $requestRelation))
            ? $requestRelation['id']
            : null;

        if ($crudRelation->getRelation() instanceof MorphOne || $crudRelation->getRelation() instanceof MorphMany) {
            $this->detachCurrentMorphedRelations($crudRelation, $relationId);
        }

        if (!$relationId) {
            return;
        }

        $relatedModel = $crudRelation->getRelation()->getRelated()->findOrFail($relationId);

        $updateData = [
            $crudRelation->getRelation()->getForeignKeyName() => $this->model->id
        ];

        if ($crudRelation->getRelation() instanceof MorphOne || $crudRelation->getRelation() instanceof MorphMany) {
            $updateData = [
                $crudRelation->getRelation()->getForeignKeyName() => $this->model->id,
                $crudRelation->getRelation()->getMorphType() => get_class($this->model)
            ];
        }

        $relatedModel->update($updateData);
    }

    protected function detachCurrentMorphedRelations(CrudRelation $crudRelation, $relationId)
    {
        $currentRelated = $this->model->{$crudRelation->getRelationName()};

        if ($currentRelated) {
            if ($currentRelated instanceof Collection) {
                foreach ($currentRelated as $related) {
                    $this->detachCurrentMorphedRelation($crudRelation, $related);
                }
            } else {
                if ($relationId != $currentRelated->{$currentRelated->getKeyName()}) {
                    $this->detachCurrentMorphedRelation($crudRelation, $currentRelated);
                }
            }
        }
    }

    protected function detachCurrentMorphedRelation(CrudRelation $crudRelation, Model $related)
    {
        $related->update([
            $crudRelation->getRelation()->getForeignKeyName() => null,
            $crudRelation->getRelation()->getMorphType() => null
        ]);
    }

    public function create(): Model
    {
        $this->saveModelAndRelations();
        return $this->model;
    }
}