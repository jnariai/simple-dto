<?php

namespace SimpleDTO\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SimpleDTO\DTOServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DTOServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
