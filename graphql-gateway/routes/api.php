<?php

use App\Http\Controllers\Api\GraphQLController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [GraphQLController::class, 'health']);
Route::post('/graphql', [GraphQLController::class, 'execute']);
Route::get('/graphql/schema', [GraphQLController::class, 'schema']);
