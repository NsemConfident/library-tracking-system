<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('library:send-due-date-reminders')
    ->daily()
    ->at('09:00')
    ->description('Send due date reminders before books are due');

Schedule::command('library:send-overdue-notifications')
    ->daily()
    ->at('09:00')
    ->description('Send overdue notifications for overdue books');
