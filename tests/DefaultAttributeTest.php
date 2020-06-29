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
        $model = $this->createBuilderFromAttributeRequest([])
            ->defaultAttributes([
                'name' => 'John'
            ])
            ->create();

        $this->assertEquals($model->name, 'John');
    }

    /** @test */
    public function it_can_add_multiple_default_attributes_in_model()
    {
        $model = $this->createBuilderFromAttributeRequest([])
            ->defaultAttributes([
                'name' => 'John',
                'age' => 40
            ])
            ->create();

        $this->assertEquals($model->name, 'John');
        $this->assertEquals($model->age, 40);
    }

    /** @test */
    public function it_allowed_atributes_overrides_default_attributes_on_create()
    {
        $model = $this
            ->createBuilderFromAttributeRequest([
                'name' => 'Paul',
                'age' => 77
            ])
            ->defaultAttributes([
                'name' => 'John',
                'age' => 40
            ])
            ->allowedAttributes('name', 'age')
            ->create();

        $this->assertEquals($model->name, 'Paul');
        $this->assertEquals($model->age, 77);
    }

    /** @test */
    public function it_allowed_atributes_overrides_default_attributes_on_update()
    {
        $singer = SingerModel::create([
            'name' => 'Ringo'
        ]);

        $model = $this
            ->createBuilderFromAttributeRequest([
                'age' => '79',
            ], $singer->id)
            ->defaultAttributes([
                'name' => 'John',
                'age' => 40
            ])
            ->allowedAttributes('name', 'age')
            ->update();

        $this->assertEquals($model->id, $singer->id);
        $this->assertEquals($model->name, 'Ringo');
        $this->assertEquals($model->age, 79);
    }

    protected function createBuilderFromAttributeRequest(array $attributes, $id = null): CrudBuilder
    {
        $request = new Request([
            'data' => [
                'id' => $id,
                'attributes' => $attributes
            ]
        ]);

        return CrudBuilder::for(SingerModel::class, $request);
    }
}