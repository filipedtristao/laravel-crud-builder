<?php

namespace CrudBuilder\Tests;

use CrudBuilder\CrudBuilder;
use CrudBuilder\Tests\TestClasses\Models\SingerModel;
use Illuminate\Http\Request;

class DefaultAttributeTest extends TestCase
{

    /** @test */
    public function it_can_add_default_attribute_in_model()
    {
        $builder = $this->createBuilderFromAttributeRequest([])
            ->defaultAttributes([
                'name' => 'John'
            ]);

        $model = $builder->getModel();
        $this->assertEquals($model->name, 'John');
    }

    /** @test */
    public function it_can_add_multiple_default_attributes_in_model()
    {
        $builder = $this->createBuilderFromAttributeRequest([])
            ->defaultAttributes([
                'name' => 'John',
                'age' => 40
            ]);

        $model = $builder->getModel();
        $this->assertEquals($model->name, 'John');
        $this->assertEquals($model->age, 40);
    }

    /** @test */
    public function it_allowed_atributes_overrides_default_attributes()
    {
        $builder = $this
            ->createBuilderFromAttributeRequest([
                'name' => 'Paul',
                'age' => 77
            ])
            ->defaultAttributes([
                'name' => 'John',
                'age' => 40
            ])
            ->allowedAttributes('name', 'age');

        $model = $builder->getModel();
        $this->assertEquals($model->name, 'Paul');
        $this->assertEquals($model->age, 77);
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