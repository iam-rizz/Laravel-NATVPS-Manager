<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(
        protected TwoFactorAuthService $twoFactorService
    ) {}

    /**
     * Show the 2FA setup page with QR code and manual entry code.
     * Requirements: 1.1, 1.2
     */
    public function setup(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // If 2FA is already enabled, redirect to recovery codes page
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.recovery-codes')
                ->with('info', __('app.2fa_already_enabled'));
        }

        // Generate a new secret key for setup
        $secret = $request->session()->get('two_factor_secret');
        
        if (!$secret) {
            $secret = $this->twoFactorService->generateSecretKey();
            $request->session()->put('two_factor_secret', $secret);
        }

        // Generate QR code URL
        $qrCodeUrl = $this->twoFactorService->getQrCodeUrl($user, $secret);

        return view('auth.two-factor.setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret,
        ]);
    }

    /**
     * Enable 2FA after verifying the TOTP code.
     * Requirements: 1.3, 1.4, 1.5, 1.6
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        // Check if 2FA is already enabled
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.recovery-codes')
                ->with('info', __('app.2fa_already_enabled'));
        }

        // Get the secret from session
        $secret = $request->session()->get('two_factor_secret');

        if (!$secret) {
            return redirect()->route('two-factor.setup')
                ->with('error', __('app.2fa_session_expired'));
        }

        // Verify the TOTP code
        if (!$this->twoFactorService->verifyCodeWithSecret($secret, $request->code)) {
            return back()->with('error', __('app.2fa_invalid_code'));
        }

        // Enable 2FA and get recovery codes
        $recoveryCodes = $this->twoFactorService->enable($user, $secret);

        // Clear the secret from session
        $request->session()->forget('two_factor_secret');

        // Store recovery codes in session temporarily to display them
        $request->session()->put('two_factor_recovery_codes', $recoveryCodes);

        return redirect()->route('two-factor.recovery-codes')
            ->with('success', __('app.2fa_enabled_success'));
    }


    /**
     * Disable 2FA for the user.
     * Requires password confirmation.
     * Requirements: 4.1, 4.2, 4.3, 4.4
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', __('app.2fa_invalid_password'));
        }

        // Check if 2FA is enabled
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup')
                ->with('info', __('app.2fa_not_enabled'));
        }

        // Disable 2FA
        $this->twoFactorService->disable($user);

        return redirect()->route('two-factor.setup')
            ->with('success', __('app.2fa_disabled_success'));
    }

    /**
     * Show the recovery codes page.
     * Requirements: 5.3
     */
    public function showRecoveryCodes(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Check if 2FA is enabled
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup')
                ->with('info', __('app.2fa_not_enabled'));
        }

        // Get recovery codes from session (only available right after enabling or regenerating)
        $recoveryCodes = $request->session()->pull('two_factor_recovery_codes');
        
        // Get remaining codes count
        $remainingCount = $this->twoFactorService->getRemainingRecoveryCodesCount($user);
        
        // Check if warning should be shown (fewer than 3 codes remaining)
        $showWarning = $remainingCount < 3;

        return view('auth.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
            'remainingCount' => $remainingCount,
            'showWarning' => $showWarning,
        ]);
    }

    /**
     * Regenerate recovery codes.
     * Requires password confirmation.
     * Requirements: 5.1, 5.2, 5.3, 5.4
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', __('app.2fa_invalid_password'));
        }

        // Check if 2FA is enabled
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup')
                ->with('info', __('app.2fa_not_enabled'));
        }

        // Regenerate recovery codes
        $recoveryCodes = $this->twoFactorService->regenerateRecoveryCodes($user);

        // Store recovery codes in session temporarily to display them
        $request->session()->put('two_factor_recovery_codes', $recoveryCodes);

        return redirect()->route('two-factor.recovery-codes')
            ->with('success', __('app.2fa_codes_regenerated'));
    }

    /**
     * View recovery codes after password confirmation.
     * Returns JSON response for AJAX request.
     */
    public function viewRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => __('app.2fa_invalid_password') ?? 'Invalid password'
            ], 422);
        }

        // Check if 2FA is enabled
        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => __('app.2fa_not_enabled') ?? '2FA is not enabled'
            ], 422);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
