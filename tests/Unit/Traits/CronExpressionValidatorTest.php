<?php declare(strict_types=1);

namespace Stylers\TaskManager\Tests\Unit\Traits;

use InvalidArgumentException;
use Stylers\TaskManager\Tests\TestCase;
use Stylers\TaskManager\Tests\Fixtures\CronExpressionValidator;

class CronExpressionValidatorTest extends TestCase
{
    public function outOfIntervalValuesDataProvider(): array
    {
        $macroExceptionMsg = 'Macro doesn\'t exists: @non-exists-macro';
        $dayOfWeekExceptionMsg = 'Day of Week must be a number between 0 and 7 (Sunday=0 or 7) or "*", "/", "-", ",".';
        $monthExceptionMsg = 'Month must be a number between 1 and 12 or "*", "/", "-", ",".';
        $dayExceptionMsg = 'Day of Month must be a number between 1 and 31 or "*", "/", "-", ",".';
        $hourExceptionMsg = 'Hour must be a number between 0 and 23 or "*", "/", "-", ",".';
        $minuteExceptionMsg = 'Minute must be a number between 0 and 59 or "*", "/", "-", ",".';

        return [
            'non-exists-macro' =>           ['@non-exists-macro', $macroExceptionMsg],
            'under_day_of_week_interval' => ['* * * * -1', $dayOfWeekExceptionMsg],
            'above_day_of_week_interval' => ['* * * * 8', $dayOfWeekExceptionMsg],
            'under_month_interval' =>       ['* * * 0 *', $monthExceptionMsg],
            'above_month_interval' =>       ['* * * 13 *', $monthExceptionMsg],
            'under_day_interval' =>         ['* * 0 * *', $dayExceptionMsg],
            'above_day_interval' =>         ['* * 32 * *', $dayExceptionMsg],
            'under_hour_interval' =>        ['* -1 * * *', $hourExceptionMsg],
            'above_hour_interval' =>        ['* 24 * * *', $hourExceptionMsg],
            'under_minute_interval' =>      ['-1 * * * *', $minuteExceptionMsg],
            'above_minute_interval' =>      ['60 * * * *', $minuteExceptionMsg],
        ];
    }

    /**
     * @test
     * @dataProvider outOfIntervalValuesDataProvider
     */
    public function it_can_handle_wrong_input(string $cronExpression, string $exceptionMsg)
    {
        $subject = new CronExpressionValidator;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMsg);

        $subject->validateExpression($cronExpression);
    }
}
