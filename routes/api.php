<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ORMController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ReasonComplaintController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupportController;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('comments', CommentController::class);
Route::apiResource('phones', PhoneController::class);
Route::apiResource('publications', PublicationController::class);
Route::apiResource('reasonComplaints', ReasonComplaintController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('sellers', SellerController::class);

Route::apiResource('images', ImageController::class);

//rutas del usuario y favoritos
Route::apiResource('users', UserController::class);
Route::get('users/{id}/favorites', [UserController::class, 'favoritos']);
Route::patch('users/{userId}/favorites/toggle', [UserController::class, 'toggleFavorito']);


Route::get('orm/test-all', [ORMController::class, 'testAllRelations']);
Route::get('orm/test-polymorphic', [ORMController::class, 'testPolymorphicRelations']);


Route::post('/support', [SupportController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/me', [AuthController::class, 'me']);

Route::post('publications/{publication}/report', [ReportController::class, 'reportPublication']);
Route::post('users/{user}/report', [ReportController::class, 'reportUser']);