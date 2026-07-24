<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('db:backup-sqlite')->dailyAt('08:30')->timezone('Asia/Manila');
Schedule::command('db:backup-sqlite')->dailyAt('15:00')->timezone('Asia/Manila');
