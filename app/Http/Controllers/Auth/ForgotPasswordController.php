<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\MailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function __construct(
        protected MailService $mailService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Show the forgot password form.
     */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to the user.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Don't reveal if email exists or not for security
            return back()->with('status', __('app.password_reset_link_sent'));
        }

        // Generate token
        $token = Str::random(64);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Send email
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        
        try {
            $this->mailService->sendPasswordReset($user, $resetUrl);
        } catch (\Exception $e) {
            // Log error but don't reveal to user
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        // Log the password reset request
        $this->auditLogService->log(
            'auth.password_reset_requested',
            null,
            $user,
            ['email' => $request->email]
        );

        return back()->with('status', __('app.password_reset_link_sent'));
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Find the token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => __('app.invalid_reset_token')]);
        }

        // Check if token is valid
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => __('app.invalid_reset_token')]);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => __('app.reset_token_expired')]);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => __('app.invalid_reset_token')]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Log the password reset
        $this->auditLogService->log(
            'auth.password_reset_completed',
            null,
            $user,
            ['email' => $request->email]
        );

        return redirect()->route('login')
            ->with('success', __('app.password_reset_success'));
    }
}
