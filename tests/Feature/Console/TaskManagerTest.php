<?php declare(strict_types=1);

namespace Stylers\TaskManager\Tests\Feature\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use Stylers\TaskManager\Console\TaskManager;
use Stylers\TaskManager\Contracts\TaskTimerInterface;
use Stylers\TaskManager\Tests\Fixtures\CommandTask;
use Stylers\TaskManager\Tests\TestCase;

class TaskManagerTest extends TestCase
{
    protected const HOUR = 7;
    protected const MINUTE = 40;
    protected const WEEKDAYS = 5;
    protected const WEEKEND_DAYS = 2;

    /** @var TaskManager */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = app(TaskManager::class);
        $this->subject->commands = [];
    }

    // Test setters

    /**
     * @test
     */
    public function it_can_set_task()
    {
        $task = (new CommandTask)->cron(sprintf('%d * * * *', self::MINUTE));

        $this->subject->addTask($task);

        self::assertNotEmpty($this->subject->commands);
        self::assertContains($task, $this->subject->commands);
    }

    /**
     * @test
     */
    public function it_can_add_multiple_tasks_at_once()
    {
        $task = (new CommandTask)->daily();
        $task2 = (new CommandTask)->daily();

        $this->subject->bulkAddTasks([$task, $task2]);

        self::assertNotEmpty($this->subject->commands);
        self::assertCount(2, $this->subject->commands);
        self::assertContains($task, $this->subject->commands);
        self::assertContains($task2, $this->subject->commands);
    }

    /**
     * @test
     */
    public function it_can_not_add_multiple_tasks_if_type_is_mismatch()
    {
        $this->expectException(\Throwable::class);

        $task = new Command();

        $this->subject->bulkAddTasks([$task]);
    }

    // Test timing

    /**
     * @test
     */
    public function it_can_function_is_time_to_execute_is_true_with_static_time_input()
    {
        $task = $this->mockTaskMethodIsTimeToExecuteAndReturn(true);

        $this->subject->addTask($task);

        $this->subject->executeTasksForThisTime();

        self::assertTrue($task->isTimeToExecute());
    }

    /**
     * @test
     */
    public function it_can_function_is_time_to_execute_is_false_with_static_time_input()
    {
        $task = $this->mockTaskMethodIsTimeToExecuteAndReturn(false);

        $this->subject->addTask($task);

        $this->subject->executeTasksForThisTime();

        self::assertFalse($task->isTimeToExecute());
    }

    public static function processTimeDataProvider(): array
    {
        $currentMinute = Carbon::now()->minute;
        $beforeCurrentMinute = abs($currentMinute - 10);

        return [
            'run - run' => [self::once(), $currentMinute, true, self::once(), $currentMinute, true],
            'run - not run' => [self::once(), $currentMinute, true, self::never(), $beforeCurrentMinute, false],
            'not run - not run' => [self::never(), $beforeCurrentMinute, false, self::never(), $beforeCurrentMinute, false],
        ];
    }

    /**
     * @test
     * @dataProvider processTimeDataProvider
     * @param InvokedCount $task1Times
     * @param int $task1Interval
     * @param bool $task1IsTimeToExecute
     * @param InvokedCount $task2Times
     * @param int $task2Interval
     * @param bool $task2IsTimeToExecute
     */
    public function it_can_handle_correcty_all_tasks_time(
        InvokedCount $task1Times,
        int $task1Interval,
        bool $task1IsTimeToExecute,
        InvokedCount $task2Times,
        int $task2Interval,
        bool $task2IsTimeToExecute
    )
    {
        /** @var TaskTimerInterface $task */
        $task = $this->assertMockTaskMethodHandleCalled($task1Times, $task1Interval);
        $task2 = $this->assertMockTaskMethodHandleCalled($task2Times, $task2Interval);

        $this->subject->bulkAddTasks([$task, $task2]);

        self::assertEquals($task1IsTimeToExecute, $task->isTimeToExecute());
        self::assertEquals($task2IsTimeToExecute, $task2->isTimeToExecute());

        self::assertNotEmpty($this->subject->commands);

        $this->subject->executeTasksForThisTime();
    }

    /**
     * @param bool $returnValue
     * @return object|TaskTimerInterface
     */
    private function mockTaskMethodIsTimeToExecuteAndReturn(bool $returnValue)
    {
        return $this->instance(
            CommandTask::class,
            \Mockery::mock(CommandTask::class, function (MockInterface $mock) use ($returnValue) {
                $mock
                    ->shouldReceive('isTimeToExecute')
                    ->andReturn($returnValue);
            })
        );
    }

    /**
     * @param InvokedCount $times
     * @param int $minutes
     * @return object|TaskTimerInterface
     */
    private function assertMockTaskMethodHandleCalled(InvokedCount $times, int $minutes)
    {
        $mock = $this
            ->getMockBuilder(CommandTask::class)
            ->onlyMethods(['handle'])
            ->getMock();

        $mock->cron(sprintf('%d * * * *', $minutes));

        $mock->expects($times)->method('handle');

        return $mock;
    }
}
