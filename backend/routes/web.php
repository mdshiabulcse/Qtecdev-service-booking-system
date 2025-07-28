<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/route-cache', function() {
    Artisan::call('route:clear');
    return 'Routes cache has been cleared';
});
Route::get('/migrate', function() {
    Artisan::call('migrate');
    return 'Migrated Done';
});
Route::get('/storage-link', function() {
    Artisan::call('storage:link');
    return 'Storage Link Done';
});
Route::get('/db-seed', function() {
    Artisan::call('db:seed');
    return 'DB Seed Done';
});
