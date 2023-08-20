<?php

use App\Http\Controllers\ExcelUploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RowsController;
use Illuminate\Support\Facades\Route;


//Главная страница
Route::get('/', function () {
    return view('welcome');
});

//"Личный кабинет" пользователя
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //Контроллер отображения формы загрузки и самой загрузки excel файла
    Route::resource('/excel', ExcelUploadController::class)->only(['create', 'store'])->name('all', 'excel');
    //Отображение импортированных из excel строк
    Route::get('/rows', [RowsController::class, 'index'])->name('rows');

    //Работа с профилем пользователя
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
