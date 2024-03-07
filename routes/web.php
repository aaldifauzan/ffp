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

Route::get('/', function () {
    return view('home', [
        "title" => "Home",
        "active" => 'home',
    ]);
});

Route::get('/maps', function () {
    return view('maps',[
        "title" => "About",
        "name" => "Aldi Fauzan",
        "email" => "aldifauzaan@student.telkomuniversity.ac.id",
        "image" => "foto.jpg",
        "active" => 'maps'
    ]);
});


Route::get('/posts', [PostController::class, 'index']);
// halaman single post
Route::get('/posts/{post:slug}',[PostController::class, 'show']);

Route::get('/login', function () {
    return view('login',[
        "title" => "About",
        "name" => "Aldi Fauzan",
        "email" => "aldifauzaan@student.telkomuniversity.ac.id",
        "image" => "foto.jpg"
    ]);
});

Route::get('/categories', function() {
    return view('categories', [
        'title' => 'Post Categories',
        "active" => 'categories',
        'categories' => Category::all()
    ]);
});


Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);


Route::get('/register', [RegisterController::class, 'index'])->middleware('guest');
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/dashboard', function()
{
    return view('dashboard.index');
})->middleware('auth');



Route::get('/dashboard/posts/checkSlug', [DashboardPostController::class], 'checkSlug')->middleware('auth');
Route::resource('/dashboard/posts', DashboardPostController::class)->middleware('auth');

Route::post('/getkabupaten', [DashboardPostController::class, 'getkabupaten'])->name('getkabupaten')->middleware('auth');
Route::post('/getkecamatan', [DashboardPostController::class, 'getkecamatan'])->name('getkecamatan')->middleware('auth');
Route::post('/getkelurahan', [DashboardPostController::class, 'getkelurahan'])->name('getkelurahan')->middleware('auth');