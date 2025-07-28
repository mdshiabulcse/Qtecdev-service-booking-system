<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/clear', function() {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    return "Cleared!";

});
Route::get('/route-cache', function() {
    Artisan::call('route:clear');
    return 'Routes cache has been cleared';
});
Route::get('/migrate', function() {
    Artisan::call('migrate');
    return 'Migrated Done';
});
Route::get('/db-seed', function () {
    try {
        Artisan::call('db:seed');
        return 'DB Seed Done!';
    } catch (Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
