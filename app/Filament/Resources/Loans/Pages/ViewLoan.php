<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use App\Services\LibraryService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('return')
                ->label('Return Book')
                ->icon('heroicon-o-arrow-left-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'active')
                ->action(function () {
                    try {
                        app(LibraryService::class)->return($this->record);
                        Notification::make()
                            ->title('Book returned successfully')
                            ->success()
                            ->send();
                        $this->redirect(static::getResource()::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error returning book')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('renew')
                ->label('Renew Loan')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'active')
                ->action(function () {
                    try {
                        app(LibraryService::class)->renew($this->record);
                        Notification::make()
                            ->title('Loan renewed successfully')
                            ->success()
                            ->send();
                        $this->refreshFormData(['due_date']);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error renewing loan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('markLost')
                ->label('Mark as Lost')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'active')
                ->action(function () {
                    try {
                        app(LibraryService::class)->markAsLost($this->record);
                        Notification::make()
                            ->title('Loan marked as lost')
                            ->success()
                            ->send();
                        $this->redirect(static::getResource()::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error marking loan as lost')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
