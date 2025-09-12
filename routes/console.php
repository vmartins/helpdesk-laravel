<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty')->everyMinute();
Schedule::command('app:close-tickets-without-interactions')->daily();