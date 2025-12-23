<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('audit-logs.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
                {{ __('app.audit_log') }} #{{ $auditLog->id }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Log Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">{{ __('app.audit_log_details') }}</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.audit_action') }}</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if(str_starts_with($auditLog->action, 'auth.'))
                                        bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif(str_starts_with($auditLog->action, 'user.'))
                                        bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @elseif(str_starts_with($auditLog->action, 'vps.'))
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif(str_starts_with($auditLog->action, 'server.'))
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else
                                        bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @endif
                                ">
                                    {{ __('app.audit_action_' . str_replace('.', '_', $auditLog->action)) }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ $auditLog->action }})</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.audit_timestamp') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $auditLog->created_at?->format('F d, Y H:i:s') }}
                                <span class="text-gray-500 dark:text-gray-400">({{ $auditLog->created_at?->diffForHumans() }})</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.audit_actor') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($auditLog->actor)
                                    <div>{{ $auditLog->actor->name }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ $auditLog->actor->email }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">ID: {{ $auditLog->actor_id }}</div>
                                @elseif($auditLog->actor_id)
                                    <div>{{ __('app.deleted_user') }} (ID: {{ $auditLog->actor_id }})</div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('app.system') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.audit_subject') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($auditLog->subject)
                                    <div>{{ class_basename($auditLog->subject_type) }}</div>
                                    @if(isset($auditLog->subject->name))
                                        <div class="text-gray-500 dark:text-gray-400">{{ $auditLog->subject->name }}</div>
                                    @elseif(isset($auditLog->subject->hostname))
                                        <div class="text-gray-500 dark:text-gray-400">{{ $auditLog->subject->hostname }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400 dark:text-gray-500">ID: {{ $auditLog->subject_id }}</div>
                                @elseif($auditLog->subject_id)
                                    <div>{{ class_basename($auditLog->subject_type ?? 'Unknown') }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">ID: {{ $auditLog->subject_id }} ({{ __('app.deleted') }})</div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.audit_ip_address') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $auditLog->ip_address ?? '-' }}
                            </dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.audit_user_agent') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 break-all">
                                {{ $auditLog->user_agent ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Properties Card -->
            @if($auditLog->properties && count($auditLog->properties) > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">{{ __('app.audit_properties') }}</h3>
                        
                        @if(isset($auditLog->properties['old']) || isset($auditLog->properties['new']))
                            <!-- Old/New Values Display -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if(isset($auditLog->properties['old']))
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('app.audit_old_values') }}</h4>
                                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                                            <dl class="space-y-2">
                                                @foreach($auditLog->properties['old'] as $key => $value)
                                                    <div>
                                                        <dt class="text-xs font-medium text-red-700 dark:text-red-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                                        <dd class="text-sm text-red-900 dark:text-red-200 break-all">
                                                            @if(is_array($value))
                                                                <pre class="text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                            @elseif(is_bool($value))
                                                                {{ $value ? 'true' : 'false' }}
                                                            @elseif(is_null($value))
                                                                <span class="italic">null</span>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </dd>
                                                    </div>
                                                @endforeach
                                            </dl>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($auditLog->properties['new']))
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('app.audit_new_values') }}</h4>
                                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                            <dl class="space-y-2">
                                                @foreach($auditLog->properties['new'] as $key => $value)
                                                    <div>
                                                        <dt class="text-xs font-medium text-green-700 dark:text-green-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                                        <dd class="text-sm text-green-900 dark:text-green-200 break-all">
                                                            @if(is_array($value))
                                                                <pre class="text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                            @elseif(is_bool($value))
                                                                {{ $value ? 'true' : 'false' }}
                                                            @elseif(is_null($value))
                                                                <span class="italic">null</span>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </dd>
                                                    </div>
                                                @endforeach
                                            </dl>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if(isset($auditLog->properties['metadata']))
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('app.audit_metadata') }}</h4>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <dl class="space-y-2">
                                            @foreach($auditLog->properties['metadata'] as $key => $value)
                                                <div>
                                                    <dt class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                                    <dd class="text-sm text-gray-900 dark:text-gray-100 break-all">
                                                        @if(is_array($value))
                                                            <pre class="text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                        @elseif(is_bool($value))
                                                            {{ $value ? 'true' : 'false' }}
                                                        @elseif(is_null($value))
                                                            <span class="italic">null</span>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Simple Properties Display -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <dl class="space-y-2">
                                    @foreach($auditLog->properties as $key => $value)
                                        <div>
                                            <dt class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100 break-all">
                                                @if(is_array($value))
                                                    <pre class="text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @elseif(is_bool($value))
                                                    {{ $value ? 'true' : 'false' }}
                                                @elseif(is_null($value))
                                                    <span class="italic">null</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
