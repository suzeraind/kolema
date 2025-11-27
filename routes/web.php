<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])
    ->controller(App\Http\Controllers\WebSocketDemoController::class)
    ->group(function () {
        Route::get('websocket-demo', 'index')->name('websocket.demo');
        Route::post('api/websocket/send', 'sendMessage')->name('api.websocket.send');
        Route::delete('api/websocket/message/{message}', 'deleteMessage')->name('api.websocket.delete');
    });

Route::get('api/queue-test', [App\Http\Controllers\Api\QueueTestController::class, 'dispatch'])
    ->name('api.queue-test');

require __DIR__.'/settings.php';
