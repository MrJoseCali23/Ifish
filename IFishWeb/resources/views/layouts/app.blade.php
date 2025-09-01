<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'IFish - Sistema Profesional de Estadísticas Pesqueras')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @yield('head') {{-- Sección extra para head extendido --}}
    
    <style>
        :root {
            --primary-blue: #1e40af;
            --secondary-blue: #3b82f6;
            --accent-blue: #60a5fa;
            --dark-blue: #1e3a8a;
            --light-blue: #dbeafe;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --danger-red: #ef4444;
            --sidebar-width: 320px;
        }

        /* Aquí van todos tus estilos personalizados (navbar, login-btn, sidebar, etc.) */
        /* (Los dejo igual que en tu mensaje anterior, ya corregidos) */

        .navbar {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            box-shadow: 0 4px 20px rgba(208, 218, 252, 0.1);
            border-bottom: 2px solid var(--light-blue) !important;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .menu-toggle {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border: none;
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(175, 188, 233, 0.3);
        }

        .menu-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 227, 247, 0.4);
            background: linear-gradient(135deg, var(--dark-blue), var(--primary-blue));
        }

        .login-btn {
            background: linear-gradient(135deg, var(--success-green), #059669);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669, #047857);
        }

        .offcanvas {
            width: var(--sidebar-width) !important;
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--light-blue) 100%);
            border: none;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.2);
        }

        .offcanvas-header {
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .offcanvas-title {
            color: white;
            font-weight: 700;
            font-size: 1.4rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: 500;
            transition: 0.3s;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(8px);
        }

        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--accent-blue), var(--secondary-blue));
            color: white;
            font-weight: 600;
        }

        .main-content {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: calc(100vh - 80px);
        }

        .content-wrapper {
            background: white;
            margin: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-light bg-light border-bottom">
        <div class="container-fluid">
            <button class="btn menu-toggle me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand mb-0 h1">
                    <a href="{{ url('/') }}" class="navbar-brand mb-0 h1 d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('images/logo2.png') }}" alt="iFish Logo" style="height: 45px;" class="img-fluid">
                     <span style="font-weight: 800; font-size: 1.8rem; background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    iFish
            </span>
</a>
            </span>

            {{-- Botón dinámico según login --}}
            @auth
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-semibold text-dark">
                        {{-- Si el usuario tiene un criadero asignado, muestra su nombre --}}
                        @if(Auth::user()->criadero)
                            <i class="bi bi-building text-muted"></i>
                            <strong>{{ Auth::user()->criadero->nombre }}</strong>
                            <span class="text-muted mx-2">|</span>
                        @endif

                        {{-- Muestra el nombre y rol del usuario --}}
                        <i class="bi bi-person-circle text-muted"></i>
                        {{ Auth::user()->name }} ({{ Auth::user()->rol }})
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
            @else
                {{-- El botón de Iniciar Sesión para visitantes --}}
                <a href="{{ route('login') }}" class="btn login-btn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                </a>
            @endauth
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="bi bi-compass me-2"></i>Navegación</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column sidebar-nav">
                @include('partials.navbar')
            </nav>
        </div>
    </div>

    <!-- Contenido -->
    <div class="main-content">
        <div class="container-fluid p-0">
            <div class="content-wrapper glass-effect">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

