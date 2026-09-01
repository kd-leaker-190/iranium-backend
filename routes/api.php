<?php

use App\Http\Controllers\NotifyController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('teams')->name('teams.')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::controller(App\Http\Controllers\Api\ArGameController::class)->prefix('ar-games')->name('ar-games.')->group(function () {
    Route::post('/{arGame}', 'exchange');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(App\Http\Controllers\Api\ActionTeamController::class)->group(function () {
        Route::post('/actions/{action}/upload', 'upload');
    });


    // leaderboard
    Route::controller(App\Http\Controllers\Api\LeaderboardController::class)->prefix('leaderboard')->name('leaderboard.')->group(function () {
        Route::get('/', 'index');
    });

    Route::controller(App\Http\Controllers\Api\ActionController::class)->group(function () {
        Route::get('/actions', 'index');
        Route::post('/actions/{action}/start', 'start');
        Route::post('/actions/{action}/end', 'end');
        Route::get('/actions/{action}', 'show');
    });

    Route::controller(App\Http\Controllers\Api\CoinController::class)->prefix('coins')->name('coins.')->group(function () {
        Route::get('/', 'index');
        Route::post('/{coin}', 'exchange');
    });
    Route::controller(App\Http\Controllers\Api\ScoreCardController::class)->prefix('score-cards')->name('score-cards.')->group(function () {
        Route::get('/', 'index');
        Route::post('/{scoreCard}', 'exchange');
    });
    Route::controller(App\Http\Controllers\Api\GameController::class)->prefix('games')->name('games.')->group(function () {
        Route::get('/', 'index');
        Route::get('/score', 'score');
        Route::get('/{game}', 'show');
        Route::post('/{game}', 'exchange');
    });

    Route::controller(TeamController::class)->group(function () {
        Route::put('/teams', 'update');
    });

    Route::apiResource('team-users', \App\Http\Controllers\Api\TeamUserController::class)->except(['show']);

    Route::controller(NotifyController::class)->prefix('notify')->name('notify.')->group(function () {
        Route::get('/', 'teamNotifications');
        Route::get('unread', 'unreadCount');
        Route::post('mark-all-read', 'markAllRead');
        Route::post('{notify}/read', 'markOneRead');
    });

    Route::get('tickets', [\App\Http\Controllers\Api\TicketController::class, 'index']);
    Route::get('tickets/categories', [\App\Http\Controllers\Api\TicketController::class, 'ticketCategories']);
    Route::get('tickets/{ticket}', [\App\Http\Controllers\Api\TicketController::class, 'show']);
    Route::post('tickets', [\App\Http\Controllers\Api\TicketController::class, 'store']);
    Route::post('tickets/{ticket}/send-message', [\App\Http\Controllers\Api\TicketController::class, 'sendMessage']);
});
