# Laravel Task Manager
![Tests](https://github.com/stylers-llc/laravel-task-manager/workflows/Tests/badge.svg)
[![codecov](https://codecov.io/gh/stylers-llc/laravel-task-manager/branch/master/graph/badge.svg?token=QYYV44SUOX)](https://codecov.io/gh/stylers-llc/laravel-task-manager)
[![Maintainability](https://api.codeclimate.com/v1/badges/cb27855f8162042e522f/maintainability)](https://codeclimate.com/repos/601989c1f8bfa307f40075d5/maintainability)  

## Requirements
- PHP >= 7.2.5
- Laravel >= 5.7

## Installation
```bash
composer require stylers/laravel-task-manager
```

## Usage
TaskTimer trait is using [Laravel's Schedule Frequency Options](https://laravel.com/docs/5.7/scheduling#schedule-frequency-options)
1. Add Interface and Trait to class implementation and add handle method for a command entrypoint

```php
use Illuminate\Console\Command;
use Stylers\TaskManager\Contracts\TaskTimerInterface;
use Stylers\TaskManager\Traits\TaskTimer;

class CommandTask extends Command implements TaskTimerInterface
{
    use TaskTimer;

    public function handle()
    {
        //
    }
}
```

2. Add CommandTask to TaskManager 

```php
use Illuminate\Support\ServiceProvider;
use Stylers\TaskManager\Console\TaskManager;
use Stylers\TaskManager\Tests\Fixtures\CommandTask;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->app->booted(function () {
            $scheduler = app(TaskManager::class);
                        
            // Single task adding
            $scheduler->addTasks(
                (new CommandTask())->dailyAt('6:00')
            );
            
            // ---- OR ----

            // Multiple task adding in same time
            $scheduler->bulkAddTasks([
                (new CommandTask())->dailyAt('6:00'),
                (new CommandTask())->weekly(),
            ]);
        });
    }
}
```

3. In example called in web.php  
(please feel free to add security checks for endpoint)

```php
use Stylers\TaskManager\Console\TaskManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/cron', static function (Request $request) {
    TaskManager::run();
});
```

4. Set cron to call this endpoint
```editorconfig
* * * * * curl -s -X GET -L domain/cron >>/var/log/cron.log 2>&1
```

## Special thanks to:
- [WebInvention](https://web-invention.com/blog/post/6/alternative-task-scheduling-laravel-proc-open-disabled) (for this blog post)
