<?php

use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Endpoint
Route::post('/login', [CustomerAuthController::class, 'login']);

// Protected Endpoints (Requires Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [CustomerAuthController::class, 'me']);
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/booking/details', [TripController::class, 'getBookingDetails']);
    Route::get('/booking/package', [TripController::class, 'getPackageDetails']);

    Route::post('/documents/{personId}', [DocumentController::class, 'upload']);
    Route::get('/documents/{personId}', [DocumentController::class, 'show']);
});

Route::prefix('guest')->group(function () {
    Route::get('/packages', [GuestController::class, 'getPackages']);
    Route::get('/packages/{id}', [GuestController::class, 'getPackageById']);
    Route::post('/inquiry', [GuestController::class, 'submitInquiry']);
    Route::get('/gallery', [GuestController::class, 'getGalleries']);
    Route::get('/gallery/{id}', [GuestController::class, 'getGalleryDetail']);
    Route::get('/books', [GuestController::class, 'getHajjBooks']);
    Route::get('/books/{id}', [GuestController::class, 'getHajjBookDetail']);
    Route::get('/guidelines', [GuestController::class, 'getHujjajGuidelines']);
    Route::get('/guidelines/{id}', [GuestController::class, 'getHujjajGuidelineDetail']);
    Route::get('/videos', [GuestController::class, 'getHajjVideos']);
    Route::get('/videos/{id}', [GuestController::class, 'getHajjVideoDetail']);
});
