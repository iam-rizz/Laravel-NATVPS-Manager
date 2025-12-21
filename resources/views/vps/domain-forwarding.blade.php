<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('vps.show', $natVps) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Domain Forwarding: {{ $natVps->hostname }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($apiError)
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-300">
                    <strong>API Warning:</strong> {{ $apiError }}
                </div>
            @endif

            <!-- Port Configuration Info -->
            @if(!empty($portConfig))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Port Configuration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            @if(!empty($portConfig['allowed_ports']))
                                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <span class="font-medium text-green-800 dark:text-green-300">Allowed Ports:</span>
                                    <p class="text-green-700 dark:text-green-400 mt-1 font-mono text-xs">{{ $portConfig['allowed_ports'] }}</p>
                                </div>
                            @endif
                            @if(!empty($portConfig['reserved_ports']))
                                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <span class="font-medium text-red-800 dark:text-red-300">Reserved Ports (TCP):</span>
                                    <p class="text-red-700 dark:text-red-400 mt-1 font-mono text-xs">{{ $portConfig['reserved_ports'] }}</p>
                                </div>
                            @endif
                            @if(!empty($portConfig['reserved_ports_http']))
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                    <span class="font-medium text-yellow-800 dark:text-yellow-300">Reserved Ports (HTTP/HTTPS):</span>
                                    <p class="text-yellow-700 dark:text-yellow-400 mt-1 font-mono text-xs">{{ $portConfig['reserved_ports_http'] }}</p>
                                </div>
                            @endif
                            @if(!empty($portConfig['src_ips']))
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <span class="font-medium text-blue-800 dark:text-blue-300">Source IPs:</span>
                                    <p class="text-blue-700 dark:text-blue-400 mt-1 font-mono text-xs">{{ implode(', ', $portConfig['src_ips']) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Add New Rule Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add Forwarding Rule</h3>
                    <form action="{{ route('vps.domain-forwarding.store', $natVps) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="protocol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Protocol</label>
                                <select name="protocol" id="protocol" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        onchange="toggleDomainField(this.value)">
                                    <option value="tcp">TCP (Port Forwarding)</option>
                                    <option value="http">HTTP (Domain Forwarding)</option>
                                    <option value="https">HTTPS (Domain Forwarding)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="protocol-hint">
                                    TCP: Forward port to VPS. HTTP/HTTPS: Forward domain to VPS.
                                </p>
                            </div>

                            <div id="domain-field" style="display: none;">
                                <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Domain <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="domain" id="domain"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="example.com">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Required for HTTP/HTTPS forwarding</p>
                            </div>

                            <div>
                                <label for="source_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Source Port <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="source_port" id="source_port" required min="1" max="65535"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="30000">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="source-port-hint">External port (e.g., 30000-65000 for TCP)</p>
                            </div>

                            <div>
                                <label for="destination_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Destination Port <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="destination_port" id="destination_port" required min="1" max="65535"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="22">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Port inside VPS (e.g., 22 for SSH, 80 for HTTP)</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Rules from Virtualizor API -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Existing Rules 
                        <span class="text-sm font-normal text-gray-500">(from Virtualizor)</span>
                    </h3>
                    
                    @if(empty($forwardings))
                        <p class="text-sm text-gray-500 dark:text-gray-400">No forwarding rules configured.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Domain</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Protocol</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Source Port</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Destination Port</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($forwardings as $forwarding)
                                        @php
                                            $recordId = $forwarding['id'] ?? $forwarding['vdfid'] ?? 0;
                                            $protocol = strtoupper($forwarding['protocol'] ?? 'TCP');
                                            $domain = $forwarding['src_hostname'] ?? '-';
                                            $srcPort = $forwarding['src_port'] ?? '-';
                                            $destPort = $forwarding['dest_port'] ?? '-';
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $domain }}</td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $badgeClass = match($protocol) {
                                                        'HTTPS' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'HTTP' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                        default => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                                    };
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                                    {{ $protocol }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $srcPort }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $destPort }}</td>
                                            <td class="px-4 py-3 text-right">
                                                @if($recordId)
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" 
                                                                onclick="openEditModal({{ $recordId }}, '{{ strtolower($protocol) }}', '{{ $domain }}', '{{ $srcPort }}', '{{ $destPort }}')"
                                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <form action="{{ route('vps.domain-forwarding.destroy', [$natVps, $recordId]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this rule?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Edit Forwarding Rule</h3>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Protocol</label>
                            <select name="protocol" id="edit_protocol" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="tcp">TCP</option>
                                <option value="http">HTTP</option>
                                <option value="https">HTTPS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Domain/Hostname</label>
                            <input type="text" name="domain" id="edit_domain"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Source Port</label>
                            <input type="number" name="source_port" id="edit_source_port" required min="1" max="65535"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Destination Port</label>
                            <input type="number" name="destination_port" id="edit_destination_port" required min="1" max="65535"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(recordId, protocol, domain, srcPort, destPort) {
            document.getElementById('editForm').action = '{{ route("vps.domain-forwarding.index", $natVps) }}/' + recordId;
            document.getElementById('edit_protocol').value = protocol;
            document.getElementById('edit_domain').value = domain === '-' ? '' : domain;
            document.getElementById('edit_source_port').value = srcPort;
            document.getElementById('edit_destination_port').value = destPort;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function toggleDomainField(protocol) {
            const domainField = document.getElementById('domain-field');
            const domainInput = document.getElementById('domain');
            const sourcePortInput = document.getElementById('source_port');
            const destPortInput = document.getElementById('destination_port');
            const sourcePortHint = document.getElementById('source-port-hint');
            const protocolHint = document.getElementById('protocol-hint');
            
            if (protocol === 'tcp') {
                // TCP - hide domain field, clear ports
                domainField.style.display = 'none';
                domainInput.value = '';
                domainInput.removeAttribute('required');
                sourcePortInput.value = '';
                destPortInput.value = '';
                sourcePortInput.placeholder = '30000';
                destPortInput.placeholder = '22';
                sourcePortHint.textContent = 'External port (e.g., 30000-65000)';
                protocolHint.textContent = 'TCP: Forward external port to internal VPS port.';
            } else if (protocol === 'http') {
                // HTTP - show domain field, set default ports
                domainField.style.display = 'block';
                domainInput.setAttribute('required', 'required');
                sourcePortInput.value = '80';
                destPortInput.value = '80';
                sourcePortInput.placeholder = '80';
                destPortInput.placeholder = '80';
                sourcePortHint.textContent = 'Default: 80 for HTTP';
                protocolHint.textContent = 'HTTP: Forward domain traffic on port 80 to VPS.';
            } else {
                // HTTPS - show domain field, set default ports
                domainField.style.display = 'block';
                domainInput.setAttribute('required', 'required');
                sourcePortInput.value = '443';
                destPortInput.value = '443';
                sourcePortInput.placeholder = '443';
                destPortInput.placeholder = '443';
                sourcePortHint.textContent = 'Default: 443 for HTTPS';
                protocolHint.textContent = 'HTTPS: Forward secure domain traffic on port 443 to VPS.';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleDomainField(document.getElementById('protocol').value);
        });
    </script>
</x-app-layout>
