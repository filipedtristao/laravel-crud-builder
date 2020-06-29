<?php

namespace CrudBuilder\Tests;

use CrudBuilder\CrudBuilder;
use CrudBuilder\Tests\TestClasses\Models\BandModel;
use CrudBuilder\Tests\TestClasses\Models\SingerModel;
use Illuminate\Http\Request;

class AllowedRelationTest extends TestCase
{
    /** @test */
    public function it_can_add_single_relation_to_model()
    {
        $band = BandModel::create([
            'name' => 'The Beatles'
        ]);

        $request = $this->createRequest([
            'attributes' => [
                'name' => 'Paul McCartney',
                'age' => '77'
            ],
            'relationships' => [
                'band' => [
                    'data' => [
                        'id' => '1'
                    ]
                ]
            ]
        ]);

        $singer = CrudBuilder::for(SingerModel::class, $request)
            ->allowedAttributes(['name', 'age'])
            ->allowedRelations(['band'])
            ->create();

        $this->assertInstanceOf(BandModel::class, $singer->band);
        $this->assertEquals($band->name, $singer->band->name);
    }

    /** @test */
    public function it_can_add_many_relations_to_model()
    {
        $singer = SingerModel::create([
            'name' => 'Paul McCartney',
            'age' => 77
        ]);

        $request = $this->createRequest([
            'attributes' => [
                'name' => 'The Beatles',
            ],
            'relationships' => [
                'members' => [
                    'data' => [
                        ['id' => $singer->id]
                    ]
                ]
            ]
        ]);

        $band = CrudBuilder::for(BandModel::class, $request)
            ->allowedAttributes(['name'])
            ->allowedRelations(['members'])
            ->create();

        $singer->refresh();

        $this->assertCount(1, $band->members);
        $this->assertEquals($singer->band_id, $band->id);
    }

    protected function createRequest(array $data): Request
    {
        return new Request([
            'data' => $data
        ]);
    }
}