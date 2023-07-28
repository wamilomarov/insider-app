<?php

use App\Http\Controllers\SeasonController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [SeasonController::class, 'index'])->name('season.index');
Route::post('new-season', [SeasonController::class, 'newSeason'])->name('season.new');
Route::post('next-week/{season}', [SeasonController::class, 'nextWeek'])->name('season.next-week');
