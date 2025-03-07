<?php

declare(strict_types=1);

namespace SimpleDTO\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SimpleDTO\DTOServiceProvider;

abstract class TestCase extends Orchestra
{
    final public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }

    protected function getPackageProviders($app): array
    {
        return [
            DTOServiceProvider::class,
        ];
    }
}
