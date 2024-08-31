<?php
use illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
    return view('welcome');
});



// Route::prefix('/blog')->group(function () {

// Route::get('/', function (Request $request) {
//     return [
//         "name" => $request->input('name','soda'),
//         "article" => "article 1"

//     ];
// });

// Route::get('/blog/{slug}-{id}', function (string $slug,string $id,Request $request) {
//     return [
//         "slug " => $slug,
//         " id " => $id,
//         "name" => $request->input('name'),
//     ];
// })->where(['id' => '[0-9]+', 
//     'slug' => '[a-z\-]+'
// ]);
// });