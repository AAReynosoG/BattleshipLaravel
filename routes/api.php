<?php

use App\Events\NotifyEvent;
use App\Events\shoot;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\TestEvent;
use App\Http\Controllers\GameController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/sendevent', function(){
    event(new TestEvent(['msg' => 'Hello World']));
    return response()->json(['success' => true]);
});

Route::prefix('user')->group(function(){
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
});


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/authenticatetoken', function () {
        return response()->json([
            'status' => true
        ]);
    });

    route::prefix('user')->group(function(){
        Route::post('/logout', [UserController::class, 'logout']);
    });

    Route::prefix('game')->group(function(){
        Route::post('/queue', [GameController::class, 'queueGame']);
        Route::put('/join/random', [GameController::class, 'joinRandomGame']);
        Route::put('/end', [GameController::class, 'endGame']);
        Route::post('/dequeue', [GameController::class, 'dequeueGame']);
        Route::post('/cancel/random', [GameController::class, 'cancelRandomQueue']);
        Route::post('/send/board', [GameController::class, 'sendBoard']);
        Route::get('/history', [GameController::class, 'myGameHistory']);
        Route::post('/notify', [GameController::class, 'sendNotify']);
        Route::post('/attack', [GameController::class, 'attack']);
        Route::post('/attack/success', [GameController::class, 'attackSuccess']);
        Route::post('/attack/failed', [GameController::class, 'attackFailed']);
    });
});

Route::post('/shoot', function(){
    event(new shoot("se ha disparado"));
    return response()->json(['success' => true]);
});


Route::post('/notify', function(){
    event(new NotifyEvent("notificacion"));
    return response()->json(['success' => true]);
});
