<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Esniva Auth API',
        'version' => 'v1',
        'status' => 'ok',
    ]);
});
