<?php declare(strict_types=1);

namespace Stylers\TaskManager\Contracts;

interface TaskTimerInterface
{
    public function isTimeToExecute(): bool;

    public function handle();
}
