<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\MarketingPageController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrivacySettingsController;

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('logout', [AuthController::class, 'logout']);
    Route::resource('marketing-pages', MarketingPageController::class);
    Route::post('products', [ProductController::class, 'create']);
    Route::get('marketing-pages/{marketingPageId}/products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::post('marketing-pages/members', [MemberController::class, 'create']);
    Route::delete('marketing-pages/members/{id}', [MemberController::class, 'destroy']);
    Route::post('friendships/send-request', [FriendshipController::class, 'sendFriendRequest']);
    Route::post('friendships/accept-request', [FriendshipController::class, 'acceptFriendRequest']);
    Route::post('friendships/reject-request', [FriendshipController::class, 'rejectFriendRequest']);
    Route::post('purchases', [PurchaseController::class, 'purchaseProduct']);
    Route::get('user/marketing-pages', [UserController::class, 'getMarketingPages']);
    Route::get('user/products', [UserController::class, 'getProducts']);
    Route::get('user/earnings', [UserController::class, 'getEarnings']);
    Route::post('user/set-language', [UserController::class, 'setLanguage']);
    Route::get('user/financial-details', [UserController::class, 'getFinancialDetails']);
    Route::get('marketing-pages/{marketingPageId}/privacy-settings', [PrivacySettingsController::class, 'getPrivacySettings']);
    Route::post('marketing-pages/{marketingPageId}/block-user', [PrivacySettingsController::class, 'blockUser']);
    Route::post('marketing-pages/{marketingPageId}/unblock-user', [PrivacySettingsController::class, 'unblockUser']);
});
