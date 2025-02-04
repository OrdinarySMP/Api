<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationQuestionAnswerController;
use App\Http\Controllers\ApplicationQuestionController;
use App\Http\Controllers\ApplicationResponseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BotTokenController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReactionRoleController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\ServerContentController;
use App\Http\Controllers\ServerContentMessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketConfigController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        'faq' => FaqController::class,
        'rule' => RuleController::class,
        'reaction-role' => ReactionRoleController::class,
        'application' => ApplicationController::class,
        'application-question' => ApplicationQuestionController::class,
        'application-question-answer' => ApplicationQuestionAnswerController::class,
        'application-response' => ApplicationResponseController::class,
        'server-content' => ServerContentController::class,
    ], ['except' => ['show']]);

    Route::apiResource('user', UserController::class)->only(['update']);
    Route::apiResource('server-content-message', ServerContentMessageController::class)->only(['index', 'store']);

    Route::get('permissions', [PermissionController::class, 'index'])->name('permission.index');
    Route::get('permissions/template', [PermissionController::class, 'template'])->name('permission.template');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permission.store');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('botToken', BotTokenController::class)->name('bot.token');

    Route::get('discord/text-channels', [DiscordController::class, 'textChannels']);
    Route::get('discord/roles', [DiscordController::class, 'roles']);

    Route::post('server-content/resend', [ServerContentController::class, 'resend'])->name('server-content.resend');

    Route::prefix('ticket')->group(function () {
        Route::apiResource('config', TicketConfigController::class)->only(['index', 'store']);
    });
});

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('discord/callback', [DiscordController::class, 'callback']);
Route::get('me', MeController::class);
