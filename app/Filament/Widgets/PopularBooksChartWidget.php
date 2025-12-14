<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;

class PopularBooksChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;
    protected ?string $heading = 'Most Checked Out Books (Top 10)';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $popularBooks = Book::withCount('loans')
            ->having('loans_count', '>', 0)
            ->orderByDesc('loans_count')
            ->limit(10)
            ->get();

        $labels = $popularBooks->pluck('title')->map(fn ($title) => \Str::limit($title, 30))->toArray();
        $data = $popularBooks->pluck('loans_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Checkouts',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'indexAxis' => 'y', // Horizontal bar chart
        ];
    }
}
