<?php declare(strict_types=1);

namespace Stylers\TaskManager\Tests\Fixtures;

use Illuminate\Console\Command;
use Stylers\TaskManager\Contracts\TaskTimerInterface;
use Stylers\TaskManager\Traits\TaskTimer;

class CommandTask extends Command implements TaskTimerInterface
{
    use TaskTimer;

    public function handle()
    {
        // This is necessary for testing
    }
}
