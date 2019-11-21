<?php

namespace CrudBuilder\Concerns;

use CrudBuilder\CrudRelation;
use CrudBuilder\Exceptions\MissingIdException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

Trait AddsCrudMethodsToModel
{

    public function save(): Model
    {
        $id = $this->getRequestId();

        if ($id) {
            return $this->update();
        }

        return $this->create();
    }

    public function create(): Model
    {
        $this->saveModelAndRelations();
        return $this->model;
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

    public function createOrUpdate(): Model
    {
        return $this->save();
    }

    protected function saveModelAndRelations()
    {
        $this->syncLocalRelations();
        $this->model->save();
        $this->syncRelatedRelations();
    }

    protected function syncLocalRelations()
    {
        if (!$this->allowedRelations){
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
        if (!$this->allowedRelations){
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

                if ($crudRelation->getRelation() instanceof HasOne) {
                    $this->syncRelatedRelation($crudRelation, $requestRelation);
                } else if ($crudRelation->getRelation() instanceof HasMany) {

                    foreach ($requestRelation as $relation) {
                        $this->syncRelatedRelation($crudRelation, $relation);
                    }
                } else {
                    throw new \Exception('Relation type [' . get_class($crudRelation->getRelation()) . '] not suported');
                }
            });
    }

    protected function syncRelatedRelation($crudRelation, $requestRelation)
    {
        $relationId = ($requestRelation && array_key_exists('id', $requestRelation))
            ? $requestRelation['id']
            : null;

        if (!$relationId) {
            return;
        }

        $relatedModel = $crudRelation->getRelation()->getRelated()->findOrFail($relationId);

        $relatedModel->update([
            $crudRelation->getRelation()->getForeignKeyName() => $this->model->id
        ]);
    }

    protected function getRequestId()
    {
        return $this->request->input('data.id');
    }

    protected function getRequestRelation(string $relationName)
    {
        return $this->request->input('data.relationships.' . $relationName . '.data');
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
}