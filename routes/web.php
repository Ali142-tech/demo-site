<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// ---- Public site ----------------------------------------------------------
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/services/{product}', [PageController::class, 'product'])->name('product');
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->middleware('throttle:6,1')->name('contact.send');

// ---- Auth -----------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---- Customer area (schools / businesses / individuals) -------------------
Route::middleware('auth')->prefix('dashboard')->name('customer.')->group(function () {
    Route::get('/', [Customer\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/quotes', [Customer\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/new', [Customer\QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [Customer\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [Customer\QuoteController::class, 'show'])->name('quotes.show');
    Route::get('/quotes/{quote}/print', [Customer\QuoteController::class, 'print'])->name('quotes.print');
    Route::post('/quotes/{quote}/respond', [Customer\QuoteController::class, 'respond'])->name('quotes.respond');
    Route::post('/quotes/{quote}/cancel', [Customer\QuoteController::class, 'cancel'])->name('quotes.cancel');
    Route::post('/quotes/{quote}/messages', [Customer\QuoteController::class, 'message'])->name('quotes.message');
    Route::post('/quotes/{quote}/files', [Customer\QuoteController::class, 'addFiles'])->name('quotes.files.add');
    Route::get('/quotes/{quote}/files/{file}', [Customer\QuoteController::class, 'download'])->name('quotes.files.download');
    Route::post('/quotes/{quote}/reorder', [Customer\QuoteController::class, 'reorder'])->name('quotes.reorder');

    Route::get('/profile', [Customer\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [Customer\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [Customer\ProfileController::class, 'password'])->name('profile.password');
});

// ---- Admin area (the shop owner) ------------------------------------------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/quotes', [Admin\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [Admin\QuoteController::class, 'show'])->name('quotes.show');
    Route::put('/quotes/{quote}/quotation', [Admin\QuoteController::class, 'saveQuotation'])->name('quotes.quotation');
    Route::put('/quotes/{quote}/status', [Admin\QuoteController::class, 'updateStatus'])->name('quotes.status');
    Route::post('/quotes/{quote}/messages', [Admin\QuoteController::class, 'message'])->name('quotes.message');
    Route::get('/quotes/{quote}/files/{file}', [Admin\QuoteController::class, 'download'])->name('quotes.files.download');
    Route::get('/quotes/{quote}/print', [Admin\QuoteController::class, 'print'])->name('quotes.print');

    Route::get('/customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [Admin\CustomerController::class, 'show'])->name('customers.show');

    Route::resource('products', Admin\ProductController::class)->except(['show', 'destroy']);

    Route::get('/messages', [Admin\MessageController::class, 'index'])->name('messages.index');
    Route::put('/messages/{message}/read', [Admin\MessageController::class, 'markRead'])->name('messages.read');
    Route::delete('/messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');
});
