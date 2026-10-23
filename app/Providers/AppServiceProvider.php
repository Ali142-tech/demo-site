<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\QuoteFile;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Route::model('quote', QuoteRequest::class);
        Route::model('file', QuoteFile::class);
        Route::model('customer', User::class);
        Route::model('message', ContactMessage::class);
    }
}
