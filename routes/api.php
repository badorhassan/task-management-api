<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;


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


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});


Route::middleware('auth:api')->group(function () {
    // Task routes available to all authenticated users
    Route::get('tasks', [TaskController::class, 'index']);
    Route::get('tasks/{id}', [TaskController::class, 'show']);
    Route::put('tasks/{id}', [TaskController::class, 'update']);
    
    // Manager only routes
    Route::middleware('role:manager')->group(function () {
        Route::post('tasks', [TaskController::class, 'store']);
        Route::delete('tasks/{id}', [TaskController::class, 'destroy']);
        Route::post('tasks/{id}/dependencies', [TaskController::class, 'addDependency']);
        Route::delete('tasks/{id}/dependencies', [TaskController::class, 'removeDependency']);
    });
});

