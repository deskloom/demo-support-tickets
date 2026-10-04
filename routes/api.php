<?php

use App\Http\Controllers\Api\TicketApiController;
use Illuminate\Support\Facades\Route;

Route::get('/tickets', [TicketApiController::class, 'index'])->name('api.tickets.index');
Route::post('/tickets', [TicketApiController::class, 'store'])->name('api.tickets.store');
Route::patch('/tickets/{ticket}', [TicketApiController::class, 'update'])->name('api.tickets.update');
