<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VpsController;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
    
    Route::get('two-factor/challenge', [TwoFactorChallengeController::class, 'show'])->name('two-factor.challenge');
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
    
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::get('two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::get('two-factor/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');
    Route::post('two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes.regenerate');
    Route::post('two-factor/recovery-codes/view', [TwoFactorController::class, 'viewRecoveryCodes'])->name('two-factor.recovery-codes.view');
});


/*
|--------------------------------------------------------------------------
| Main Application Routes (Unified)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'two-factor'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // VPS Management
    Route::get('vps', [VpsController::class, 'index'])->name('vps.index');
    Route::get('vps/create', [VpsController::class, 'create'])->name('vps.create');
    Route::post('vps', [VpsController::class, 'store'])->name('vps.store');
    Route::get('vps/import', [VpsController::class, 'showImport'])->name('vps.import');
    Route::post('vps/import/{server}', [VpsController::class, 'importFromServer'])->name('vps.import.server');
    Route::get('vps/{natVps}', [VpsController::class, 'show'])->name('vps.show');
    Route::get('vps/{natVps}/edit', [VpsController::class, 'edit'])->name('vps.edit');
    Route::put('vps/{natVps}', [VpsController::class, 'update'])->name('vps.update');
    Route::delete('vps/{natVps}', [VpsController::class, 'destroy'])->name('vps.destroy');
    
    // VPS Assignment
    Route::post('vps/{natVps}/assign', [VpsController::class, 'assign'])->name('vps.assign');
    Route::post('vps/{natVps}/unassign', [VpsController::class, 'unassign'])->name('vps.unassign');
    
    // VPS Power Actions
    Route::middleware('throttle:vps-actions')->group(function () {
        Route::post('vps/{natVps}/start', [VpsController::class, 'start'])->name('vps.start');
        Route::post('vps/{natVps}/stop', [VpsController::class, 'stop'])->name('vps.stop');
        Route::post('vps/{natVps}/restart', [VpsController::class, 'restart'])->name('vps.restart');
        Route::post('vps/{natVps}/poweroff', [VpsController::class, 'poweroff'])->name('vps.poweroff');
    });
    
    // VPS Resource Usage
    Route::get('vps/{natVps}/resource-usage', [VpsController::class, 'resourceUsage'])->name('vps.resource-usage');
    
    // VPS SSH Credentials Update (user can update their own)
    Route::put('vps/{natVps}/ssh', [VpsController::class, 'updateSshCredentials'])->name('vps.update-ssh');
    
    // Domain Forwarding
    Route::get('vps/{natVps}/domain-forwarding', [\App\Http\Controllers\DomainForwardingController::class, 'index'])->name('vps.domain-forwarding.index');
    Route::post('vps/{natVps}/domain-forwarding', [\App\Http\Controllers\DomainForwardingController::class, 'store'])->name('vps.domain-forwarding.store');
    Route::put('vps/{natVps}/domain-forwarding/{recordId}', [\App\Http\Controllers\DomainForwardingController::class, 'update'])->name('vps.domain-forwarding.update');
    Route::delete('vps/{natVps}/domain-forwarding/{recordId}', [\App\Http\Controllers\DomainForwardingController::class, 'destroy'])->name('vps.domain-forwarding.destroy');
    
    // Console Access
    Route::get('console', [ConsoleController::class, 'index'])->name('console.index');
    Route::get('console/proxy-health', [ConsoleController::class, 'proxyHealth'])->name('console.proxy-health');
    Route::get('console/{natVps}', [ConsoleController::class, 'show'])->name('console.show');
    Route::get('console/{natVps}/vnc', [ConsoleController::class, 'getVncDetails'])->name('console.vnc');
    Route::get('console/{natVps}/ssh', [ConsoleController::class, 'getSshDetails'])->name('console.ssh');
    Route::post('console/{natVps}/token', [ConsoleController::class, 'generateToken'])->name('console.token');
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    // Server Management
    Route::resource('servers', \App\Http\Controllers\Admin\ServerController::class)->except(['show']);
    Route::post('servers/{server}/test-connection', [\App\Http\Controllers\Admin\ServerController::class, 'testConnection'])->name('servers.test-connection');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'general'])->name('general');
        Route::put('general', [\App\Http\Controllers\Admin\SettingController::class, 'updateGeneral'])->name('general.update');
        
        Route::get('mail', [\App\Http\Controllers\Admin\SettingController::class, 'mail'])->name('mail');
        Route::put('mail', [\App\Http\Controllers\Admin\SettingController::class, 'updateMail'])->name('mail.update');
        Route::post('mail/test', [\App\Http\Controllers\Admin\SettingController::class, 'testMail'])->name('mail.test');
        
        Route::get('notifications', [\App\Http\Controllers\Admin\SettingController::class, 'notifications'])->name('notifications');
        Route::put('notifications', [\App\Http\Controllers\Admin\SettingController::class, 'updateNotifications'])->name('notifications.update');
        
        Route::get('email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('email-templates/{emailTemplate}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('email-templates/{emailTemplate}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('email-templates.update');
        Route::get('email-templates/{emailTemplate}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('email-templates.preview');
        
        Route::get('audit', [\App\Http\Controllers\Admin\SettingController::class, 'audit'])->name('audit');
        Route::put('audit', [\App\Http\Controllers\Admin\SettingController::class, 'updateAudit'])->name('audit.update');
    });
    
    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});
