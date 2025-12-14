<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', \App\Livewire\Dashboard\Index::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Library routes
    Route::prefix('library')->name('library.')->group(function () {
        // Books
        Route::get('/books', \App\Livewire\Books\Index::class)->name('books.index');
        Route::get('/books/{book}', \App\Livewire\Books\Show::class)->name('books.show');
        
        // Circulation (librarians only)
        Route::middleware('can:librarian')->group(function () {
            Route::get('/circulation/checkout', \App\Livewire\Circulation\Checkout::class)->name('circulation.checkout');
            Route::get('/circulation/return', \App\Livewire\Circulation\ReturnBook::class)->name('circulation.return');
        });

        // Patron self-service pages
        Route::get('/my-loans', \App\Livewire\MyLoans\Index::class)->name('my-loans');
        Route::get('/my-holds', \App\Livewire\MyHolds\Index::class)->name('my-holds');
        Route::get('/my-fines', \App\Livewire\MyFines\Index::class)->name('my-fines');
    });
});

// API routes for library operations
Route::middleware(['auth'])->prefix('api/library')->name('api.library.')->group(function () {
    Route::apiResource('books', \App\Http\Controllers\BookController::class);
    Route::post('circulation/checkout', [\App\Http\Controllers\CirculationController::class, 'checkout'])->name('circulation.checkout');
    Route::post('circulation/{loan}/return', [\App\Http\Controllers\CirculationController::class, 'return'])->name('circulation.return');
    Route::post('circulation/{loan}/renew', [\App\Http\Controllers\CirculationController::class, 'renew'])->name('circulation.renew');
    Route::post('circulation/{loan}/lost', [\App\Http\Controllers\CirculationController::class, 'markAsLost'])->name('circulation.lost');
    Route::apiResource('holds', \App\Http\Controllers\HoldController::class);
    Route::post('holds/{hold}/cancel', [\App\Http\Controllers\HoldController::class, 'destroy'])->name('holds.cancel');
    Route::apiResource('fines', \App\Http\Controllers\FineController::class);
    Route::post('fines/{fine}/pay', [\App\Http\Controllers\FineController::class, 'markPaid'])->name('fines.pay');
    Route::post('fines/{fine}/waive', [\App\Http\Controllers\FineController::class, 'waive'])->name('fines.waive');
});
