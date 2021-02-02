<?php

namespace Stylers\TaskManager\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Stylers\TaskManager\Providers\TaskManagerServiceProvider;

/**
 * Class TestCase
 * @package Stylers\TaskManager\Tests
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            TaskManagerServiceProvider::class,
        ];
    }
}
