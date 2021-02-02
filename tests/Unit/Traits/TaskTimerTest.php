<?php declare(strict_types=1);

namespace Stylers\TaskManager\Tests\Unit\Console;

use Carbon\Carbon;
use Cron\CronExpression;
use Stylers\TaskManager\Contracts\TaskTimerInterface;
use Stylers\TaskManager\Tests\TestCase;
use Stylers\TaskManager\Tests\Fixtures\CommandTask;

class TaskTimerTest extends TestCase
{
    protected const HOUR = 7;
    protected const MINUTE = 40;
    protected const WEEKDAYS = 5;
    protected const WEEKEND_DAYS = 2;

    protected const TIME_PATTERN = '%d:%d';

    /** @var TaskTimerInterface */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new CommandTask;
    }

    // Test setters

    /**
     * @test
     */
    public function it_can_set_specified_minutes()
    {
        $task = $this->subject->hourlyAt(self::MINUTE);

        $expectedExpression = CronExpression::factory(sprintf('%d * * * *', self::MINUTE))
            ->getExpression();

        self::assertEquals($expectedExpression, $task->expression);
    }

    /**
     * @test
     */
    public function it_can_set_every_week_on_monday_at_7_40()
    {
        $task = $this->subject->weeklyOn(Carbon::MONDAY, sprintf(self::TIME_PATTERN, self::HOUR, self::MINUTE));

        $expectedExpression = CronExpression::factory(
            sprintf('%d %d * * %d', self::MINUTE, self::HOUR, Carbon::MONDAY)
        )->getExpression();

        self::assertEquals($expectedExpression, $task->expression);
    }

    /**
     * @test
     */
    public function it_can_set_monthly()
    {
        $task = $this->subject->monthly();

        $expectedExpression = CronExpression::factory('@monthly')
            ->getExpression();

        self::assertEquals($expectedExpression, $task->expression);
    }

    /**
     * @test
     */
    public function it_can_set_monthly_on_friday_at_7_hour()
    {
        $task = $this->subject->monthlyOn(Carbon::FRIDAY, sprintf('%d:00', self::HOUR));

        $expectedExpression = CronExpression::factory('@monthly')
            ->setPart(CronExpression::DAY, Carbon::FRIDAY)
            ->setPart(CronExpression::HOUR, self::HOUR)
            ->getExpression();

        self::assertEquals($expectedExpression, $task->expression);
    }

    /**
     * @test
     */
    public function it_can_set_daily_at_7_40()
    {
        $task = $this->subject->dailyAt(sprintf(self::TIME_PATTERN, self::HOUR, self::MINUTE));

        $expectedExpression = CronExpression::factory('@daily')
            ->setPart(CronExpression::HOUR, self::HOUR)
            ->setPart(CronExpression::MINUTE, self::MINUTE)
            ->getExpression();

        self::assertEquals($expectedExpression, $task->expression);
    }

    /**
     * @test
     */
    public function it_can_make_new_instance()
    {
        $task = new CommandTask;

        $otherTask = new CommandTask;

        self::assertIsObject($task);
        self::assertInstanceOf(TaskTimerInterface::class, $task);
        self::assertNotSame($task, $otherTask);
    }

    // Test timing

    /**
     * @test
     */
    public function it_can_handle_is_time_to_execute_is_true()
    {
        $now = Carbon::now();

        $task = $this->subject->weeklyOn($now->dayOfWeek, sprintf(self::TIME_PATTERN, $now->hour, $now->minute));

        self::assertTrue($task->isTimeToExecute());
    }

    /**
     * @test
     */
    public function it_can_handle_is_time_to_execute_is_false()
    {
        $now = Carbon::now();

        $task = $this->subject->weeklyOn($now->dayOfWeek, sprintf(self::TIME_PATTERN, $now->hour, $now->minute + 1));

        self::assertNotTrue($task->isTimeToExecute());
    }
}
