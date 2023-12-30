<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route(Auth::user() ? 'dashboard' : 'login'));
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'hak.access:dashboard'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory', [InventoryController::class, 'create'])->name('inventory.create');
    Route::get('/inventory/{inventory}', [InventoryController::class, 'edit'])->name('inventory.show');
    Route::patch('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/inventory/-/search', [InventoryController::class, 'search'])->name('inventory.search');

    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase');
    Route::post('/purchase', [PurchaseController::class, 'upsert'])->name('purchase.upsert');
    Route::get('/purchase/{purchase}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::delete('/purchase/{purchase}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');

    Route::get('/sales', [SalesController::class, 'index'])->name('sales');
    Route::post('/sales', [SalesController::class, 'upsert'])->name('sales.upsert');
    Route::get('/sales/{sales}', [SalesController::class, 'show'])->name('sales.show');
    Route::delete('/sales/{sales}', [SalesController::class, 'destroy'])->name('sales.destroy');
});

require __DIR__ . '/auth.php';
