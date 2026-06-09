<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/playground');
});

Route::get('/playground', function () {
    return view('playground');
});
