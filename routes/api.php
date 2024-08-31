<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientUserController;
use App\Http\Controllers\ArticleController;
use Laravel\Passport\Http\Controllers\{
    AccessTokenController,
    TransientTokenController,
    AuthorizationController,
    ApproveAuthorizationController,
    DenyAuthorizationController,
    AuthorizedAccessTokenController,
    ScopeController,
    PersonalAccessTokenController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
//ajout user
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
//ajout client
Route::post('/clients', [ClientController::class, 'createClient']);
//ajout un compte utilisateur à un client
Route::post('/clients/{client}/add-user', [ClientUserController::class, 'store'])->name('clients.addUser');
//ajout article
Route::post('/articles', [ArticleController::class, 'store']);
// ->middleware('role:Boutiquier')
//Lister ensemble des articles du stock
Route::get('v1/articles', [ArticleController::class, 'index']);
// Authenticated Routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/token', [AccessTokenController::class, 'issueToken'])->name('token')->middleware('throttle');
    Route::post('/token/refresh', [TransientTokenController::class, 'refresh'])->name('token.refresh');
    Route::post('/authorize', [ApproveAuthorizationController::class, 'approve'])->name('authorizations.approve');
    Route::delete('/authorize', [DenyAuthorizationController::class, 'deny'])->name('authorizations.deny');
    Route::get('/tokens', [AuthorizedAccessTokenController::class, 'forUser'])->name('tokens.index');
    Route::delete('/tokens/{token_id}', [AuthorizedAccessTokenController::class, 'destroy'])->name('tokens.destroy');
    Route::get('/scopes', [ScopeController::class, 'all'])->name('scopes.index');
    Route::get('/personal-access-tokens', [PersonalAccessTokenController::class, 'forUser'])->name('personal.tokens.index');
    Route::post('/personal-access-tokens', [PersonalAccessTokenController::class, 'store'])->name('personal.tokens.store');
    Route::delete('/personal-access-tokens/{token_id}', [PersonalAccessTokenController::class, 'destroy'])->name('personal.tokens.destroy');
});

// Authorization Routes (Web Middleware)
$guard = config('passport.guard', null);
Route::middleware(['web', $guard ? 'auth:'.$guard : 'auth'])->group(function () {
    Route::get('/authorize', [AuthorizationController::class, 'authorize'])->name('authorizations.authorize');
});
