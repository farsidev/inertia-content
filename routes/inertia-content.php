<?php

use Farsi\InertiaContent\Facades\Content;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Inertia Content Routes
|--------------------------------------------------------------------------
|
| These routes are automatically registered when routes.enabled is true
| in the config file. You can customize the prefix and middleware.
|
*/

Route::middleware(config('inertia-content.routes.middleware', ['web']))
    ->prefix(config('inertia-content.routes.prefix', ''))
    ->group(function () {
        // Catch-all route for content pages
        Route::get('{path}', function ($path) {
            return Content::pageOrFail($path);
        })->where('path', '.*');
    });
