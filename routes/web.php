<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeMenuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuDetailController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TenantTransactionController;
use App\Http\Controllers\TenantHistoryController;
use App\Http\Controllers\TenantHomeController;
use App\Http\Controllers\ForgotpassController;


Route::middleware('web')->get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/signout', [LoginController::class, 'signout']);

Route::get('/signup', [SignUpController::class, 'index'])->middleware('guest');;
Route::post('/signup', [SignUpController::class, 'store']);

Route::get('/forgotpass', [ForgotpassController::class, 'showForgotPass'])->middleware('guest');
Route::post('/forgotpass', [ForgotpassController::class, 'getEmail']);

Route::get('/{id}/homepage', [HomeMenuController::class, 'home'])->name('home.index')->middleware('auth');


Route::get('/verification', [ForgotpassController::class, 'showVerif'])->middleware('guest');
Route::post('/verification', [ForgotpassController::class, 'resendEmail']);

Route::get('/inputnp', [ForgotpassController::class, 'showChangePassword'])->name('change.password')->middleware('guest');
Route::post('/inputnp', [ForgotpassController::class, 'changePassword']);

// Route::get('/topup', [TopUpController::class, 'show']);
Route::get('/{id}/topup/{emoney}', [TopUpController::class, 'activeEmoney'])->middleware('auth');
Route::post('/{id}/topup/process', [TopUpController::class, 'midtransProcess'])->name('topup.process');
Route::post('/{id}/topup/finish', [TopUpController::class, 'finishMidtrans']);

Route::get('/{id}/profile', [ProfileController::class, 'show'])->name('profile')->middleware('auth');
Route::post('{user_id}/profile/edit', [ProfileController::class, 'editProfile'])->name('edit.profile');
Route::post('{id}/profile/edit_profile_image', [ProfileController::class, 'editProfileImage']);

Route::get('/{id}/order', [OrderController::class, 'show'])->middleware('auth');
Route::get('/{id}/order/confirm_pickup', [OrderController::class, 'confirmPickup'])->middleware('auth');
Route::get('/{id}/cart', [CartController::class, 'show'])->middleware('auth');

Route::get('/{id}/cart/order_now', [CartController::class, 'store'])->middleware('auth');
Route::get('/{id}/edit_notes', [CartController::class, 'saveEdit']);

Route::get('/{id}/menu/{tenant_name}', [MenuController::class, 'show'])->middleware('auth');
Route::get('/{id}/menu_detail/{tenant_name}/{menu_id}', [MenuDetailController::class, 'show'])->middleware('auth');
Route::get('/{id}/menu_detail/add_to_cart', [MenuDetailController::class, 'store'])->middleware('auth');

Route::get('/{id}/notification', [NotificationController::class, 'show'])->middleware('auth');
Route::get('/{id}/notification/change_status', [NotificationController::class, 'changeStatus'])->middleware('auth');

Route::get('/{id}/tenant/transaction', [TenantTransactionController::class, 'show']);
Route::get('/{id}/tenant/history', [TenantHistoryController::class, 'show']);
Route::get('/{id}/tenant/homepage', [TenantHomeController::class, 'show'])->middleware('auth');
Route::get('/{id}/tenant/finish_order/{trans_id}', [TenantTransactionController::class, 'finishOrder']);
