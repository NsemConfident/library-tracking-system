<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('library:send-due-date-reminders --days=3')
    ->daily()
    ->at('09:00')
    ->description('Send due date reminders 3 days before books are due');

Schedule::command('library:send-overdue-notifications --days=1')
    ->daily()
    ->at('09:00')
    ->description('Send overdue notifications for books overdue by 1 or more days');
