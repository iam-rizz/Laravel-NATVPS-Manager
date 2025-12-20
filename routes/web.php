<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\LanguageController;
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

// Language switch route
Route::get('language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Test toast notifications (remove in production)
Route::get('/test-toast', function () {
    return redirect('/')->with('success', 'This is a success message!');
});
Route::get('/test-toast-warning', function () {
    return redirect('/')->with('warning', 'This is a warning message!');
});
Route::get('/test-toast-error', function () {
    return redirect('/')->with('error', 'This is an error message!');
});

// Test toast in authenticated pages
Route::middleware('auth')->group(function () {
    Route::get('/test-admin-toast', function () {
        return redirect()->route('admin.nat-vps.index')->with('success', 'Test success message from admin!');
    });
    Route::get('/test-user-toast', function () {
        return redirect()->route('user.vps.index')->with('warning', 'Test warning message from user!');
    });
});

/*
|--------------------------------------------------------------------------
| Guest Routes (unauthenticated users only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    
    // Forgot Password
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Challenge Routes (during login)
|--------------------------------------------------------------------------
| These routes handle 2FA verification during the login process.
| The user has provided valid credentials but needs to complete 2FA.
| Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 7.5
*/

Route::middleware('guest')->group(function () {
    Route::get('two-factor/challenge', [TwoFactorChallengeController::class, 'show'])
        ->name('two-factor.challenge');
    Route::post('two-factor/challenge', [TwoFactorChallengeController::class, 'verify'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.verify');
    Route::post('two-factor/recovery', [TwoFactorChallengeController::class, 'verifyRecovery'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.recovery');
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
        // Preserve flash messages during role-based redirect
        $flashData = [];
        foreach (['success', 'error', 'warning', 'info'] as $key) {
            if (session()->has($key)) {
                $flashData[$key] = session($key);
            }
        }
        
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with($flashData);
        }
        return redirect()->route('user.dashboard')->with($flashData);
    })->name('dashboard');
    
    // Profile routes
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Management Routes
|--------------------------------------------------------------------------
| These routes handle 2FA setup, enable/disable, and recovery code management.
| Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 4.1, 4.2, 4.3, 4.4, 5.1, 5.2, 5.3, 5.4
*/

Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::get('two-factor/setup', [TwoFactorController::class, 'setup'])
        ->name('two-factor.setup');
    Route::post('two-factor/enable', [TwoFactorController::class, 'enable'])
        ->name('two-factor.enable');
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])
        ->name('two-factor.disable');
    Route::get('two-factor/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])
        ->name('two-factor.recovery-codes');
    Route::post('two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])
        ->name('two-factor.recovery-codes.regenerate');
    Route::post('two-factor/recovery-codes/view', [TwoFactorController::class, 'viewRecoveryCodes'])
        ->name('two-factor.recovery-codes.view');
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
    
    // Admin NAT VPS Power Actions with rate limiting (10 per minute)
    Route::middleware('throttle:vps-actions')->group(function () {
        Route::post('nat-vps/{natVps}/start', [\App\Http\Controllers\Admin\NatVpsController::class, 'start'])
            ->name('nat-vps.start');
        Route::post('nat-vps/{natVps}/stop', [\App\Http\Controllers\Admin\NatVpsController::class, 'stop'])
            ->name('nat-vps.stop');
        Route::post('nat-vps/{natVps}/restart', [\App\Http\Controllers\Admin\NatVpsController::class, 'restart'])
            ->name('nat-vps.restart');
        Route::post('nat-vps/{natVps}/poweroff', [\App\Http\Controllers\Admin\NatVpsController::class, 'poweroff'])
            ->name('nat-vps.poweroff');
    });
    
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
    
    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        // General settings
        Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'general'])->name('general');
        Route::put('general', [\App\Http\Controllers\Admin\SettingController::class, 'updateGeneral'])->name('general.update');
        
        // Mail settings
        Route::get('mail', [\App\Http\Controllers\Admin\SettingController::class, 'mail'])->name('mail');
        Route::put('mail', [\App\Http\Controllers\Admin\SettingController::class, 'updateMail'])->name('mail.update');
        Route::post('mail/test', [\App\Http\Controllers\Admin\SettingController::class, 'testMail'])->name('mail.test');
        
        // Notification settings
        Route::get('notifications', [\App\Http\Controllers\Admin\SettingController::class, 'notifications'])->name('notifications');
        Route::put('notifications', [\App\Http\Controllers\Admin\SettingController::class, 'updateNotifications'])->name('notifications.update');
        
        // Email templates
        Route::get('email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('email-templates/{emailTemplate}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('email-templates/{emailTemplate}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('email-templates.update');
        Route::get('email-templates/{emailTemplate}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('email-templates.preview');
        
        // Audit log settings
        Route::get('audit', [\App\Http\Controllers\Admin\SettingController::class, 'audit'])->name('audit');
        Route::put('audit', [\App\Http\Controllers\Admin\SettingController::class, 'updateAudit'])->name('audit.update');
    });
    
    // Audit Logs routes (Requirements: 7.1, 7.2, 7.3, 7.4, 7.5)
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
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
        
        // Power actions with rate limiting (10 per minute)
        Route::middleware('throttle:vps-actions')->group(function () {
            Route::post('vps/{natVps}/start', [\App\Http\Controllers\User\VpsController::class, 'start'])->name('vps.start');
            Route::post('vps/{natVps}/stop', [\App\Http\Controllers\User\VpsController::class, 'stop'])->name('vps.stop');
            Route::post('vps/{natVps}/restart', [\App\Http\Controllers\User\VpsController::class, 'restart'])->name('vps.restart');
            Route::post('vps/{natVps}/poweroff', [\App\Http\Controllers\User\VpsController::class, 'poweroff'])->name('vps.poweroff');
        });
        
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
