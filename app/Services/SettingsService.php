<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    /**
     * Get a configuration value
     * First checks database settings, then falls back to config file
     */
    public function get(string $key, $default = null)
    {
        // Check if settings table exists
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return config("library.{$key}", $default);
            }
        } catch (\Exception $e) {
            return config("library.{$key}", $default);
        }

        // Try to get from database
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            return Setting::castValue($setting->value, $setting->type);
        }

        // Fall back to config file
        return config("library.{$key}", $default);
    }

    /**
     * Set a configuration value in database
     */
    public function set(string $key, $value, string $type = 'string'): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        Setting::setValue($key, $value, $type);
    }

    /**
     * Get all settings grouped by category
     */
    public function getAllGrouped(): array
    {
        if (!\Schema::hasTable('settings')) {
            return [];
        }

        return Setting::orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->toArray();
    }

    /**
     * Get loan period in days
     */
    public function getLoanPeriodDays(): int
    {
        return (int) $this->get('loan_period_days', 14);
    }

    /**
     * Get maximum loans per patron
     */
    public function getMaxLoansPerPatron(): int
    {
        return (int) $this->get('max_loans_per_patron', 10);
    }

    /**
     * Get renewal period in days
     */
    public function getRenewalPeriodDays(): int
    {
        return (int) $this->get('renewal_period_days', 14);
    }

    /**
     * Get fine rate per day
     */
    public function getFineRatePerDay(): float
    {
        return (float) $this->get('fine_rate_per_day', 0.50);
    }

    /**
     * Get lost book fee
     */
    public function getLostBookFee(): float
    {
        return (float) $this->get('lost_book_fee', 50.00);
    }

    /**
     * Get fine due days
     */
    public function getFineDueDays(): int
    {
        return (int) $this->get('fine_due_days', 30);
    }

    /**
     * Get hold expiry days
     */
    public function getHoldExpiryDays(): int
    {
        return (int) $this->get('hold_expiry_days', 7);
    }

    /**
     * Get due date reminder days
     */
    public function getDueDateReminderDays(): int
    {
        return (int) $this->get('due_date_reminder_days', 3);
    }

    /**
     * Get overdue notification days
     */
    public function getOverdueNotificationDays(): int
    {
        return (int) $this->get('overdue_notification_days', 1);
    }
}

