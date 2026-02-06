<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/marketer.php';
require __DIR__.'/warehouse.php';
require __DIR__.'/admin.php';
