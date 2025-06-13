<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::post('/contact-support', [SupportController::class, 'send']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::get('/admin/plans', [AdminController::class, 'getPlans']);
    Route::post('/admin/plans', [AdminController::class, 'updatePlans']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    Route::post('/admin/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);

    //Patient Dashboard
    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::post('/ai-notes', [NoteController::class, 'AIstore']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
    Route::post('/notes/{id}/email', [NoteController::class, 'sendEmail']);
    Route::get('/notes/{id}/download', [NoteController::class, 'download']);

    Route::post('/profile/image', [ProfileController::class, 'updateProfileImage']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/password', [ProfileController::class, 'changePassword']);

    Route::get('/subscriptions', [ProfileController::class, 'getSubscription']);
    Route::post('/subscriptions', [ProfileController::class, 'updateSubscription']);

    Route::get('/cards', [ProfileController::class, 'getCard']);
    Route::post('/cards', [ProfileController::class, 'storeCard']);
    Route::post('/cards/{id}/activate', [ProfileController::class, 'setActiveCard']);


    Route::post('/transcribe', [ProfileController::class, 'transcribe']);

});



