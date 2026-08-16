<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'api' => '/api',
        'docs' => 'REST API con Laravel + Sanctum',
    ]);
});