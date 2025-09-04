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

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Publication;
use App\Models\Seller;
use App\Models\User;

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


Route::get('ormControllerTest', [ORMController::class, 'testAllRelations']);


Route::get('/categories-test', function () {
    return Category::query()
        ->included()
        ->filter()
        ->sort()
        ->getOrPaginate();
});

Route::get('/complaints-test', function () {
    return Complaint::query()
        ->included()
        ->filter()
        ->sort()
        ->getOrPaginate();
});

Route::get('/publications-test', function () {
    return Publication::query()
        ->included()
        ->filter()
        ->sort()
        ->getOrPaginate();
});

Route::get('/sellers-test', function () {
    return Seller::query()
        ->included()
        ->filter()
        ->sort()
        ->getOrPaginate();
});

Route::get('/users-test', function () {
    return User::query()
        ->included()
        ->filter()
        ->sort()
        ->getOrPaginate();
});