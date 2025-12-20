<x-app-layout>
    <x-slot name="header">
        {{ __('app.users') }}
    </x-slot>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-surface-500 dark:text-surface-400">{{ __('app.manage_users') ?? 'Manage system users' }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('app.add_user') ?? 'Add User' }}
        </a>
    </div>

    @if($users->isEmpty())
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-12">
                <div class="w-16 h-16 mx-auto bg-surface-100 dark:bg-surface-800 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">{{ __('app.no_users') ?? 'No users' }}</h3>
                <p class="text-surface-500 dark:text-surface-400 mb-6">{{ __('app.get_started_add_user') ?? 'Get started by adding a new user.' }}</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    {{ __('app.add_user') ?? 'Add User' }}
                </a>
            </div>
        </div>
    @else
        <!-- Desktop Table View -->
        <div class="hidden md:block">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('app.name') ?? 'Name' }}</th>
                            <th>{{ __('app.email') }}</th>
                            <th>{{ __('app.role') ?? 'Role' }}</th>
                            <th>{{ __('app.assigned_vps') ?? 'Assigned VPS' }}</th>
                            <th>{{ __('app.created') ?? 'Created' }}</th>
                            <th class="text-right">{{ __('app.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-medium">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-surface-900 dark:text-white">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role->value === 'admin')
                                        <span class="badge badge-primary">{{ __('app.admin') ?? 'Admin' }}</span>
                                    @else
                                        <span class="badge badge-info">{{ __('app.user') ?? 'User' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $user->nat_vps_count }}</span>
                                </td>
                                <td class="text-surface-500 dark:text-surface-400">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn btn-sm btn-ghost text-blue-600 dark:text-blue-400"
                                           title="{{ __('app.view') ?? 'View' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-sm btn-ghost"
                                           title="{{ __('app.edit') ?? 'Edit' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if($user->id !== auth()->user()?->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('app.confirm_delete_user') ?? 'Are you sure?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-ghost text-red-600 dark:text-red-400"
                                                    title="{{ __('app.delete') ?? 'Delete' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @foreach($users as $user)
                <div class="card">
                    <div class="card-body">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-medium">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-medium text-surface-900 dark:text-white">{{ $user->name }}</h3>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            @if($user->role->value === 'admin')
                                <span class="badge badge-primary">{{ __('app.admin') ?? 'Admin' }}</span>
                            @else
                                <span class="badge badge-info">{{ __('app.user') ?? 'User' }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400 mb-4">
                            <span>{{ $user->nat_vps_count }} VPS</span>
                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-secondary flex-1">
                                {{ __('app.view') ?? 'View' }}
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-secondary flex-1">
                                {{ __('app.edit') ?? 'Edit' }}
                            </a>
                            @if($user->id !== auth()->user()?->id)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('app.confirm_delete_user') ?? 'Are you sure?' }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-full">
                                    {{ __('app.delete') ?? 'Delete' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
