<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Copy;
use App\Models\Fine;
use App\Models\Hold;
use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LibraryStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Books', Book::count())
                ->description('Books in catalog')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('success'),
            Stat::make('Total Copies', Copy::count())
                ->description(Copy::where('status', 'available')->count() . ' available')
                ->descriptionIcon('heroicon-o-document-duplicate')
                ->color('info'),
            Stat::make('Active Loans', Loan::where('status', 'active')->count())
                ->description(Loan::where('status', 'active')->where('due_date', '<', now())->count() . ' overdue')
                ->descriptionIcon('heroicon-o-arrow-right-circle')
                ->color('warning'),
            Stat::make('Pending Holds', Hold::where('status', 'pending')->count())
                ->description(Hold::where('status', 'ready')->count() . ' ready for pickup')
                ->descriptionIcon('heroicon-o-clock')
                ->color('primary'),
            Stat::make('Pending Fines', '$' . number_format(Fine::where('status', 'pending')->sum('amount'), 2))
                ->description(Fine::where('status', 'pending')->count() . ' unpaid fines')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('danger'),
            Stat::make('Total Patrons', User::where('role', 'patron')->count())
                ->description(User::where('role', 'librarian')->count() . ' librarians')
                ->descriptionIcon('heroicon-o-users')
                ->color('gray'),
        ];
    }
}
