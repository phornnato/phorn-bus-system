<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripPointController;
use App\Http\Controllers\TripScheduleController;
use App\Http\Controllers\UserRoleController;
use App\Models\Location;
use App\Http\Controllers\UserController;

// -----------------------------
// Public Routes
// -----------------------------
Route::get('/', [BusController::class, 'index'])->name('home');
Route::get('/search', [BusController::class, 'search'])->name('search');
Route::get('/booking/{trip_id}', [BookingController::class, 'showBookingPage'])->name('booking.show');
Route::get('/trips/{id}/book', [TripController::class, 'showBooking'])->name('trips.book');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// -----------------------------
// User Info (Public Users)
// -----------------------------
Route::get('/user/register', [UserController::class, 'showRegister'])->name('user.register');
Route::post('/user/register', [UserController::class, 'register'])->name('user.register.post');

Route::get('/user/login', [UserController::class, 'showLogin'])->name('user.login');
Route::post('/user/login', [UserController::class, 'login'])->name('user.login.post');

Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
Route::get('/user/logout', [UserController::class, 'logout'])->name('user.logout');



// -----------------------------
// Authentication Routes
// -----------------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// -----------------------------
// User Dashboard (Normal Users)
// -----------------------------
Route::get('/dashboard', function () {
    if (!session()->has('user_id')) {
        return redirect()->route('login');
    }
    return view('admin.dashboard');
})->name('dashboard');

// -----------------------------
// Admin Routes
// -----------------------------
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        if (!session()->has('user_id') || session('role') !== 'Admin') {
            return redirect()->route('dashboard'); // redirect non-admins
        }
        return app()->make(App\Http\Controllers\AdminController::class)->dashboard();
    })->name('admin.dashboard');

    Route::group([], function () {
        Route::resource('users_role', UserRoleController::class);
        Route::resource('location', LocationController::class);
        Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

        // trip management routes

        Route::get('trips', [TripController::class, 'index'])->name('admin.trips.index');
        Route::get('trips/create', [TripController::class, 'create'])->name('admin.trips.create');
        Route::post('trips/store', [TripController::class, 'store'])->name('admin.trips.store');
        Route::get('trips/edit/{id}', [TripController::class, 'edit'])->name('admin.trips.edit');
        Route::post('trips/update/{id}', [TripController::class, 'update'])->name('admin.trips.update');
        Route::delete('trips/delete/{id}', [TripController::class, 'destroy'])->name('admin.trips.delete');

        // Trip Schedules CRUD
        Route::get('trip-schedules', [TripScheduleController::class, 'index'])->name('admin.trip_schedules.index');
        Route::get('trip-schedules/create', [TripScheduleController::class, 'create'])->name('admin.trip_schedules.create');
        Route::post('trip-schedules/store', [TripScheduleController::class, 'store'])->name('admin.trip_schedules.store');
        Route::get('trip-schedules/edit/{id}', [TripScheduleController::class, 'edit'])->name('admin.trip_schedules.edit');
        Route::post('trip-schedules/update/{id}', [TripScheduleController::class, 'update'])->name('admin.trip_schedules.update');
        Route::delete('trip-schedules/delete/{id}', [TripScheduleController::class, 'destroy'])->name('admin.trip_schedules.delete');

        // Trip Points CRUD
        Route::get('trip-points', [TripPointController::class, 'index'])->name('admin.trip_points.index');
        Route::get('trip-points/create', [TripPointController::class, 'create'])->name('admin.trip_points.create');
        Route::post('trip-points/store', [TripPointController::class, 'store'])->name('admin.trip_points.store');
        Route::get('trip-points/edit/{id}', [TripPointController::class, 'edit'])->name('admin.trip_points.edit');
        Route::post('trip-points/update/{id}', [TripPointController::class, 'update'])->name('admin.trip_points.update');
        Route::delete('trip-points/delete/{id}', [TripPointController::class, 'destroy'])->name('admin.trip_points.delete');

        Route::get('users-info', [UserController::class, 'indexRecord'])->name('admin.user_info.index');

        Route::get('booking', [AdminController::class,'booking'])->name('admin.booking.index');
        Route::patch('/admin/bookings/{id}/confirm', [AdminController::class, 'confirmBooking'])->name('admin.bookings.confirm');
        Route::patch('/admin/bookings/{id}/cancel', [AdminController::class, 'cancelBooking'])->name('admin.bookings.cancel');
        

    });
});

