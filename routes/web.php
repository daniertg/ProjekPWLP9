<?php

use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ArticleController;
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

Route::get('mahasiswas/nilai/{nim}', [MahasiswaController::class, 'khs']);

Route::resource('mahasiswas', MahasiswaController::class);
Route::get('mahasiswas/cetak/{nim}', [MahasiswaController::class, 'cetak'])->name('mahasiswas.cetak');
Route::resource('articles', ArticleController::class);
Route::get('articles/cetak-pdf', [ArticleController::class, 'cetak_pdf']);