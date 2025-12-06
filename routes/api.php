<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrgTypeController;
use App\Http\Controllers\OrgLevelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\OrgRegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// ============== UTILITY ==============

Route::post('/log-notification', function (Request $request) {
    $type = strtoupper($request->type);
    $message = $request->message;

    Log::channel('notifications')->info("[$type] $message");

    return response()->json(['status' => 'ok']);
});

// ============== AUTHENTICATION ==============

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// ============== PUBLIC ENDPOINTS ==============

// Academic Years Management
Route::get('/manage/academic-years', [AcademicYearController::class, 'index']);
Route::get('/manage/academic-years/tree', [AcademicYearController::class, 'tree']);
Route::post('/manage/academic-years', [AcademicYearController::class, 'store']);
Route::put('/manage/academic-years/{id}', [AcademicYearController::class, 'update']);
Route::delete('/manage/academic-years/{id}', [AcademicYearController::class, 'destroy']);

// Semesters Management
Route::get('/manage/semesters', [SemesterController::class, 'index']);
Route::post('/manage/semesters', [SemesterController::class, 'store']);
Route::get('/manage/semesters/{id}', [SemesterController::class, 'show']);
Route::put('/manage/semesters/{id}', [SemesterController::class, 'update']);
Route::delete('/manage/semesters/{id}', [SemesterController::class, 'destroy']);

// Organizations Management
Route::get('/manage/organizations', [OrganizationController::class, 'index']);
Route::get('/manage/organizations/tree', [OrganizationController::class, 'tree']);
Route::get('/manage/organizations/{id}', [OrganizationController::class, 'show']);
Route::get('/manage/organizations/{id}/users', [OrganizationController::class, 'getUsers']);
Route::post('/manage/organizations', [OrganizationController::class, 'store']);
Route::put('/manage/organizations/{id}', [OrganizationController::class, 'update']);
Route::delete('/manage/organizations/{id}', [OrganizationController::class, 'destroy']);

// Organization Types & Levels
Route::get('/manage/org-types', [OrgTypeController::class, 'index']);
Route::get('/manage/org-levels', [OrgLevelController::class, 'index']);

// Users Management
Route::get('/manage/users', [UserController::class, 'index']);
Route::get('/manage/users/{id}', [UserController::class, 'show']);
Route::post('/manage/users', [UserController::class, 'store']);
Route::put('/manage/users/{id}', [UserController::class, 'update']);
Route::delete('/manage/users/{id}', [UserController::class, 'destroy']);
Route::post('/manage/users/{id}/reset-password', [UserController::class, 'resetPassword']);

// User Types
Route::get('/manage/user-types', [UserTypeController::class, 'index']);

// ============== AUTHENTICATED ENDPOINTS ==============

Route::middleware('auth:sanctum')->group(function () {
    // Account Management
    Route::get('/me', [AccountController::class, 'me']);
    Route::put('/account', [AccountController::class, 'updateAccount']);
    Route::put('/account/change-password', [AccountController::class, 'changePassword']);

    // Organization Status & Join Requests
    Route::get('/me/org-status', [OrgRegistrationController::class, 'getMyStatus']);
    Route::post('/me/join-requests', [OrgRegistrationController::class, 'sendRequest']);

    // Pending Requests (Admin)
    Route::get('/manage/organizations/{id}/pending-requests', [OrganizationController::class, 'getPendingRequests']);
});
