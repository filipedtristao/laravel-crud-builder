<?php

namespace CrudBuilder\Tests;

use CrudBuilder\CrudBuilder;
use CrudBuilder\Tests\TestClasses\Models\SingerModel;
use Illuminate\Http\Request;

class AllowedAttributeTest extends TestCase
{

    /** @test */
    public function it_can_add_attribute_in_model()
    {
        $builder = $this->createBuilderFromAttributeRequest([
            'name' => 'Paul'
        ])->allowedAttributes('name');

        $model = $builder->getModel();
        $this->assertEquals('Paul', $model->name);
    }

    /** @test */
    public function it_can_add_multiple_attributes_in_model()
    {
        $builder = $this->createBuilderFromAttributeRequest([
            'name' => 'Paul',
            'age' => 77
        ])->allowedAttributes('age', 'name');

        $model = $builder->getModel();
        $this->assertEquals('Paul', $model->name);
        $this->assertEquals(77, $model->age);
    }

    /** @test */
    public function it_can_transform_attribute_in_model()
    {
        $builder = $this->createBuilderFromAttributeRequest([
            'name' => 'Paul'
        ])->allowedAttributes([
            'name' => function ($name) {
                return $name . ' McCartney';
            }
        ]);

        $model = $builder->getModel();
        $this->assertEquals('Paul McCartney', $model->name);
    }

    /** @test */
    public function it_can_transform_multiple_attributes_in_model()
    {
        $builder = $this->createBuilderFromAttributeRequest([
            'name' => 'Paul',
            'age' => 27
        ])->allowedAttributes([
            'name' => function ($name) {
                return $name . ' McCartney';
            },
            'age' => function($age) {
                return $age + 50;
            }
        ]);

        $model = $builder->getModel();
        $this->assertEquals('Paul McCartney', $model->name);
        $this->assertEquals(77, $model->age);
    }

    protected function createBuilderFromAttributeRequest(array $attributes): CrudBuilder
    {
        $request = new Request([
            'data' => [
                'attributes' => $attributes
            ]
        ]);

        return CrudBuilder::for(SingerModel::class, $request);
    }
}