<?php


namespace RadiateCode\DaStats\tests;


use RadiateCode\DaStats\StatsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [StatsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {

    }
}