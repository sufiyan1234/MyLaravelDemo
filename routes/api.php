<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketAssignmentController;
use App\Http\Controllers\Api\AdminDashboardController;


/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::post(
    '/register',
    [AuthController::class, 'register']
);

Route::post(
    '/login',
    [AuthController::class, 'login']
);


/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/me',
        [AuthController::class, 'me']
    );

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    );


    /*
    |--------------------------------------------------------------------------
    | Tickets
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/tickets',
        [TicketController::class, 'index']
    );

    Route::post(
        '/tickets',
        [TicketController::class, 'store']
    );

    Route::get(
        '/tickets/{ticket}',
        [TicketController::class, 'show']
    );

    Route::put(
        '/tickets/{ticket}',
        [TicketController::class, 'update']
    );


    /*
    |--------------------------------------------------------------------------
    | Admin only
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        Route::get(
            '/agents',
            [TicketAssignmentController::class, 'agents']
        );

        Route::put(
            '/tickets/{ticket}/assign',
            [TicketAssignmentController::class, 'assign']
        );

        Route::get(
            '/admin/dashboard',
            [AdminDashboardController::class, 'index']
        );
    });
});