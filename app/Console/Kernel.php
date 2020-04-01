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
            ->everyTenMinutes();

        $schedule->command(ParseIssuesCommand::class)
           ->everyTenMinutes()
           ->withoutOverlapping(4)
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
