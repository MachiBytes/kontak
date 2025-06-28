@extends('layouts.app')

@section('content')
<div class="h-full bg-gray-50">
    <!-- Dashboard Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-600 mt-1">Manage your contacts and business relationships</p>
            </div>
            @if($canEdit)
            <button onclick="openContactModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus-ring text-sm font-medium">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Contact
            </button>
            @endif
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <form method="GET" action="{{ route('home') }}" class="w-full">
                @if(request('dashboard'))
                    <input type="hidden" name="dashboard" value="{{ request('dashboard') }}">
                @endif
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search contacts..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus-ring sm:text-sm" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Contacts Grid -->
    <div class="flex-1 p-6">
        @if($groupedContacts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-6">
                @foreach($groupedContacts as $letter => $contacts)
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">{{ $letter }}</h3>

                        @foreach($contacts as $contact)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md contact-card cursor-pointer focus-ring" onclick="viewContact({{ $contact->id }})">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-{{ ['blue', 'green', 'purple', 'pink', 'indigo', 'red', 'yellow', 'orange', 'teal', 'cyan'][abs(crc32($contact->name)) % 10] }}-600 rounded-full flex items-center justify-center avatar-gradient">
                                        <span class="text-white font-semibold text-lg">{{ $contact->initials }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $contact->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $contact->primary_email ?: $contact->primary_phone ?: 'No contact info' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No contacts found</h3>
                <p class="text-gray-500 mb-6">
                    @if($search)
                        No contacts match your search criteria.
                    @else
                        Get started by adding your first contact.
                    @endif
                </p>
                @if($canEdit)
                    <button onclick="openContactModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus-ring text-sm font-medium">
                        Add Contact
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Contact Modal -->
<div id="contactModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.3);">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 id="modalTitle" class="text-xl font-semibold text-gray-900">Contact Details</h2>
            <button onclick="closeContactModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="modalContent" class="p-6">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
let currentContactId = null;
let isEditMode = false;

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'border-green-500' : 'border-red-500'
    }`;

    toast.innerHTML = `
        <div class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success'
                        ? '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                        : '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                    }
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;

    document.getElementById('toast-container').appendChild(toast);

    // Slide in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// Open contact modal
