<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (unauthenticated users only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard - redirects based on role
    Route::get('dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard (Requirement 10.1, 10.2, 10.3)
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Server management routes (to be implemented in task 5)
    Route::resource('servers', \App\Http\Controllers\Admin\ServerController::class)->except(['show']);
    Route::post('servers/{server}/test-connection', [\App\Http\Controllers\Admin\ServerController::class, 'testConnection'])
        ->name('servers.test-connection');
    
    // NAT VPS management routes (to be implemented in task 6)
    Route::resource('nat-vps', \App\Http\Controllers\Admin\NatVpsController::class)
        ->parameters(['nat-vps' => 'natVps']);
    Route::post('nat-vps/{natVps}/assign', [\App\Http\Controllers\Admin\NatVpsController::class, 'assign'])
        ->name('nat-vps.assign');
    Route::post('nat-vps/{natVps}/unassign', [\App\Http\Controllers\Admin\NatVpsController::class, 'unassign'])
        ->name('nat-vps.unassign');
    Route::get('nat-vps-import', [\App\Http\Controllers\Admin\NatVpsController::class, 'showImport'])
        ->name('nat-vps.import');
    Route::post('nat-vps-import/{server}', [\App\Http\Controllers\Admin\NatVpsController::class, 'importFromServer'])
        ->name('nat-vps.import.server');
    
    // Admin NAT VPS Power Actions
    Route::post('nat-vps/{natVps}/start', [\App\Http\Controllers\Admin\NatVpsController::class, 'start'])
        ->name('nat-vps.start');
    Route::post('nat-vps/{natVps}/stop', [\App\Http\Controllers\Admin\NatVpsController::class, 'stop'])
        ->name('nat-vps.stop');
    Route::post('nat-vps/{natVps}/restart', [\App\Http\Controllers\Admin\NatVpsController::class, 'restart'])
        ->name('nat-vps.restart');
    Route::post('nat-vps/{natVps}/poweroff', [\App\Http\Controllers\Admin\NatVpsController::class, 'poweroff'])
        ->name('nat-vps.poweroff');
    
    // Admin NAT VPS Resource Usage API (async loading)
    Route::get('nat-vps/{natVps}/resource-usage', [\App\Http\Controllers\Admin\NatVpsController::class, 'resourceUsage'])
        ->name('nat-vps.resource-usage');
    
    // Admin Domain Forwarding routes
    Route::get('nat-vps/{natVps}/domain-forwarding', [\App\Http\Controllers\Admin\DomainForwardingController::class, 'index'])
        ->name('nat-vps.domain-forwarding.index');
    Route::post('nat-vps/{natVps}/domain-forwarding', [\App\Http\Controllers\Admin\DomainForwardingController::class, 'store'])
        ->name('nat-vps.domain-forwarding.store');
    Route::put('nat-vps/{natVps}/domain-forwarding/{recordId}', [\App\Http\Controllers\Admin\DomainForwardingController::class, 'update'])
        ->name('nat-vps.domain-forwarding.update');
    Route::delete('nat-vps/{natVps}/domain-forwarding/{recordId}', [\App\Http\Controllers\Admin\DomainForwardingController::class, 'destroy'])
        ->name('nat-vps.domain-forwarding.destroy');
    
    // User management routes (to be implemented in task 7)
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
        ->name('users.reset-password');
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    // User Dashboard (Requirement 10.4)
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    
    // VPS list (no specific VPS access check needed)
    Route::get('vps', [\App\Http\Controllers\User\VpsController::class, 'index'])->name('vps.index');
    
    // VPS-specific routes with access middleware
    Route::middleware('vps.access')->group(function () {
        // VPS viewing and power actions (to be implemented in task 10)
        Route::get('vps/{natVps}', [\App\Http\Controllers\User\VpsController::class, 'show'])->name('vps.show');
        Route::post('vps/{natVps}/start', [\App\Http\Controllers\User\VpsController::class, 'start'])->name('vps.start');
        Route::post('vps/{natVps}/stop', [\App\Http\Controllers\User\VpsController::class, 'stop'])->name('vps.stop');
        Route::post('vps/{natVps}/restart', [\App\Http\Controllers\User\VpsController::class, 'restart'])->name('vps.restart');
        Route::post('vps/{natVps}/poweroff', [\App\Http\Controllers\User\VpsController::class, 'poweroff'])->name('vps.poweroff');
        
        // Resource Usage API (async loading)
        Route::get('vps/{natVps}/resource-usage', [\App\Http\Controllers\User\VpsController::class, 'resourceUsage'])->name('vps.resource-usage');
        
        // Domain forwarding routes
        Route::get('vps/{natVps}/domain-forwarding', [\App\Http\Controllers\User\DomainForwardingController::class, 'index'])
            ->name('vps.domain-forwarding.index');
        Route::post('vps/{natVps}/domain-forwarding', [\App\Http\Controllers\User\DomainForwardingController::class, 'store'])
            ->name('vps.domain-forwarding.store');
        Route::put('vps/{natVps}/domain-forwarding/{recordId}', [\App\Http\Controllers\User\DomainForwardingController::class, 'update'])
            ->name('vps.domain-forwarding.update');
        Route::delete('vps/{natVps}/domain-forwarding/{recordId}', [\App\Http\Controllers\User\DomainForwardingController::class, 'destroy'])
            ->name('vps.domain-forwarding.destroy');
    });
});
