<?php declare(strict_types=1);

namespace Stylers\TaskManager\Traits;

use InvalidArgumentException;

trait CronExpressionValidator
{
    public function validateExpression(string $expression): void
    {
        $parts = explode(' ', $expression);

        if (strpos($expression, '@') === 0
            && ! preg_match(
                '/^@(annually|yearly|monthly|weekly|daily|hourly|reboot)|(@every (\d{1,4}(ns|us|µs|ms|s|m|h))+)$/u',
                $expression
            )) {
            throw new InvalidArgumentException(sprintf("Macro doesn't exists: %s", $expression));
        }

        if (! preg_match('/^(([1-5]?\d)([\/|,\-]\d{0,2})?|\*)$/', $parts[0])) {
            throw new InvalidArgumentException('Minute must be a number between 0 and 59 or "*", "/", "-", ",".');
        }

        if (! preg_match('/^((2[0-3]|1\d|\d)([\/,\-]\d{0,2})?|\*)$/', $parts[1])) {
            throw new InvalidArgumentException('Hour must be a number between 0 and 23 or "*", "/", "-", ",".');
        }

        if (! preg_match('/^((3[01]|[12]\d|[1-9])(\/\d+)?|\*|-)$/', $parts[2])) {
            throw new InvalidArgumentException('Day of Month must be a number between 1 and 31 or "*", "/", "-", ",".');
        }

        if (! preg_match('/^((1[0-2]|[1-9])(\/\d+)?|\*|-)$/', $parts[3])) {
            throw new InvalidArgumentException('Month must be a number between 1 and 12 or "*", "/", "-", ",".');
        }

        if (! preg_match('/^(([0-7])(\/\d+)?|\*|-)$/', $parts[4])) {
            throw new InvalidArgumentException('Day of Week must be a number between 0 and 7 (Sunday=0 or 7) or "*", "/", "-", ",".');
        }
    }
}
