<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
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
     Route::get('/checkin', [HomeController::class, 'check-in'])->name('checkin');
    Route::get('/attendance', [HomeController::class, 'attendance'])->name('attendance');
    Route::get('/reports', [HomeController::class, 'report'])->name('reports');
    Route::get('/settings', [HomeController::class, 'setting'])->name('settings');
    Route::get('/support', [HomeController::class, 'support'])->name('support');



    Route::get('/telegram/bind', [TelegramController::class, 'initiateBind'])->name('telegram.bind');
    Route::get('/telegram/unbind', [TelegramController::class, 'unbind'])->name('telegram.unbind');
});

Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login-submit', [LoginController::class,'login'])->name('login.submit');



// Routes for admin users only
Route::middleware(['auth', 'admin'])->group(function () {
   
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
Route::get('/admin/view', [AdminController::class,'adminview'])->name('admin.attendance.index');


// employee 
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
   
     Route::get('/qrcode', [AdminController::class, 'qrmake'])->name('qrcode'); 
});

Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

Auth::routes(['verify' => true]);