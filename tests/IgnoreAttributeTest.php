<?php

namespace CrudBuilder\Tests;

use CrudBuilder\CrudBuilder;
use CrudBuilder\Tests\TestClasses\Models\SingerModel;
use Illuminate\Http\Request;

class IgnoreAttributeTest extends TestCase
{

    /** @test */
    public function it_can_ignore_attribute_in_model()
    {
        $model = $this
            ->createBuilderFromAttributeRequest([
                'name' => 'John',
                'age' => 40
            ])
            ->ignoreAttributes(['age'])
            ->allowedAttributes(['name'])
            ->create();

        $this->assertEquals($model->name, 'John');
        $this->assertNull($model->age);
    }

    /** @test */
    public function it_can_ignore_multiple_attributes_in_model()
    {
        $model = $this
            ->createBuilderFromAttributeRequest([
                'name' => 'John',
                'surname' => 'Lennon',
                'age' => 40
            ])
            ->ignoreAttributes(['age', 'surname'])
            ->allowedAttributes(['name'])
            ->create();

        $this->assertEquals($model->name, 'John');
        $this->assertNull($model->age);
        $this->assertNull($model->surname);
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