<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Library Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control the default behavior of the library system.
    | They can be overridden by database settings if the settings table exists.
    |
    */

    // Loan Settings
    'loan_period_days' => env('LIBRARY_LOAN_PERIOD_DAYS', 14),
    'max_loans_per_patron' => env('LIBRARY_MAX_LOANS', 10),
    'renewal_period_days' => env('LIBRARY_RENEWAL_PERIOD_DAYS', 14),

    // Fine Settings
    'fine_rate_per_day' => env('LIBRARY_FINE_RATE_PER_DAY', 0.50),
    'lost_book_fee' => env('LIBRARY_LOST_BOOK_FEE', 50.00),
    'fine_due_days' => env('LIBRARY_FINE_DUE_DAYS', 30),

    // Hold Settings
    'hold_expiry_days' => env('LIBRARY_HOLD_EXPIRY_DAYS', 7),

    // Email Notification Settings
    'due_date_reminder_days' => env('LIBRARY_DUE_DATE_REMINDER_DAYS', 3),
    'overdue_notification_days' => env('LIBRARY_OVERDUE_NOTIFICATION_DAYS', 1),
];