function openContactModal(contactId = null) {
    currentContactId = contactId;
    isEditMode = false;

    const modal = document.getElementById('contactModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');

    if (contactId) {
        modalTitle.textContent = 'Contact Details';
        loadContactView(contactId);
    } else {
        modalTitle.textContent = 'Add New Contact';
        loadContactForm();
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Close contact modal
function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentContactId = null;
    isEditMode = false;
}

// View contact details
function viewContact(contactId) {
    openContactModal(contactId);
}

// Load contact view
async function loadContactView(contactId) {
    try {
        const response = await fetch(`/contacts/${contactId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            const contact = data.contact;
            const canEdit = data.canEdit;

            // Build emails HTML
            let emailsHtml = '';
            if (contact.emails && contact.emails.length > 0) {
                contact.emails.forEach(email => {
                    emailsHtml += `
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${email.type.charAt(0).toUpperCase() + email.type.slice(1)} Email</p>
                                    <p class="text-sm text-gray-600">${email.address}</p>
                                </div>
                            </div>
                            <button onclick="copyToClipboard('${email.address}')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                });
            }

            // Build phones HTML
            let phonesHtml = '';
            if (contact.phones && contact.phones.length > 0) {
                contact.phones.forEach(phone => {
                    phonesHtml += `
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${phone.type.charAt(0).toUpperCase() + phone.type.slice(1)} Phone</p>
                                    <p class="text-sm text-gray-600">${phone.number}</p>
                                </div>
                            </div>
                            <button onclick="copyToClipboard('${phone.number}')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                });
            }

            // Build websites HTML
            let websitesHtml = '';
            if (contact.websites && contact.websites.length > 0) {
                contact.websites.forEach(website => {
                    websitesHtml += `
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${website.type.charAt(0).toUpperCase() + website.type.slice(1)} Website</p>
                                    <p class="text-sm text-gray-600">${website.url}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="${website.url}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                <button onclick="copyToClipboard('${website.url}')" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;
                });
            }

            document.getElementById('modalContent').innerHTML = `
                <div class="space-y-6">
                    <!-- Contact Header -->
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-xl">${contact.initials}</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900">${contact.name}</h3>
                        </div>
                        ${canEdit ? `
                        <div class="flex space-x-2">
                            <button onclick="editContact()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                            <button onclick="deleteContact()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Contact Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${emailsHtml}
                        ${phonesHtml}
                        ${websitesHtml}

                        ${contact.address ? `
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg md:col-span-2">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Address</p>
                                    <p class="text-sm text-gray-600">${contact.address}</p>
                                </div>
                            </div>
                            <button onclick="copyToClipboard('${contact.address}')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                        ` : ''}
                    </div>

                    ${contact.notes ? `
                    <div class="border-t pt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Notes</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">${contact.notes}</p>
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
        } else {
            showToast(data.message || 'Failed to load contact details', 'error');
        }
    } catch (error) {
        showToast('An error occurred while loading contact details', 'error');
    }
}

// Load contact form
function loadContactForm(contact = null) {
    isEditMode = !!contact;

    let phonesHtml = '';
    let emailsHtml = '';
    let websitesHtml = '';

    // Load existing data if editing
    if (contact) {
        if (contact.phones && contact.phones.length > 0) {
            contact.phones.forEach((phone, index) => {
                phonesHtml += createPhoneInput(index, phone.type, phone.number);
            });
        }
        if (contact.emails && contact.emails.length > 0) {
            contact.emails.forEach((email, index) => {
                emailsHtml += createEmailInput(index, email.type, email.address);
            });
        }
        if (contact.websites && contact.websites.length > 0) {
            contact.websites.forEach((website, index) => {
                websitesHtml += createWebsiteInput(index, website.type, website.url);
            });
        }
    }

    // Add one empty input for each if none exist
    if (!phonesHtml) phonesHtml = createPhoneInput(0);
    if (!emailsHtml) emailsHtml = createEmailInput(0);
    if (!websitesHtml) websitesHtml = createWebsiteInput(0);

    document.getElementById('modalContent').innerHTML = `
        <form id="contactForm" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                <input type="text" id="name" name="name" required value="${contact ? contact.name : ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Emails Section -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">Email Addresses</label>
                    <button type="button" onclick="addEmailInput()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">+ Add Email</button>
                </div>
                <div id="emails-container" class="space-y-3">
                    ${emailsHtml}
                </div>
            </div>

            <!-- Phones Section -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">Phone Numbers</label>
                    <button type="button" onclick="addPhoneInput()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">+ Add Phone</button>
                </div>
                <div id="phones-container" class="space-y-3">
                    ${phonesHtml}
                </div>
            </div>

            <!-- Websites Section -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">Websites</label>
                    <button type="button" onclick="addWebsiteInput()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">+ Add Website</button>
                </div>
                <div id="websites-container" class="space-y-3">
                    ${websitesHtml}
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea id="address" name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">${contact ? (contact.address || '') : ''}</textarea>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">${contact ? (contact.notes || '') : ''}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <button type="button" onclick="closeContactModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    ${isEditMode ? 'Update Contact' : 'Create Contact'}
                </button>
            </div>
        </form>
    `;

    // Add form submit handler
    document.getElementById('contactForm').addEventListener('submit', handleContactFormSubmit);
}

// Create email input HTML
function createEmailInput(index, type = '', address = '') {
    return `
        <div class="flex space-x-3">
            <select name="emails[${index}][type]" class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="personal" ${type === 'personal' ? 'selected' : ''}>Personal</option>
                <option value="work" ${type === 'work' ? 'selected' : ''}>Work</option>
                <option value="other" ${type === 'other' ? 'selected' : ''}>Other</option>
            </select>
            <input type="email" name="emails[${index}][address]" value="${address}" placeholder="Email address" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <button type="button" onclick="removeInput(this)" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
}

// Create phone input HTML
function createPhoneInput(index, type = '', number = '') {
    return `
        <div class="flex space-x-3">
            <select name="phones[${index}][type]" class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="mobile" ${type === 'mobile' ? 'selected' : ''}>Mobile</option>
                <option value="home" ${type === 'home' ? 'selected' : ''}>Home</option>
                <option value="work" ${type === 'work' ? 'selected' : ''}>Work</option>
                <option value="other" ${type === 'other' ? 'selected' : ''}>Other</option>
            </select>
            <input type="tel" name="phones[${index}][number]" value="${number}" placeholder="Phone number" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <button type="button" onclick="removeInput(this)" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
}

// Create website input HTML
function createWebsiteInput(index, type = '', url = '') {
    return `
        <div class="flex space-x-3">
            <select name="websites[${index}][type]" class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="personal" ${type === 'personal' ? 'selected' : ''}>Personal</option>
                <option value="work" ${type === 'work' ? 'selected' : ''}>Work</option>
                <option value="portfolio" ${type === 'portfolio' ? 'selected' : ''}>Portfolio</option>
                <option value="social" ${type === 'social' ? 'selected' : ''}>Social</option>
                <option value="other" ${type === 'other' ? 'selected' : ''}>Other</option>
            </select>
            <input type="url" name="websites[${index}][url]" value="${url}" placeholder="Website URL" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <button type="button" onclick="removeInput(this)" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
}

// Add new email input
function addEmailInput() {
    const container = document.getElementById('emails-container');
    const index = container.children.length;
    const div = document.createElement('div');
    div.innerHTML = createEmailInput(index);
    container.appendChild(div.firstElementChild);
}

// Add new phone input
function addPhoneInput() {
    const container = document.getElementById('phones-container');
    const index = container.children.length;
    const div = document.createElement('div');
    div.innerHTML = createPhoneInput(index);
    container.appendChild(div.firstElementChild);
}

// Add new website input
function addWebsiteInput() {
    const container = document.getElementById('websites-container');
    const index = container.children.length;
    const div = document.createElement('div');
    div.innerHTML = createWebsiteInput(index);
    container.appendChild(div.firstElementChild);
}

// Remove input
function removeInput(button) {
    const container = button.closest('.space-y-3');
    if (container.children.length > 1) {
        button.parentElement.remove();
        // Re-index remaining inputs
        reindexInputs(container);
    }
}

// Re-index inputs after removal
function reindexInputs(container) {
    Array.from(container.children).forEach((child, index) => {
        const inputs = child.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.name;
            if (name.includes('[')) {
                const baseName = name.substring(0, name.indexOf('['));
                const fieldName = name.substring(name.lastIndexOf('[') + 1, name.length - 1);
                input.name = `${baseName}[${index}][${fieldName}]`;
            }
        });
    });
}

// Edit contact
function editContact() {
    document.getElementById('modalTitle').textContent = 'Edit Contact';

    // Get current contact data from the view
    fetch(`/contacts/${currentContactId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadContactForm(data.contact);
        }
    });
}

// Handle contact form submission
async function handleContactFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = {};

    // Process form data
    for (let [key, value] of formData.entries()) {
        if (key.includes('[')) {
            // Handle array fields
            const match = key.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const [, fieldName, index, subField] = match;
                if (!data[fieldName]) data[fieldName] = [];
                if (!data[fieldName][index]) data[fieldName][index] = {};
                data[fieldName][index][subField] = value;
            }
        } else {
            data[key] = value;
        }
    }

    // Clean up arrays (remove empty entries)
    ['phones', 'emails', 'websites'].forEach(field => {
        if (data[field]) {
            data[field] = data[field].filter(item => {
                if (field === 'phones') return item.number && item.number.trim();
                if (field === 'emails') return item.address && item.address.trim();
                if (field === 'websites') return item.url && item.url.trim();
                return false;
            });
            if (data[field].length === 0) data[field] = null;
        }
    });

    // Add dashboard parameter if it exists
    const dashboardParam = new URLSearchParams(window.location.search).get('dashboard');
    if (dashboardParam) {
        data.dashboard = dashboardParam;
    }

    try {
        const url = isEditMode ? `/contacts/${currentContactId}` : '/contacts';
        const method = isEditMode ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message);
            closeContactModal();
            // Reload the page to show updated contacts
            window.location.reload();
        } else {
            if (result.errors) {
                // Show validation errors
                for (const [field, messages] of Object.entries(result.errors)) {
                    showToast(messages[0], 'error');
                }
            } else {
                showToast(result.message || 'An error occurred', 'error');
            }
        }
    } catch (error) {
        showToast('An error occurred while saving the contact', 'error');
    }
}

// Delete contact
async function deleteContact() {
    if (!confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/contacts/${currentContactId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message);
            closeContactModal();
            // Reload the page to show updated contacts
            window.location.reload();
        } else {
            showToast(result.message || 'An error occurred', 'error');
        }
    } catch (error) {
        showToast('An error occurred while deleting the contact', 'error');
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!');
    }).catch(() => {
        showToast('Failed to copy to clipboard', 'error');
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeContactModal();
    }
});
</script>
@endsection
