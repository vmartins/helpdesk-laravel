<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\PublicTicketController;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});


// socialite login
Route::get('/auth/{provider}', [SocialiteController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProvideCallback']);

Route::get('/open-ticket', [PublicTicketController::class, 'create'])->name('public-ticket.create');
Route::post('/open-ticket', [PublicTicketController::class, 'store'])->name('public-ticket.store');

Route::get('/attachments/download/{id}', function (int $id) {
    $record = \App\Models\Comment::findOrFail($id);
    
    if (empty($record->attachments)) {
        abort(404);
    }

    $path = \Illuminate\Support\Facades\Storage::disk('public')->path($record->attachments);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->download($path, basename($record->attachments));
})->name('attachments.download')->middleware('auth');