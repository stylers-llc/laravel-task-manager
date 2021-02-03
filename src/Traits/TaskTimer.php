<?php declare(strict_types=1);

namespace Stylers\TaskManager\Traits;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Log;

trait TaskTimer
{
    use ManagesFrequencies;
    use CronExpressionValidator;

    public $expression = '* * * * *'; // Cron expression
    public $timezone;

    public function isTimeToExecute(): bool
    {
        try {
            return CronExpression::factory($this->expression)
                ->isDue('now', $this->timezone);
        } catch (\InvalidArgumentException $e) {
            Log::warning($e->getMessage());

            return false;
        }
    }
}
