<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Daily Laundry') }}</title>

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap & Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- FullCalendar CSS -->
    @stack('styles')

    <!-- Fonts & Custom Notion Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .notion-box {
            border-radius: 14px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .notion-btn {
            border-radius: 8px;
            padding: 6px 16px;
            font-weight: 500;
        }

        /* Welcome Message Styles */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            margin: 1rem 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .welcome-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .welcome-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .text-admin {
            color: #e74c3c;
        }

        .text-kurir {
            color: #27ae60;
        }

        .text-kasir {
            color: #3498db;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
        @include('layouts.sidebar')
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            <!-- Welcome Message Section -->
            @auth
                <div class="welcome-container px-6 py-4">
                    <div class="welcome-banner">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if (auth()->user()->role === 'admin')
                                    <h1 class="welcome-title">Welcome Admin!</h1>
                                    <p class="welcome-subtitle">Selamat datang di Dashboard Admin Daily Laundry</p>
                                @elseif(auth()->user()->role === 'kurir')
                                    <h1 class="welcome-title">Welcome Kurir!</h1>
                                    <p class="welcome-subtitle">Selamat datang di Dashboard Kurir Daily Laundry</p>
                                @elseif(auth()->user()->role === 'kasir')
                                    <h1 class="welcome-title">Welcome Kasir!</h1>
                                    <p class="welcome-subtitle">Selamat datang di Dashboard Kasir Daily Laundry</p>
                                @else
                                    <h1 class="welcome-title">Welcome!</h1>
                                    <p class="welcome-subtitle">Selamat datang di Daily Laundry Management System</p>
                                @endif
                            </div>
                            <div class="text-end">
                                <small class="opacity-75">Logged in as:
                                    <span
                                        class="fw-bold
                                    @if (auth()->user()->role === 'admin') text-admin
                                    @elseif(auth()->user()->role === 'kurir') text-kurir
                                    @elseif(auth()->user()->role === 'kasir') text-kasir @endif">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </small>
                                <br>
                                <small class="opacity-75">{{ auth()->user()->name }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth

            <main class="flex-1 px-6 pb-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- FullCalendar & other page-specific scripts -->
    @stack('scripts')
</body>

</html>
