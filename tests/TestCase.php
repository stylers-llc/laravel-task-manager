<?php declare(strict_types=1);

namespace Stylers\TaskManager\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Stylers\TaskManager\Providers\TaskManagerServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TaskManagerServiceProvider::class,
        ];
    }
}
