<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrgTypeController;
use App\Http\Controllers\OrgLevelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccountController;

Route::post('/log-notification', function (Request $request) {
    $type = strtoupper($request->type);
    $message = $request->message;

    Log::channel('notifications')->info("[$type] $message");

    return response()->json(['status' => 'ok']);
});


Route::get('/manage/academic-years/tree', [AcademicYearController::class, 'tree']);
Route::post('/manage/academic-years', [AcademicYearController::class, 'store']);
Route::put('/manage/academic-years/{id}', [AcademicYearController::class, 'update']);
Route::delete('/manage/academic-years/{id}', [AcademicYearController::class, 'destroy']);

Route::get('manage/semesters', [SemesterController::class, 'index']);
Route::post('manage/semesters', [SemesterController::class, 'store']);
Route::put('manage/semesters/{id}', [SemesterController::class, 'update']);
Route::delete('manage/semesters/{id}', [SemesterController::class, 'destroy']);

Route::get('/manage/organizations/tree', [OrganizationController::class, 'tree']);
Route::get('/manage/organizations/{id}', [OrganizationController::class, 'show']);
Route::post('/manage/organizations', [OrganizationController::class, 'store']);
Route::put('/manage/organizations/{id}', [OrganizationController::class, 'update']);
Route::delete('/manage/organizations/{id}', [OrganizationController::class, 'destroy']);

Route::get('/manage/org-types', [OrgTypeController::class, 'index']);
Route::get('/manage/org-levels', [OrgLevelController::class, 'index']);

// user profile
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/me', [AccountController::class, 'me']);
//     Route::put('/me', [AccountController::class, 'updateAccount']);
//     Route::put('/me/password', [AccountController::class, 'changePassword']);
// });

Route::get('/users', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::put('/user/{id}/password', [UserController::class, 'resetPassword']);
