<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class LoansChartWidget extends ChartWidget
{
    protected static ?int $sort = 5;
    protected ?string $heading = 'Loans Over Time (Last 30 Days)';

    protected int | string | array $columnSpan = 'full';
    protected function getData(): array
    {
        $days = 30;
        $labels = [];
        $checkouts = [];
        $returns = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            
            $checkouts[] = Loan::whereDate('checkout_date', $date)->count();
            $returns[] = Loan::whereDate('returned_date', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Checkouts',
                    'data' => $checkouts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Returns',
                    'data' => $returns,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
