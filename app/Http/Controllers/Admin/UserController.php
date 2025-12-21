<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected AuditLogService $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }
    /**
     * Display a listing of all users with VPS counts.
     * Requirements: 8.5
     */
    public function index()
    {
        $users = User::withCount('natVps')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     * Requirements: 8.1, 2.1
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Will be hashed by the model cast
            'role' => $validated['role'],
        ]);

        // Log user creation
        $this->auditLogService->log(
            'user.created',
            $request->user(),
            $user,
            [
                'new' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                ],
            ]
        );

        return redirect()
            ->route('users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }


    /**
     * Display the specified user.
     * Requirements: 8.5
     */
    public function show(User $user)
    {
        $user->load('natVps.server');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     * Requirements: 8.2, 2.2
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        // Capture old values before update
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
        ];

        $user->update($validated);

        // Capture new values after update
        $newValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
        ];

        // Log user update with old and new values
        $this->auditLogService->log(
            'user.updated',
            $request->user(),
            $user,
            AuditLogService::makeUpdateProperties($oldValues, $newValues)
        );

        return redirect()
            ->route('users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user from storage.
     * Requirements: 8.3, 2.3
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent deleting yourself
        if ($user->id === $request->user()?->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;

        // Capture user details before deletion for audit log
        $deletedUserDetails = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
        ];

        // Remove NAT VPS assignments (set user_id to null)
        $user->natVps()->update(['user_id' => null]);

        // Log user deletion before actually deleting
        $this->auditLogService->log(
            'user.deleted',
            $request->user(),
            $user,
            [
                'deleted' => $deletedUserDetails,
            ]
        );

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "User '{$userName}' deleted successfully.");
    }

    /**
     * Reset the user's password.
     * Requirements: 8.4, 2.4
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ]);

        $user->update([
            'password' => $validated['password'], // Will be hashed by the model cast
        ]);

        // Log password reset
        $this->auditLogService->log(
            'user.password_reset',
            $request->user(),
            $user,
            [
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
            ]
        );

        return redirect()
            ->back()
            ->with('success', "Password for '{$user->name}' has been reset successfully.");
    }
}
