<?php declare(strict_types=1);

namespace Stylers\TaskManager\Contracts;

interface TaskTimerInterface
{
    // Checks task is runnable at now
    public function isTimeToExecute(): bool;

    // Recommended command class method to use an entrypoint
    public function handle();
}
