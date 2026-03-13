<?php

use App\Http\Controllers\Web\ApiPlaygroundController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ApiPlaygroundController::class, 'index']);
Route::get('/playground', [ApiPlaygroundController::class, 'index']);
