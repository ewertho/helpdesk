
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PostController,
    StorageFileController
};

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
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('posts.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Posts Routes
    Route::any('/posts/search', [PostController::class, 'search'])->name('posts.search');
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/edit/{id}', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');    
});

Route::get('image/{filename}', [StorageFileController::class, 'getPubliclyStorgeFile'])->name('image.displayImage');

require __DIR__.'/auth.php';
