<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Bot\WebhookController;

Route::prefix('bot')->group(function () {
    Route::get('/barbeiros', [WebhookController::class, 'barbeiros']);
    Route::get('/servicos', [WebhookController::class, 'servicos']);
    Route::get('/horarios', [WebhookController::class, 'horarios']);
    Route::get('/dias-disponiveis', [WebhookController::class, 'diasDisponiveis']);
    Route::post('/agendar', [WebhookController::class, 'agendar']);
    Route::get('/lembretes', [WebhookController::class, 'lembretes']);
});
