<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('home');
});

Route::get('/pesan', [PesanController::class, 'create'])->name('pesan.create');
Route::post('/pesan', [PesanController::class, 'store'])->name('pesan.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::post('/order/terima', [OrderController::class, 'terima'])->name('order.terima');
    Route::post('/transactions/status-update', [TransactionController::class, 'updateStatus'])->name('transactions.status.update');

    Route::prefix('kurir')->name('kurir.')->group(function () {
        Route::get('/pengantaran', [KurirController::class, 'index'])->name('pengantaran.index');
        Route::get('/pengantaran/create', [KurirController::class, 'create'])->name('pengantaran.create');
        Route::post('/pengantaran', [KurirController::class, 'store'])->name('pengantaran.store');
        Route::get('/pengantaran/{id}/edit', [KurirController::class, 'edit'])->name('pengantaran.edit');
        Route::put('/pengantaran/{id}', [KurirController::class, 'update'])->name('pengantaran.update');
        Route::delete('/pengantaran/{id}', [KurirController::class, 'destroy'])->name('pengantaran.destroy');
    });

});

Route::resource('customers', CustomerController::class);

Route::resource('service-types', ServiceTypeController::class);

Route::resource('transactions', TransactionController::class)
    ->parameters(['transactions' => 'no_transaction']);

Route::resource('payments', PaymentController::class)
    ->parameters(['payments' => 'no_payment']);


require __DIR__.'/auth.php';
