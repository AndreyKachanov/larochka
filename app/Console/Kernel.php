<?php

namespace App\Console;

use App\Console\Commands\Email\ParseMailBoxCommand;
use App\Console\Commands\Jira\ParseIssuesCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(ParseMailBoxCommand::class)
            ->withoutOverlapping(9)
            ->weekdays()
            ->twiceDaily(6, 15)
            ->appendOutputTo(storage_path('logs/schedule-parse_mail_command.log'))
            ->emailOutputOnFailure('andrey170749@yandex.ru');

        $schedule->command(ParseIssuesCommand::class)
            ->withoutOverlapping(4)
            ->weekdays()
            ->twiceDaily(1, 12)
            ->appendOutputTo(storage_path('logs/schedule-parse_issues_command.log'))
            ->emailOutputOnFailure('andrey170749@yandex.ru');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
