<?php

namespace App\Filament\Widgets;

use App\Models\Fine;
use Filament\Widgets\ChartWidget;

class FinesChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;
    protected ?string $heading = 'Fines by Status';

    protected function getData(): array
    {
        $fines = Fine::selectRaw('status, SUM(amount) as total')
            ->groupBy('status')
            ->get();

        $labels = $fines->pluck('status')->map(fn ($status) => ucfirst($status))->toArray();
        $data = $fines->pluck('total')->toArray();

        $colors = [
            'rgba(245, 158, 11, 0.8)',   // Yellow for pending
            'rgba(16, 185, 129, 0.8)',   // Green for paid
            'rgba(59, 130, 246, 0.8)',   // Blue for waived
            'rgba(156, 163, 175, 0.8)',  // Gray for cancelled
        ];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
