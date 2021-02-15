<?php declare(strict_types=1);

namespace Stylers\TaskManager\Contracts;

interface TaskTimerInterface
{
    public function isTimeToExecute(): bool;

    // Recommended command class method to use an entrypoint
    public function handle();
}
