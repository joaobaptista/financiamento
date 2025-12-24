<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule
            ->job(new \App\Jobs\SendCheckoutIncompleteRemindersJob)
            ->everyTenMinutes()
            ->withoutOverlapping();

        $schedule
            ->job(new \App\Jobs\SendCampaignEndingSoonNotificationsJob)
            ->hourly()
            ->withoutOverlapping();

        $schedule
            ->job(new \App\Jobs\FinalizeEndedCampaignsJob)
            ->hourly()
            ->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
