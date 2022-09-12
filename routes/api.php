<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UsersController;

Route::middleware('auth:sanctum')->group(function (){

    Route::get('/balance', [UsersController::class, 'checkBalance'])->name('users.checkBalance');
    Route::post('/deposit', [UsersController::class, 'depositMoney'])->name('users.depositMoney');
    Route::post('/transfer', [UsersController::class, 'transferMoney'])->name('users.transferMoney');
    Route::post('/transferOut', [UsersController::class, 'transferMoneyOut'])->name('users.transferMoneyOut');

});


Route::get('/currencies', [UsersController::class, 'getCurrencies'])->name('users.getCurrencies');

