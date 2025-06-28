<tr class="hover:bg-gray-50" data-user-id="{{ $access->id }}">
    <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox" class="user-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $access->id }}">
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                <span class="text-white font-semibold text-sm">
                    {{ strtoupper(substr($access->email, 0, 2)) }}
                </span>
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                    {{ $access->user?->name ?? 'Invited User' }}
                </div>
                <div class="text-sm text-gray-500">{{ $access->email }}</div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <select name="role" onchange="updateUserRole({{ $access->id }}, this.value)"
                class="text-sm border border-gray-300 rounded px-2 py-1 focus-ring">
            <option value="audience" {{ $access->role == 'audience' ? 'selected' : '' }}>Audience</option>
            <option value="admin" {{ $access->role == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <div class="text-sm text-gray-900">{{ $access->invited_at->format('M j, Y') }}</div>
        <div class="text-xs text-gray-500">{{ $access->invited_at->format('g:i A') }}</div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        @if($access->last_accessed_at)
            <div class="text-sm text-gray-900">{{ $access->last_accessed_at->format('M j, Y') }}</div>
            <div class="text-xs text-gray-500">{{ $access->last_accessed_at->format('g:i A') }}</div>
        @else
            <span class="text-sm text-gray-400">Never</span>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <button onclick="removeUser({{ $access->id }})"
                class="text-red-600 hover:text-red-900 focus-ring px-2 py-1 rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    </td>
</tr>
