<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kontak') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sidebar collapsible styles */
        .sidebar-collapsed {
            width: 5rem !important; /* 80px */
        }

        .sidebar-collapsed .sidebar-text {
            display: none;
        }

        .sidebar-collapsed .sidebar-logo {
            display: none;
        }

        .sidebar-collapsed .user-initials {
            font-size: 0.75rem;
        }

        .sidebar-collapsed nav a {
            justify-content: center;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .sidebar-collapsed .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Smooth transitions */
        #main-content {
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 1024px) {
            #main-content {
                margin-left: 16rem; /* Default sidebar width */
            }
        }

        /* Mobile adjustments */
        @media (max-width: 1023px) {
            #main-content {
                margin-left: 0 !important;
            }

            .sidebar-collapsed {
                width: 16rem !important; /* Reset to full width on mobile */
            }

            .sidebar-collapsed .sidebar-text {
                display: block !important;
            }

            .sidebar-collapsed .sidebar-logo {
                display: block !important;
            }
        }
    </style>
</head>
<body class="font-inter antialiased bg-gray-50">
    <div class="flex h-screen">
        @auth
            @include('layouts.navigation')
        @endauth

        <!-- Main Content Area -->
        <div id="main-content" class="flex-1 flex flex-col overflow-hidden transition-all duration-300 ease-in-out {{ auth()->check() ? 'lg:ml-64' : '' }}">
            @guest
                <!-- Guest Header -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center h-16">
                            <div class="flex items-center">
                                <img src="{{ asset('logo/kontak_logo.png') }}" alt="Kontak" class="h-8 w-auto">
                                <span class="ml-3 text-xl font-bold text-gray-900">{{ config('app.name', 'Kontak') }}</span>
                            </div>
                            <div class="flex space-x-4">
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Register</a>
                            </div>
                        </div>
                    </div>
                </header>
            @endguest

            <!-- Flash Messages - Toast Notifications -->
            @if (session('success') || session('error'))
                <div class="fixed top-4 right-4 z-50 max-w-md w-full space-y-2">
                    @if (session('success'))
                        <div id="success-alert" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-0">
                            <div class="flex items-start justify-between">
                                <div class="flex">
                                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ session('success') }}</span>
                                </div>
                                <button type="button" onclick="dismissAlert('success-alert')" class="ml-4 text-green-600 hover:text-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-green-50 rounded-lg p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div id="error-alert" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-0">
                            <div class="flex items-start justify-between">
                                <div class="flex">
                                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ session('error') }}</span>
                                </div>
                                <button type="button" onclick="dismissAlert('error-alert')" class="ml-4 text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-red-50 rounded-lg p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>

        <!-- JavaScript for dismissible toast alerts -->
    <script>
        function dismissAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                // Slide out to the right and fade out
                alert.style.transform = 'translateX(100%)';
                alert.style.opacity = '0';

                // Remove element after animation completes
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = ['success-alert', 'error-alert'];

            // Add slide-in animation on load
            alerts.forEach(alertId => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    // Initial state: off-screen to the right
                    alert.style.transform = 'translateX(100%)';
                    alert.style.opacity = '0';

                    // Animate in after a brief delay
                    setTimeout(() => {
                        alert.style.transform = 'translateX(0)';
                        alert.style.opacity = '1';
                    }, 100);

                    // Auto-dismiss after 5 seconds
                    setTimeout(() => {
                        dismissAlert(alertId);
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>
