<?php declare(strict_types=1);

namespace Stylers\TaskManager\Traits;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Log;

trait TaskTimer
{
    use ManagesFrequencies;
    use CronExpressionValidator;

    protected $expression = '* * * * *'; // Cron expression
    public $timezone;

    public function __set($name, $value)
    {
        if ($name === 'expression') {
            $this->validateExpression($value);
        }

        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __isset($name): bool
    {
        return isset($this->$name);
    }

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
