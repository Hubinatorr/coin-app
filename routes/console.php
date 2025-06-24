<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:fetch-currency-data')->everyFifteenSeconds();
