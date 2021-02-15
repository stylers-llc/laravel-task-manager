<?php declare(strict_types=1);

namespace Stylers\TaskManager\Console;

use Exception;
use InvalidArgumentException;
use Stylers\TaskManager\Contracts\TaskTimerInterface;
use Illuminate\Support\Facades\Log;

class TaskManager
{
    /** @var array */
    public $commands = [];

    // static method for run scheduler easily in one line
    public static function run(): void
    {
        $scheduler = app(__CLASS__);

        $scheduler->executeTasksForThisTime();
    }

    public function addTask(TaskTimerInterface $task): void
    {
        $this->commands[] = $task;
    }

    public function bulkAddTasks(array $tasks): void
    {
        foreach ($tasks as $task) {
            if (! ($task instanceof TaskTimerInterface)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Argument passed in array to %s::bulkAddTask() must be an implementation of %s',
                        __CLASS__,
                        TaskTimerInterface::class
                    )
                );
            }

            $this->commands[] = $task;
        }
    }

    public function executeTasksForThisTime(): void
    {
        foreach ($this->commands as $task) {
            if ($task->isTimeToExecute()) {
                // Catch exception if occurred and continue other tasks
                try {
                    $task->handle(); // Command's default method execution
                } catch (Exception $e) {
                    Log::warning($e->getMessage());
                }
            }
        }
    }
}
