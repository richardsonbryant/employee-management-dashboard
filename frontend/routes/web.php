<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Helpers\GoogleCalendar;

use App\Services\UserDataApiService;

use Illuminate\Support\Facades\Http;

Route::get('/test-api', function () {
    $response = Http::get('http://localhost:8080/api/userdata'); // tes langsung endpoint kamu
    return $response->json();
});

Route::get('/test-service', function (App\Services\UserDataApiService $service) {
    dd($service->getAllUserData());
});


// ======================= AUTH =======================
Route::get("/login", [AuthController::class, "login"])->name("login");
Route::post("/login", [AuthController::class, "loginPost"])->name("login.post");
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Registration
Route::get('/register', [AuthController::class, "register"])->name('register');
Route::post('/register', [AuthController::class, "registerPost"])->name("register.post");
// User Registration
// Route::get('/register', [AuthController::class, "register"])->name('register');
// Route::post('/register', [AuthController::class, "registerPost"])->name("register.post");

// // ======================= HOME =======================
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin-dashboard')
            : redirect()->route('employee-dashboard');
    }
    return redirect()->route('login');
});

Route::get('/Unauthorized-access', function () {
    return view('unauthorized'); // View untuk halaman Unauthorized
})->name('unauthorized');


// ======================= ADMIN ROUTES =======================
Route::middleware(['checkrole:admin'])->group(function () {
    // Dashboard
    Route::get('/admin-dashboard', [UserController::class, "loadAllUsers"])->name('admin-dashboard');


    // Summary Employee
    Route::get('/summary-employee', [UserController::class, "loadSummaryEmployee"])->name('summaryEmployee');

    // Edit User Data
    Route::get('/edit/user/{id}', [UserController::class, 'loadUserApprovalForm'])->name('EditUser');
    Route::get('/edit/wfh/{id}', [UserController::class, 'loadWfhApprovalForm'])->name('EditWfh');
    Route::get('/edit/sick/{id}', [UserController::class, 'loadSickApprovalForm'])->name('EditSick');


    // Handle User Data
    Route::post('/handle/user/{id}', [UserController::class, 'handleUserData'])->name('handleUserData.post');
    Route::post('/handle/wfh/{id}', [UserController::class, 'handleWfhData'])->name('handleWfhData.post');
    Route::post('/handle/sick/{id}', [UserController::class, 'handleSickData'])->name('handleSickData.post');



    // Broadcast
    Route::get('/broadcast', [UserController::class, 'showBroadcastForm'])->name('broadcast.form');
    Route::post('/broadcast', [UserController::class, 'sendBroadcast'])->name('broadcast.post');

    Route::get('/export-user-data/{email}', [UserController::class, 'exportUserData'])->name('exportUserData');

    Route::get('/attendance/export', [UserController::class, 'exportAttendance'])->name('exportAttendance');


    Route::get('/summary-attendance', [UserController::class, 'loadSummaryAttendance'])->name('summaryAttendance');


    // No Permission User (tidak ada kabar)
    Route::get("/add/noPermissionUser", [UserController::class, "AddNoPermissionUserForm"])->name('noPermissionUser');
    Route::post("/add/noPermissionUser", [UserController::class, "addNoPermissionUser"])->name('noPermissionUser.post')->middleware('throttle:10,1');
    // Route::get('/edit/noPermissionUser/{id}', [UserController::class, 'loadNoPermissionUserApprovalForm'])->name('EditNoPermissionUser');
    // Route::post('/handle/noPermissionUser/{id}', [UserController::class, 'handleNoPermissionUser'])->name('handleNoPermission.post');
});

// ======================= EMPLOYEE ROUTES =======================
Route::middleware(['checkrole:employee'])->group(function () {
    // Employee Dashboard
    Route::get('/employee-dashboard', [UserController::class, "loadUserDashboard"])->name('employee-dashboard');

    // View History (Employee Only)
    Route::get('/view-history/{email}', [UserController::class, 'viewHistoryData'])->name('viewHistoryData');

    // Mailbox
    Route::get('/mailbox', [UserController::class, 'showMailbox'])->name('mailbox');

    Route::post('/clock-in', [UserController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [UserController::class, 'clockOut'])->name('clock-out');

    Route::get('/employee-request/{id}', [UserController::class, 'loadEmployeeRequest'])->name('loadEmployeeRequest');
});

// ======================= SHARED ROUTES =======================
// View Specific User Data
Route::get('/view-user/{email}', [UserController::class, 'viewUserData'])->name('viewUserData');
Route::get('/view-history/{email}', [UserController::class, 'viewHistoryData'])->name('viewHistoryData');

// Leave Status Update
Route::post('/leave/update-status/{id}', [UserController::class, 'updateLeaveStatus'])->name('update.leave.status');

// Broadcast
Route::get('/broadcast/view/{id}', [UserController::class, 'viewBroadcast'])->name('broadcast.view');
Route::post('/broadcast/accept/{id}', [UserController::class, 'acceptBroadcast'])->name('broadcast.accept');
Route::post('/broadcast/reject/{id}', [UserController::class, 'rejectBroadcast'])->name('broadcast.reject');

// Add User
Route::get("/add/user", [UserController::class, "AddUserForm"])->name('addUser');
Route::post("/add/user", [UserController::class, "addUser"])->name('addUser.post')->middleware('throttle:10,1');

// Add WFH User
Route::get("/add/wfh-user", [UserController::class, "AddWfhUserForm"])->name('addWfhUser');
Route::post("/add/wfh-user", [UserController::class, "addWfhUser"])->name('addWfhUser.post')->middleware('throttle:10,1');

// Add Sick User
Route::get("/add/sick-user", [UserController::class, "AddSickUserForm"])->name('addSickUser');
Route::post("/add/sick-user", [UserController::class, "addSickUser"])->name('addSickUser.post')->middleware('throttle:10,1');
