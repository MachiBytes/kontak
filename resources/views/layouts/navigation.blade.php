<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-all duration-300 ease-in-out">
    <div class="flex flex-col h-full">
                <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 bg-gray-800">
            <div class="flex items-center">
                <img src="{{ asset('logo/kontak_logo.png') }}" alt="Kontak" class="h-8 w-auto sidebar-logo" onerror="this.style.display='none'">
                <span class="ml-3 text-xl font-bold text-white sidebar-text">{{ config('app.name', 'Kontak') }}</span>
            </div>
            <button id="sidebar-toggle" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto sidebar-scroll">
            <a href="{{ route('home') }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white group transition-colors duration-200 {{ request()->routeIs('home') ? 'bg-gray-800 text-white' : '' }}" title="Dashboard">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v1H8V5z"></path>
                </svg>
                <span class="ml-3 sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('settings.index', request()->query()) }}" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white group transition-colors duration-200 {{ request()->routeIs('settings.*') ? 'bg-gray-800 text-white' : '' }}" title="Settings">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="ml-3 sidebar-text">Settings</span>
            </a>

            <!-- Divider -->
            <div class="my-4 border-t border-gray-700 divider"></div>

            <!-- Contact Books Section -->
            <div class="mb-3 contact-books-section">
                <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider sidebar-text">Contact Books</h3>
            </div>

            <div id="contact-books-list" class="space-y-1">
                @if(isset($contactBooks))
                    @foreach($contactBooks as $index => $contactBook)
                        @php
                            $isOwner = $contactBook->owner_id === Auth::id();
                            $isPersonalBook = Auth::user()->isPersonalContactBook($contactBook);

                            // Determine display name
                            if ($isPersonalBook) {
                                $displayName = 'Personal';
                            } else {
                                $displayName = $contactBook->name;
                            }

                            // Determine if this is the current contact book
                            $isCurrent = false;
                            if (isset($currentContactBook)) {
                                $isCurrent = $contactBook->id === $currentContactBook->id;
                            }

                            // Determine the URL using the contact book's slug
                            if ($isPersonalBook) {
                                $url = route('home');
                            } else {
                                $url = route('home', ['dashboard' => $contactBook->slug]);
                            }
                        @endphp

                        <div
                            class="contact-book-item-wrapper {{ $isPersonalBook ? 'personal-book' : 'draggable-book' }}"
                            data-contact-book-id="{{ $contactBook->id }}"
                            {{ !$isPersonalBook ? 'draggable="true"' : '' }}
                        >
                            <a href="{{ $url }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg group transition-colors duration-200 contact-book-item {{ $isCurrent ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}" title="{{ $contactBook->name }}">
                                @if(!$isPersonalBook)
                                    <div class="drag-handle w-4 h-4 flex-shrink-0 mr-1 opacity-0 group-hover:opacity-50 transition-opacity cursor-grab">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
                                        </svg>
                                    </div>
                                @endif

                                <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center contact-book-dot">
                                    @if($isCurrent)
                                        <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                    @else
                                        <div class="w-2.5 h-2.5 bg-{{ $contactBook->color }}-400 rounded-full"></div>
                                    @endif
                                </div>
                                <span class="ml-3 sidebar-text">{{ $displayName }}</span>
                                @if($isCurrent)
                                    <svg class="w-4 h-4 ml-auto sidebar-text" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback to hardcoded values if no contact books data -->
                    <div class="contact-book-item-wrapper personal-book" data-contact-book-id="personal">
                        <!-- Personal Dashboard -->
                        <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg group transition-colors duration-200 contact-book-item {{ !request()->get('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}" title="Personal Dashboard">
                            <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center contact-book-dot">
                                @if(!request()->get('dashboard'))
                                    <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                @else
                                    <div class="w-2.5 h-2.5 bg-blue-400 rounded-full"></div>
                                @endif
                            </div>
                            <span class="ml-3 sidebar-text">Personal</span>
                            @if(!request()->get('dashboard'))
                                <svg class="w-4 h-4 ml-auto sidebar-text" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </a>
                    </div>

                    <!-- Other Contact Books (Fake Data) -->
                    <div class="contact-book-item-wrapper draggable-book" data-contact-book-id="2" draggable="true">
                        <a href="{{ route('home', ['dashboard' => 'sarah-marketing']) }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg group transition-colors duration-200 contact-book-item {{ request()->get('dashboard') == 'sarah-marketing' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}" title="Sarah's Marketing Team">
                            <div class="drag-handle w-4 h-4 flex-shrink-0 mr-1 opacity-0 group-hover:opacity-50 transition-opacity cursor-grab">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
                                </svg>
                            </div>
                            <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center contact-book-dot">
                                @if(request()->get('dashboard') == 'sarah-marketing')
                                    <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                @else
                                    <div class="w-2.5 h-2.5 bg-pink-400 rounded-full"></div>
                                @endif
                            </div>
                            <span class="ml-3 sidebar-text">Sarah's Marketing Team</span>
                            @if(request()->get('dashboard') == 'sarah-marketing')
                                <svg class="w-4 h-4 ml-auto sidebar-text" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </a>
                    </div>

                    <div class="contact-book-item-wrapper draggable-book" data-contact-book-id="3" draggable="true">
                        <a href="{{ route('home', ['dashboard' => 'acme-corp']) }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg group transition-colors duration-200 contact-book-item {{ request()->get('dashboard') == 'acme-corp' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}" title="ACME Corporation">
                            <div class="drag-handle w-4 h-4 flex-shrink-0 mr-1 opacity-0 group-hover:opacity-50 transition-opacity cursor-grab">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
                                </svg>
                            </div>
                            <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center contact-book-dot">
                                @if(request()->get('dashboard') == 'acme-corp')
                                    <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                @else
                                    <div class="w-2.5 h-2.5 bg-green-400 rounded-full"></div>
                                @endif
                            </div>
                            <span class="ml-3 sidebar-text">ACME Corporation</span>
                            @if(request()->get('dashboard') == 'acme-corp')
                                <svg class="w-4 h-4 ml-auto sidebar-text" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </a>
                    </div>

                    <div class="contact-book-item-wrapper draggable-book" data-contact-book-id="4" draggable="true">
                        <a href="{{ route('home', ['dashboard' => 'techstartup']) }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg group transition-colors duration-200 contact-book-item {{ request()->get('dashboard') == 'techstartup' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}" title="TechStartup Inc">
                            <div class="drag-handle w-4 h-4 flex-shrink-0 mr-1 opacity-0 group-hover:opacity-50 transition-opacity cursor-grab">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
                                </svg>
                            </div>
                            <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center contact-book-dot">
                                @if(request()->get('dashboard') == 'techstartup')
                                    <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                @else
                                    <div class="w-2.5 h-2.5 bg-purple-400 rounded-full"></div>
                                @endif
                            </div>
                            <span class="ml-3 sidebar-text">TechStartup Inc</span>
                            @if(request()->get('dashboard') == 'techstartup')
                                <svg class="w-4 h-4 ml-auto sidebar-text" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                    </a>
                </div>

                    <div class="contact-book-item-wrapper draggable-book" data-contact-book-id="5" draggable="true">
                        <a href="{{ route('home', ['dashboard' => 'freelance-clients']) }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg group transition-colors duration-200 contact-book-item {{ request()->get('dashboard') == 'freelance-clients' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}" title="Freelance Clients">
                            <div class="drag-handle w-4 h-4 flex-shrink-0 mr-1 opacity-0 group-hover:opacity-50 transition-opacity cursor-grab">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM17 14a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
                                </svg>
                            </div>
                            <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center contact-book-dot">
                                @if(request()->get('dashboard') == 'freelance-clients')
                                    <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                @else
                                    <div class="w-2.5 h-2.5 bg-orange-400 rounded-full"></div>
                                @endif
                            </div>
                            <span class="ml-3 sidebar-text">Freelance Clients</span>
                            @if(request()->get('dashboard') == 'freelance-clients')
                                <svg class="w-4 h-4 ml-auto sidebar-text" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </a>
                </div>
                @endif
            </div>
        </nav>

        <!-- User Profile Section -->
        <div class="px-4 py-4 border-t border-gray-800">
                        <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center avatar-gradient">
                        <span class="text-white font-semibold text-sm user-initials">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>
                    </div>
                </div>
                <div class="ml-3 flex-1 min-w-0 sidebar-text">
                    <p class="text-sm font-medium text-white truncate sidebar-user-name">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
                <div class="ml-3 sidebar-text">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition-colors duration-200 focus-ring" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                                </button>
                            </form>
                        </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="hidden fixed inset-0 z-40 lg:hidden" style="background-color: rgba(0, 0, 0, 0.3);"></div>

<!-- Mobile menu button (visible only on mobile when sidebar is hidden) -->
<div id="mobile-menu-btn" class="fixed top-4 left-4 z-50 lg:hidden">
    <button id="mobile-toggle" class="p-2 bg-gray-800 text-white rounded-lg shadow-lg hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileToggle = document.getElementById('mobile-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const mobileBtnContainer = document.getElementById('mobile-menu-btn');
    const mainContent = document.getElementById('main-content');

    // Get stored sidebar state
    let isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    let isMobile = window.innerWidth < 1024; // lg breakpoint

    function updateSidebarState() {
        isMobile = window.innerWidth < 1024;

        if (isMobile) {
            // Mobile behavior
            sidebar.classList.remove('sidebar-collapsed');
            if (isCollapsed) {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                mobileBtnContainer.classList.remove('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                mobileBtnContainer.classList.add('hidden');
            }
            // Reset main content margin on mobile
            if (mainContent) {
                mainContent.style.marginLeft = '0';
            }
        } else {
            // Desktop behavior
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
            mobileBtnContainer.classList.add('hidden');

            if (isCollapsed) {
                sidebar.classList.add('sidebar-collapsed');
                if (mainContent) {
                    mainContent.style.marginLeft = '5rem'; // 80px for collapsed sidebar
                }
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                if (mainContent) {
                    mainContent.style.marginLeft = '16rem'; // 256px for full sidebar
                }
            }
        }

        // Update toggle button icon
        updateToggleIcon();
    }

    function updateToggleIcon() {
        const icon = sidebarToggle.querySelector('svg');
        if (isCollapsed) {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>';
        } else {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>';
        }
    }

    function toggleSidebar() {
        isCollapsed = !isCollapsed;
        localStorage.setItem('sidebar-collapsed', isCollapsed);
        updateSidebarState();
    }

    // Event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);
    mobileToggle.addEventListener('click', toggleSidebar);

    // Close sidebar when clicking overlay on mobile
    sidebarOverlay.addEventListener('click', function() {
        if (isMobile) {
            isCollapsed = true;
            updateSidebarState();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        updateSidebarState();
    });

    // Initialize
    updateSidebarState();

    // Contact Book Drag and Drop Functionality
    const contactBooksList = document.getElementById('contact-books-list');
    let draggedElement = null;
    let draggedOverElement = null;

    if (contactBooksList) {
        // Add event listeners to draggable items
        function initializeDragAndDrop() {
            const draggableItems = contactBooksList.querySelectorAll('.draggable-book');

            draggableItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('dragenter', handleDragEnter);
                item.addEventListener('dragleave', handleDragLeave);
                item.addEventListener('drop', handleDrop);
                item.addEventListener('dragend', handleDragEnd);
            });
        }

        function handleDragStart(e) {
            draggedElement = this;
            this.style.opacity = '0.5';

            // Add dragging class for visual feedback
            this.classList.add('dragging');

            // Set drag effect
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }

            // Don't allow dropping on personal book
            if (this.classList.contains('personal-book')) {
                e.dataTransfer.dropEffect = 'none';
                return false;
            }

            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDragEnter(e) {
            if (this !== draggedElement && !this.classList.contains('personal-book')) {
                this.classList.add('drag-over');
            }
        }

        function handleDragLeave(e) {
            this.classList.remove('drag-over');
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            // Don't allow dropping on personal book
            if (this.classList.contains('personal-book')) {
                return false;
            }

            if (draggedElement !== this) {
                // Get the position to insert
                const rect = this.getBoundingClientRect();
                const midpoint = rect.top + rect.height / 2;
                const insertBefore = e.clientY < midpoint;

                if (insertBefore) {
                    contactBooksList.insertBefore(draggedElement, this);
                } else {
                    contactBooksList.insertBefore(draggedElement, this.nextSibling);
                }

                // Save the new order
                saveContactBookOrder();
            }

            return false;
        }

        function handleDragEnd(e) {
            // Reset visual states
            this.style.opacity = '1';
            this.classList.remove('dragging');

            // Remove drag-over class from all items
            const allItems = contactBooksList.querySelectorAll('.contact-book-item-wrapper');
            allItems.forEach(item => {
                item.classList.remove('drag-over');
            });

            draggedElement = null;
        }

        function saveContactBookOrder() {
            // Get the current order of contact books (excluding personal)
            const items = contactBooksList.querySelectorAll('.draggable-book');
            const contactBookIds = Array.from(items).map(item => {
                return parseInt(item.getAttribute('data-contact-book-id'));
            });

            // Send to server
            fetch('{{ route("contact-books.update-order") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    contact_book_ids: contactBookIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optional: Show a subtle notification
                    console.log('Contact book order saved successfully');
                } else {
                    console.error('Failed to save contact book order:', data.message);
                    // Optional: Revert the order or show error message
                }
            })
            .catch(error => {
                console.error('Error saving contact book order:', error);
                // Optional: Revert the order or show error message
            });
        }

        // Initialize drag and drop on page load
        initializeDragAndDrop();
    }
});
</script>
