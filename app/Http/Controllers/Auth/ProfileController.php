<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Show the profile edit form.
     */
    public function edit(): View
    {
        return view('auth.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $user->fill($validated);
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Log the profile update
        $this->auditLogService->log(
            'user.profile_updated',
            $user,
            $user,
            [
                'old' => $oldValues,
                'new' => $validated,
            ]
        );

        return redirect()->route('profile.edit')
            ->with('success', __('app.profile_updated'));
    }

    /**
     * Show the change password form.
     */
    public function showChangePassword(): View
    {
        return view('auth.profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Log the password change
        $this->auditLogService->log(
            'user.password_changed',
            $user,
            $user,
            ['self_change' => true]
        );

        return redirect()->route('profile.edit')
            ->with('success', __('app.password_changed'));
    }
}
