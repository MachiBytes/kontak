@extends('layouts.app')

@section('content')
<div class="h-full bg-gray-50">
    @php
        $currentDashboard = request()->get('dashboard');
        $isOwner = $contactBook->owner_id === Auth::id();
    @endphp

    <!-- Settings Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                <p class="text-sm text-gray-600 mt-1">
                    @if($currentDashboard)
                        Settings for this contact book
                    @else
                        Manage your personal settings and contact book preferences
                    @endif
                </p>
            </div>
            @if($currentDashboard)
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="w-3 h-3 bg-{{ $contactBook->color }}-400 rounded-full"></div>
                    <span>{{ $contactBook->name }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="flex-1 p-6">
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Personal Settings Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Personal</h2>
                    <p class="text-sm text-gray-600 mt-1">Your personal account information and security settings</p>
                </div>

                <div class="p-6 space-y-8">
                    <!-- Profile Information -->
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-4">Profile Information</h3>
                        <form id="profile-form">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>

                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea id="address" name="address" rows="3"
                                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">{{ old('address', $user->address) }}</textarea>
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus-ring text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="button-text">Update Profile</span>
                                    <svg class="loading-spinner w-4 h-4 ml-2 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Change Password -->
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-4">Change Password</h3>
                        <form id="password-form">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" id="current_password" name="current_password"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" id="password" name="password"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus-ring text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="button-text">Update Password</span>
                                    <svg class="loading-spinner w-4 h-4 ml-2 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Book Settings Card -->
            @if($isOwner)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Contact Book</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Settings for this contact book
                    </p>
                </div>

                <div class="p-6 space-y-8">
                    <!-- Contact Book Information -->
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-4">Contact Book Information</h3>
                        <form id="contact-book-form">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Contact Book Name</label>
                                    <input type="text" id="book_name" name="name" value="{{ old('name', $contactBook->name) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                </div>

                                <div>
                                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color Theme</label>
                                    <select id="book_color" name="color" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                        <option value="blue" {{ $contactBook->color == 'blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="green" {{ $contactBook->color == 'green' ? 'selected' : '' }}>Green</option>
                                        <option value="purple" {{ $contactBook->color == 'purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="pink" {{ $contactBook->color == 'pink' ? 'selected' : '' }}>Pink</option>
                                        <option value="orange" {{ $contactBook->color == 'orange' ? 'selected' : '' }}>Orange</option>
                                        <option value="red" {{ $contactBook->color == 'red' ? 'selected' : '' }}>Red</option>
                                        <option value="yellow" {{ $contactBook->color == 'yellow' ? 'selected' : '' }}>Yellow</option>
                                        <option value="indigo" {{ $contactBook->color == 'indigo' ? 'selected' : '' }}>Indigo</option>
                                    </select>
                                    <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                    <p class="mt-1 text-xs text-gray-500">This color will be used for the contact book indicator in the sidebar</p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus-ring text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="button-text">Update Contact Book</span>
                                    <svg class="loading-spinner w-4 h-4 ml-2 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- User Access Management -->
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-4">User Access Management</h3>
                        <p class="text-sm text-gray-600 mb-4">Control who can access this contact book and their permissions</p>

                        <!-- Add User Form -->
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Add New User</h4>
                            <form id="add-user-form">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="access_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <input type="email" id="access_email" name="email"
                                               placeholder="user@example.com"
                                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                        <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                    </div>

                                    <div>
                                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                        <select id="role" name="role" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm">
                                            <option value="audience">Audience (View Only)</option>
                                            <option value="admin">Admin (Full Access)</option>
                                        </select>
                                        <div class="error-message text-sm text-red-600 mt-1 hidden"></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus-ring text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span class="button-text">
                                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add User
                                        </span>
                                        <svg class="loading-spinner w-4 h-4 ml-2 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Bulk Actions -->
                        <div id="bulk-actions" class="hidden px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-blue-900">
                                    <span id="selected-count">0</span> user(s) selected
                                </span>
                                <button id="bulk-remove-btn" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 focus-ring">
                                    Remove Selected
                                </button>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invited</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Accessed</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="user-access-table" class="bg-white divide-y divide-gray-200">
                                    @forelse($userAccess as $access)
                                        @include('settings.partials.user-access-row', ['access' => $access])
                                    @empty
                                        <tr id="no-users-row">
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                    </svg>
                                                    <p class="text-lg font-medium text-gray-900">No users added yet</p>
                                                    <p class="text-sm text-gray-500 mt-1">Add users above to share access to this contact book</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Read-only view for non-owners -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Contact Book</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        You have {{ $userAccess->first()?->role ?? 'view' }} access to {{ $contactBook->name }}
                    </p>
                </div>

                <div class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Shared Contact Book</h3>
                                <p class="text-sm text-blue-700 mt-1">
                                    This contact book is owned by {{ $contactBook->owner->name }}.
                                    Contact them to request changes to your access level or the contact book settings.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Settings JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Utility functions
    function showToast(message, type = 'success') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 max-w-md w-full transform translate-x-0 transition-all duration-300 ${
            type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'
        } border px-4 py-3 rounded-lg shadow-lg`;

        toast.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success'
                            ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                            : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
        }
                    </svg>
                    <span class="text-sm font-medium">${message}</span>
                </div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-4 ${
                    type === 'success' ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'
                } focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Slide in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    function showFieldErrors(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('border-red-500');
        });

        // Show new errors
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            const errorDiv = input?.parentElement.querySelector('.error-message');
            if (input && errorDiv) {
                input.classList.add('border-red-500');
                errorDiv.textContent = errors[field][0];
                errorDiv.classList.remove('hidden');
            }
        });
    }

    function setLoading(button, loading) {
        const spinner = button.querySelector('.loading-spinner');
        const text = button.querySelector('.button-text');

        if (loading) {
            button.disabled = true;
            spinner?.classList.remove('hidden');
        } else {
            button.disabled = false;
            spinner?.classList.add('hidden');
        }
    }

    // Profile Form
    document.getElementById('profile-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');

        setLoading(submitBtn, true);

        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route("settings.update-profile") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);
                // Update sidebar user info if needed
                const sidebarName = document.querySelector('.sidebar-user-name');
                if (sidebarName) sidebarName.textContent = data.user.name;
            } else {
                if (data.errors) {
                    showFieldErrors(form, data.errors);
                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        } finally {
            setLoading(submitBtn, false);
        }
    });

    // Password Form
    document.getElementById('password-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');

        setLoading(submitBtn, true);

        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route("settings.update-password") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);
                form.reset();
            } else {
                if (data.errors) {
                    showFieldErrors(form, data.errors);
                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        } finally {
            setLoading(submitBtn, false);
        }
    });

    // Contact Book Form
    @if($isOwner)
    document.getElementById('contact-book-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');

        setLoading(submitBtn, true);

        try {
            const formData = new FormData(form);
            const queryParams = new URLSearchParams(window.location.search);

            const response = await fetch('{{ route("settings.update-contact-book") }}?' + queryParams.toString(), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);

                // Update the page elements with new contact book data
                const updatedContactBook = data.contactBook;
                updateContactBookDisplay(updatedContactBook);

            } else {
                if (data.errors) {
                    showFieldErrors(form, data.errors);
                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        } finally {
            setLoading(submitBtn, false);
        }
    });

        // Function to update contact book display across the page
    function updateContactBookDisplay(contactBook) {
        // Update settings page header subtitle
        const headerSubtitle = document.querySelector('.bg-white.border-b .text-sm.text-gray-600');
        if (headerSubtitle && headerSubtitle.textContent.includes('Settings for')) {
            headerSubtitle.classList.add('update-flash');
            headerSubtitle.textContent = `Settings for this contact book`;
            setTimeout(() => headerSubtitle.classList.remove('update-flash'), 600);
        }

        // Update top-right contact book indicator
        const topRightIndicator = document.querySelector('.bg-white.border-b .flex.items-center.space-x-2');
        if (topRightIndicator) {
            topRightIndicator.classList.add('update-flash');

            const colorDot = topRightIndicator.querySelector('.rounded-full');
            const nameSpan = topRightIndicator.querySelector('span');

            if (colorDot) {
                // Remove old color classes and add new one
                colorDot.className = colorDot.className.replace(/bg-\w+-400/g, '');
                colorDot.classList.add(`bg-${contactBook.color}-400`);
            }

            if (nameSpan) {
                nameSpan.textContent = contactBook.name;
            }

            setTimeout(() => topRightIndicator.classList.remove('update-flash'), 600);
        }

        // Update contact book section header
        const contactBookSectionTitle = document.querySelector('.bg-white.rounded-lg .px-6.py-4 .text-sm.text-gray-600');
        if (contactBookSectionTitle && contactBookSectionTitle.textContent.includes('Settings for')) {
            contactBookSectionTitle.classList.add('update-flash');
            contactBookSectionTitle.textContent = `Settings for this contact book`;
            setTimeout(() => contactBookSectionTitle.classList.remove('update-flash'), 600);
        }

        // Update sidebar contact book name and color
        updateSidebarContactBook(contactBook);
    }

        // Function to update sidebar contact book
    function updateSidebarContactBook(contactBook) {
        // Find the current contact book in sidebar
        const currentContactBookId = {{ $contactBook->id }};
        const sidebarItem = document.querySelector(`[data-contact-book-id="${currentContactBookId}"]`);

        if (sidebarItem) {
            // Add visual feedback
            sidebarItem.classList.add('update-flash');

            // Update contact book name in sidebar (but not for personal books)
            const nameSpan = sidebarItem.querySelector('.sidebar-text');
            const isPersonalBook = sidebarItem.classList.contains('personal-book');

            if (nameSpan && !isPersonalBook) {
                nameSpan.classList.add('updating');
                setTimeout(() => {
                    nameSpan.textContent = contactBook.name;
                    nameSpan.classList.remove('updating');
                }, 150);
            }

            // Update color dot in sidebar
            const colorDot = sidebarItem.querySelector('.contact-book-dot .rounded-full:not(.bg-white)');
            if (colorDot) {
                // Remove old color classes and add new one
                colorDot.className = colorDot.className.replace(/bg-\w+-400/g, '');
                colorDot.classList.add(`bg-${contactBook.color}-400`);
            }

            // Update tooltip with full name
            const link = sidebarItem.querySelector('a');
            if (link) {
                link.setAttribute('title', contactBook.name);
            }

            // Remove flash effect
            setTimeout(() => sidebarItem.classList.remove('update-flash'), 600);
        }
    }
    @endif

    // Add User Form
    @if($isOwner)
    document.getElementById('add-user-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');

        setLoading(submitBtn, true);

        try {
            const formData = new FormData(form);
            const queryParams = new URLSearchParams(window.location.search);

            const response = await fetch('{{ route("settings.add-user-access") }}?' + queryParams.toString(), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);
                form.reset();

                // Remove "no users" row if it exists
                const noUsersRow = document.getElementById('no-users-row');
                if (noUsersRow) noUsersRow.remove();

                // Add new user row
                const tableBody = document.getElementById('user-access-table');
                const newRow = createUserRow(data.userAccess);
                tableBody.insertAdjacentHTML('afterbegin', newRow);

            } else {
                if (data.errors) {
                    showFieldErrors(form, data.errors);
                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        } finally {
            setLoading(submitBtn, false);
        }
    });
    @endif

    function createUserRow(userAccess) {
        const userName = userAccess.user ? userAccess.user.name : 'Invited User';
        const initials = userAccess.email.substring(0, 2).toUpperCase();
        const invitedDate = new Date(userAccess.invited_at).toLocaleDateString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric'
        });
        const invitedTime = new Date(userAccess.invited_at).toLocaleTimeString('en-US', {
            hour: 'numeric', minute: '2-digit', hour12: true
        });

        let lastAccessed = '<span class="text-sm text-gray-400">Never</span>';
        if (userAccess.last_accessed_at) {
            const lastDate = new Date(userAccess.last_accessed_at).toLocaleDateString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric'
            });
            const lastTime = new Date(userAccess.last_accessed_at).toLocaleTimeString('en-US', {
                hour: 'numeric', minute: '2-digit', hour12: true
            });
            lastAccessed = `<div class="text-sm text-gray-900">${lastDate}</div><div class="text-xs text-gray-500">${lastTime}</div>`;
        }

        return `
            <tr class="hover:bg-gray-50" data-user-id="${userAccess.id}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" class="user-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="${userAccess.id}">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">${initials}</span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${userName}</div>
                            <div class="text-sm text-gray-500">${userAccess.email}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <select name="role" onchange="updateUserRole(${userAccess.id}, this.value)"
                            class="text-sm border border-gray-300 rounded px-2 py-1 focus-ring">
                        <option value="audience" ${userAccess.role === 'audience' ? 'selected' : ''}>Audience</option>
                        <option value="admin" ${userAccess.role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="text-sm text-gray-900">${invitedDate}</div>
                    <div class="text-xs text-gray-500">${invitedTime}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${lastAccessed}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button onclick="removeUser(${userAccess.id})"
                            class="text-red-600 hover:text-red-900 focus-ring px-2 py-1 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    }

    // Bulk selection
    const selectAllCheckbox = document.getElementById('select-all');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkRemoveBtn = document.getElementById('bulk-remove-btn');

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkboxes.length;

        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkActions.classList.remove('hidden');
        } else {
            bulkActions.classList.add('hidden');
        }

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.user-checkbox');
        selectAllCheckbox.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
    }

    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateBulkActions();
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('user-checkbox')) {
            updateBulkActions();
        }
    });

    bulkRemoveBtn.addEventListener('click', async function() {
        const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked'))
            .map(checkbox => checkbox.value);

        if (selectedIds.length === 0) return;

        if (!confirm(`Are you sure you want to remove ${selectedIds.length} user(s)?`)) return;

        try {
            const queryParams = new URLSearchParams(window.location.search);

            const response = await fetch('{{ route("settings.bulk-remove-user-access") }}?' + queryParams.toString(), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ user_access_ids: selectedIds })
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);

                // Remove rows
                selectedIds.forEach(id => {
                    const row = document.querySelector(`tr[data-user-id="${id}"]`);
                    if (row) row.remove();
                });

                // Check if table is empty
                const remainingRows = document.querySelectorAll('#user-access-table tr:not(#no-users-row)');
                if (remainingRows.length === 0) {
                    document.getElementById('user-access-table').innerHTML = `
                        <tr id="no-users-row">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 715 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900">No users added yet</p>
                                    <p class="text-sm text-gray-500 mt-1">Add users above to share access to this contact book</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                updateBulkActions();
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        }
    });

    // Global functions for inline handlers
    window.updateUserRole = async function(userId, role) {
        try {
            const queryParams = new URLSearchParams(window.location.search);

            const response = await fetch(`{{ route('settings.update-user-access', ':id') }}`.replace(':id', userId) + '?' + queryParams.toString(), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ role })
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);
            } else {
                showToast(data.message || 'An error occurred', 'error');
                // Reset select to previous value
                location.reload();
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
            location.reload();
        }
    };

    window.removeUser = async function(userId) {
        if (!confirm('Are you sure you want to remove this user\'s access?')) return;

        try {
            const queryParams = new URLSearchParams(window.location.search);

            const response = await fetch(`{{ route('settings.remove-user-access', ':id') }}`.replace(':id', userId) + '?' + queryParams.toString(), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);

                // Remove row
                const row = document.querySelector(`tr[data-user-id="${userId}"]`);
                if (row) row.remove();

                // Check if table is empty
                const remainingRows = document.querySelectorAll('#user-access-table tr:not(#no-users-row)');
                if (remainingRows.length === 0) {
                    document.getElementById('user-access-table').innerHTML = `
                        <tr id="no-users-row">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 715 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900">No users added yet</p>
                                    <p class="text-sm text-gray-500 mt-1">Add users above to share access to this contact book</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                updateBulkActions();
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showToast('Network error occurred', 'error');
        }
    };
});
</script>
@endsection
