<?php declare(strict_types=1);

namespace Stylers\TaskManager\Providers;

use Illuminate\Support\ServiceProvider;
use Stylers\TaskManager\Console\TaskManager;

class TaskManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        dd("it's working");
    }
    
    public function register()
    {
        $this->app->singleton(TaskManager::class, function () {
            return new TaskManager();
        });
    }
}
