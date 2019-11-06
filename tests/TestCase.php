<?php

namespace CrudBuilder\Tests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
        $this->withFactories(__DIR__.'/factories');
    }

    protected function setUpDatabase(Application $app)
    {

        $schemaBuilder = $app['db']->connection()->getSchemaBuilder();

        $schemaBuilder->create('singers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->integer('age');
            $table->integer('band_id')->nullable();
            $table->boolean('is_visible')->default(true);
        });

        $schemaBuilder->create('bands', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->integer('vocalist_id')->nullable();
            $table->boolean('is_visible')->default(true);
        });

    }

    protected function getPackageProviders($app)
    {
        return [];
    }

    protected function assertQueryLogContains(string $partialSql)
    {
        $queryLog = collect(DB::getQueryLog())->pluck('query')->implode('|');
        // Could've used `assertStringContainsString` but we want to support L5.5 with PHPUnit 6.0
        $this->assertTrue(Str::contains($queryLog, $partialSql));
    }

    protected function assertQueryLogDoesntContain(string $partialSql)
    {
        $queryLog = collect(DB::getQueryLog())->pluck('query')->implode('|');
        // Could've used `assertStringContainsString` but we want to support L5.5 with PHPUnit 6.0
        $this->assertFalse(Str::contains($queryLog, $partialSql), "Query log contained partial SQL: `{$partialSql}`");
    }
}