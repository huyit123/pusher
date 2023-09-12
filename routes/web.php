<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
use App\Http\Controllers\PusherController;

Route::get('/pusher', [PusherController::class, 'index']);
Route::post('/send-notification', [PusherController::class, 'sendNotification']);
