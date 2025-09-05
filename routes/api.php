<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ORMController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ReasonComplaintController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

route::apiResource('categories',CategoryController::class);
route::apiResource('comments',CommentController::class);
route::apiResource('complaints',ComplaintController::class);
route::apiResource('phones',PhoneController::class);
route::apiResource('publications',PublicationController::class);
route::apiResource('reasonComplaints',ReasonComplaintController::class);
route::apiResource('roles',RoleController::class);
route::apiResource('sellers',SellerController::class);
route::apiResource('users',UserController::class);


route::get('users/{id}/favorites',[UserController::class,'favoritos']); 







Route::get('/all-relations', [ORMController::class, 'testAllRelations']);
Route::get('/polymorphic-relations', [ORMController::class, 'testPolymorphicRelations']);