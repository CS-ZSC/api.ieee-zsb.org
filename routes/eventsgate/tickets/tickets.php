<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->group(function () {
    // get all users tickets
    Route::get('tickets/my-tickets', [TicketController::class, 'getUserTickets']);

    // used by organizers to verify information about this ticket holder
    Route::post('tickets/verify', [TicketController::class, 'verifyUserTicket']);

    // used by organizers to check in users after scaning their qr code and verifying info
    Route::post('tickets/check-in', [TicketController::class, 'checkinUser']);
});
