<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardPostController;

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


Route::get('/home', [PostController::class, 'home'])->name('home');
Route::get('/maps', [PostController::class, 'maps'])->name('maps');


Route::get('/history', [PostController::class, 'history'])->name('history');
Route::post('/getkota', [PostController::class, 'getkota'])->name('getkota');

// halaman single post
Route::get('/posts/{post:slug}',[PostController::class, 'show']);




Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);


Route::get('/register', [RegisterController::class, 'index'])->middleware('guest');
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/dashboard', function()
{
    return view('dashboard.index');
})->middleware('auth');


// Import CSV Page
Route::get('/dashboard/posts/importcsv', [DashboardPostController::class, 'importCSV'])->name('dashboard.posts.importcsv');



Route::resource('/dashboard/posts', DashboardPostController::class)->middleware('auth');

Route::post('/getkabupaten', [DashboardPostController::class, 'getkabupaten'])->name('getkabupaten')->middleware('auth');

Route::get('/dashboard/posts/province/{provinceId}', [DashboardPostController::class, 'showRegenciesByProvince'])
    ->name('dashboard.posts.showRegenciesByProvince')
    ->middleware('auth');

Route::get('/dashboard/posts/edit/{province_id}/{regency_id}/{post_id}', [DashboardPostController::class, 'edit'])
    ->name('dashboard.posts.edit')
    ->middleware('auth');


Route::get('/dashboard/posts/show/{province_id}/{regency_id}', [DashboardPostController::class, 'show'])
    ->name('dashboard.posts.show')
    ->middleware('auth');

Route::delete('/dashboard/posts/{post}', [DashboardPostController::class, 'destroy'])
    ->name('dashboard.posts.destroy');


    Route::get('/dashboard/posts/importcsv', [DashboardPostController::class, 'importCSV'])->name('dashboard.posts.importcsv');
    Route::post('/dashboard/posts/handle-csv-import', [DashboardPostController::class, 'handleCSVImport'])->name('dashboard.posts.handleCSVImport');


    Route::get('/dashboard/posts', [DashboardPostController::class, 'index'])->name('dashboard.posts.index');