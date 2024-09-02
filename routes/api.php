<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientUserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\RoleController;
use Laravel\Passport\Passport;

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

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken']);
Route::get('/oauth/authorize', [AuthorizationController::class, 'authorize']);
Route::delete('/oauth/tokens/{token_id}', [AuthorizedAccessTokenController::class, 'destroy']);
Route::post('/oauth/token/refresh', [TransientTokenController::class, 'refresh']);

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

Route::post('login', [AuthController::class, 'login']);
// ->middleware('role:Admin')
Route::middleware(['auth:api', 'role:Admin'])->group(function () {
        //ajout user
Route::post('register', [AuthController::class, 'register']);
// lister les coomptes users
Route::get('/usersAll', [AuthController::class, 'listUsers']);
// Lister tous les comptes utilisateurs ayant ce role et qui son active ou pas
Route::get('/users', [AuthController::class, 'listUsersByRole']);
// Lister les informations d'un client a partir de l'id
Route::get('clients/{id}', [ClientController::class, 'showClient']);
// Lister les dettes d'un client , les dettes affiches n'ont pas de details
Route::post('clients/{id}/dettes', [ClientController::class, 'showClientDebts']);
// Afficher les informations du client ainsi le compte user
Route::post('clients/{id}/user', [ClientController::class, 'showClientWithUser']);

});
//ajout un compte utilisateur à un client
Route::post('/clients/{client}/add-user', [ClientUserController::class, 'store'])->name('clients.addUser');

// ->middleware('role:Boutiquier')
Route::middleware(['auth:api', 'role:Boutiquier'])->group(function () {
        //ajout client
Route::post('/clients', [ClientController::class, 'createClient']);
    //ajout article
Route::post('/articles', [ArticleController::class, 'store']);
 Route::prefix('v1')->group(function () {
        //Lister ensemble des articles du stock
    Route::get('articles', [ArticleController::class, 'index']);
    // obtenir un article par ID
    Route::get('articles/{id}', [ArticleController::class, 'showById']);
    // obtenir un article par libellé
    Route::post('articles/libelle', [ArticleController::class, 'showByLibelle']);
    // Mettre à jour la quantité en stock d'un article par ID
    Route::patch('articles/{id}', [ArticleController::class, 'updateStockById']);
    // Mettre à jour la quantité en stock de plusieurs articles
    Route::post('articles/stock', [ArticleController::class, 'updateStockByIds']);
   // Lister les informations d'un client a partir de l'id
Route::get('clients/{id}', [ClientController::class, 'showClient']);
// Lister les dettes d'un client , les dettes affiches n'ont pas de details
Route::post('clients/{id}/dettes', [ClientController::class, 'showClientDebts']);
// Lister les dettes d'un client , les dettes affiches n'ont pas de details
Route::post('clients/{id}/dettes', [ClientController::class, 'showClientDebts']);
// Afficher les informations du client ainsi le compte user
Route::post('clients/{id}/user', [ClientController::class, 'showClientWithUser']);
 // Créer une dette
 Route::post('dettes', [DetteController::class, 'createDette']);
// lister tout les clients ou lister les clients avec compte ou sans compte et lister client avec compte active ou non
Route::get('clients', [ClientController::class, 'listClients']);
// rechercher un client par telephone
Route::post('clients/telephone', [ClientController::class, 'searchClientByPhone']);

});
});

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
