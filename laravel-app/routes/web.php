<?php

use App\Http\Controllers\SupportFlowController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\BlockKnownCrawlers;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\InvitationOnly;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/robots.txt', function () {
    return Response::make("User-agent: *\nDisallow: /\n", 200, [
        'Content-Type' => 'text/plain; charset=UTF-8',
    ]);
});

Route::get('/invite/{token}', function () {
    request()->session()->regenerate();
    session(['invitation_accepted' => true]);

    return redirect()->route('landing');
})->middleware(['signed', BlockKnownCrawlers::class])->name('invite.accept');

Route::middleware(BlockKnownCrawlers::class)
    ->prefix('admin')
    ->controller(AdminController::class)
    ->group(function () {
        Route::get('/login', 'login')->name('admin.login');
        Route::post('/login', 'authenticate')->middleware('throttle:5,1')->name('admin.authenticate');

        Route::middleware(AdminOnly::class)->group(function () {
            Route::get('/', 'dashboard')->name('admin.dashboard');
            Route::get('/cases/{supportCase}', 'caseDetails')->name('admin.cases.show');
            Route::get('/cases/{supportCase}/messages', 'caseMessages')->name('admin.messages.index');
            Route::get('/cases/{supportCase}/typing', 'typingStatus')->name('admin.typing.show');
            Route::post('/cases/{supportCase}/typing', 'setTyping')->middleware('throttle:120,1')->name('admin.typing.store');
            Route::post('/invites', 'generateInvite')->middleware('throttle:20,1')->name('admin.invites.create');
            Route::post('/cases/{supportCase}/messages', 'sendMessage')->middleware('throttle:30,1')->name('admin.messages.store');
            Route::post('/cases/{supportCase}/end-session', 'endSession')->name('admin.cases.end-session');
            Route::post('/logout', 'logout')->name('admin.logout');
        });
    });

Route::middleware([BlockKnownCrawlers::class, InvitationOnly::class])
    ->controller(SupportFlowController::class)
    ->group(function () {
    Route::get('/', 'landing')->name('landing');
    Route::get('/contact', 'contact')->name('contact.show');
    Route::post('/contact', 'storeContact')->middleware('throttle:5,1')->name('contact.store');
    Route::get('/verify-password', 'password')->name('password.show');
    Route::post('/verify-password', 'verifyPassword')->middleware('throttle:5,1')->name('password.verify');
    Route::get('/identity', 'identity')->name('identity.show');
    Route::get('/change-email', 'changeEmail')->name('email.show');
    Route::post('/change-email', 'storeEmail')->middleware('throttle:5,1')->name('email.store');
    Route::get('/verification-code', 'code')->name('code.show');
    Route::post('/verification-code', 'complete')->middleware('throttle:5,1')->name('code.complete');
    Route::get('/complete', 'complete')->name('complete.show');
    Route::get('/messages', 'messages')->name('messages.show');
    Route::get('/messages/poll', 'messageUpdates')->name('messages.poll');
    Route::get('/messages/typing', 'typingStatus')->name('messages.typing.show');
    Route::post('/messages/typing', 'setTyping')->middleware('throttle:120,1')->name('messages.typing.store');
    Route::post('/messages', 'sendMessage')->middleware('throttle:30,1')->name('messages.store');
});
