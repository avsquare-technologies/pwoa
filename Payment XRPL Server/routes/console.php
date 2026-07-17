<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::command('xrpl:check-reserves')
    ->twiceDailyAt()
    ->appendOutputTo(storage_path('logs/xrpl_reserve.log'));
