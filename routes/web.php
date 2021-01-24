<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdutoController;
use PhpParser\Node\Expr\AssignOp\Pow;

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

Route::get('/', HomeController::class);

Route::get('users/{id}', function ($id) {
    return "o usuario é: $id";
});

Route::get('produtos/inserir', [ProdutoController::class, 'create']);
Route::get('produtos/{nome}/{valor?}', [ProdutoController::class, 'show']);
Route::get('produtos', [ProdutoController::class, 'index']);