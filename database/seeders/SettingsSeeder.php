<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            // Loan Settings
            [
                'key' => 'loan_period_days',
                'value' => '14',
                'type' => 'integer',
                'group' => 'loans',
                'description' => 'Default loan period in days',
            ],
            [
                'key' => 'max_loans_per_patron',
                'value' => '10',
                'type' => 'integer',
                'group' => 'loans',
                'description' => 'Maximum number of active loans per patron',
            ],
            [
                'key' => 'renewal_period_days',
                'value' => '14',
                'type' => 'integer',
                'group' => 'loans',
                'description' => 'Number of days to extend loan when renewed',
            ],
            // Fine Settings
            [
                'key' => 'fine_rate_per_day',
                'value' => '0.50',
                'type' => 'float',
                'group' => 'fines',
                'description' => 'Fine amount per day for overdue books (in dollars)',
            ],
            [
                'key' => 'lost_book_fee',
                'value' => '50.00',
                'type' => 'float',
                'group' => 'fines',
                'description' => 'Replacement fee for lost books (in dollars)',
            ],
            [
                'key' => 'fine_due_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'fines',
                'description' => 'Number of days after fine creation before it is due',
            ],
            // Hold Settings
            [
                'key' => 'hold_expiry_days',
                'value' => '7',
                'type' => 'integer',
                'group' => 'holds',
                'description' => 'Number of days before a hold expires',
            ],
            // Notification Settings
            [
                'key' => 'due_date_reminder_days',
                'value' => '3',
                'type' => 'integer',
                'group' => 'notifications',
                'description' => 'Number of days before due date to send reminder email',
            ],
            [
                'key' => 'overdue_notification_days',
                'value' => '1',
                'type' => 'integer',
                'group' => 'notifications',
                'description' => 'Number of days overdue before sending notification email',
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

