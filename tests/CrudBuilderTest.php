<?php

namespace CrudBuilder\Tests;

use CrudBuilder\CrudBuilder;
use CrudBuilder\Tests\TestClasses\Models\SingerModel;

class QueryBuilderTest extends TestCase
{
    /** @test */
    public function it_will_create_for_given_model()
    {
        $builder = CrudBuilder::for(SingerModel::class);
        $this->assertInstanceOf(CrudBuilder::class, $builder);
    }
}