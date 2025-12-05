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
use App\Http\Controllers\UserTypeController;

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

Route::get('/manage/organizations', [OrganizationController::class, 'index']);
Route::get('/manage/organizations/tree', [OrganizationController::class, 'tree']);
Route::get('/manage/organizations/{id}', [OrganizationController::class, 'show']);
Route::post('/manage/organizations', [OrganizationController::class, 'store']);
Route::put('/manage/organizations/{id}', [OrganizationController::class, 'update']);
Route::delete('/manage/organizations/{id}', [OrganizationController::class, 'destroy']);
Route::get('/organizations/{id}/users', [OrganizationController::class, 'getUsers']);

Route::get('/manage/org-types', [OrgTypeController::class, 'index']);
Route::get('/manage/org-levels', [OrgLevelController::class, 'index']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);

Route::get('/user-types', [UserTypeController::class, 'index']);
