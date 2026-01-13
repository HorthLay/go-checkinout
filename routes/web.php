<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    if (Auth::check()) {
        // User is logged in, redirect based on role
        if (Auth::user()->role_type === 'admin') {
            return redirect('/admin/dashboard'); // or route('admin.dashboard')
        } else {
            return redirect('/home'); // or route('home') for regular users
        }
    }
    
    // User is not logged in, redirect to login
    return redirect('/login');
});


// Routes for authenticated users only
Route::middleware(['auth'])->group(function () {
    Route::get('/home',[HomeController::class,'index'])->name('home');
     Route::get('/checkin', [CheckinController::class, 'index'])->name('checkin');
    Route::get('/my-schedule', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/support', [HomeController::class, 'support'])->name('support');
      Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin');
    Route::get('/attendance/verify', [CheckInController::class, 'verify'])->name('attendance.verify');
    Route::post('/attendance/submit', [CheckInController::class, 'submit'])->name('attendance.submit');


    Route::get('/telegram/bind', [TelegramController::class, 'initiateBind'])->name('telegram.bind');
    Route::get('/telegram/unbind', [TelegramController::class, 'unbind'])->name('telegram.unbind');
});

Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login-submit', [LoginController::class,'login'])->name('login.submit');



// Routes for admin users only
Route::middleware(['auth', 'admin'])->group(function () {
   
    Route::get('/admin/dashboard',[AdminController::class,'index'])->name('admin.dashboard');

    Route::get('/log-attendance',[AdminController::class,'attendance'])->name('admin.attendance.index');
     Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::post('/notifications/mark-all-read', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [AdminController::class, 'markNotificationRead'])->name('admin.notifications.mark-read');
    Route::delete('/notifications/{id}', [AdminController::class, 'deleteNotification'])->name('admin.notifications.delete');
    Route::delete('/notifications/delete-all-read', [AdminController::class, 'deleteAllReadNotifications'])->name('admin.notifications.delete-all-read');
   
// Attendance
        Route::post('/schedule', [AttendanceController::class, 'storeSchedule'])->name('admin.attendance.schedule.store');
        Route::post('/dayoff', [AttendanceController::class, 'storeDayOff'])->name('admin.attendance.dayoff.store');
        Route::delete('/dayoff/{id}', [AttendanceController::class, 'deleteDayOff'])->name('admin.attendance.dayoff.delete');

// employee 
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
//    qr
     Route::get('/qrcode', [AdminController::class, 'qrmake'])->name('qrcode'); 
    //  report

     Route::get('/reports', [AdminController::class, 'report'])->name('reports');
     Route::get('/reports/print', [AdminController::class, 'reportPrint'])->name('reports.print');
     Route::get('/reports/export-csv', [AdminController::class, 'exportCSV'])->name('reports.export-csv');

    //  created map
 Route::get('/map', [AdminController::class, 'mapcreated'])->name('map.created');
    Route::get('/map/create', [AdminController::class, 'create'])->name('map.create');
    Route::post('/map', [AdminController::class, 'store'])->name('map.store');
    Route::get('/map/{id}/edit', [AdminController::class, 'edit'])->name('map.edit');
    Route::put('/map/{id}', [AdminController::class, 'update'])->name('map.update');
    Route::patch('/map/{id}/toggle', [AdminController::class, 'toggle'])->name('map.toggle');
    Route::delete('/map/{id}', [AdminController::class, 'destroy'])->name('map.destroy');


    // setting
     Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', action: [SettingController::class, 'update'])->name('settings.update');
     Route::post('/settings/export', [SettingController::class, 'exportData'])->name('settings.export');
    Route::post('/settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
    Route::delete('/settings/reset-data', [SettingController::class, 'resetData'])->name('settings.reset-data');
});

Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

Auth::routes(['verify' => true]);