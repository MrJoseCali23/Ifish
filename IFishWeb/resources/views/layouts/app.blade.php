<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="iFish - Sistema profesional para la gestión de criaderos">
    <meta name="author" content="iFish Team">
    <title>@yield('title', 'iFish - Sistema Profesional')</title>
    <!-- Favicon para branding -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Bootstrap CSS y Icons desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts para tipografía moderna -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @yield('head')
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
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --dark-mode-bg: #1f2937;
            --dark-mode-text: #e5e7eb;
        }

        /* Tipografía global */
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Fondo dinámico CSS */
        .main-content {
            background: linear-gradient(135deg, var(--light-blue) 0%, #e2e8f0 100%);
            min-height: calc(100vh - 80px);
            position: relative;
            overflow-x: hidden;
            animation: gradientShift 20s ease infinite;
            background-size: 200% 200%;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Navbar mejorado */
        .navbar {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            box-shadow: 0 4px 20px rgba(208, 218, 252, 0.2);
            border-bottom: 2px solid var(--light-blue) !important;
            padding: 0.75rem 0;
            transition: all 0.3s ease;
        }

        .navbar .container-fluid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.3s ease;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .navbar-brand:hover {
            transform: translateX(-50%) scale(1.05);
        }

        .menu-toggle {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(175, 188, 233, 0.3);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.3s ease;
        }

        .menu-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 227, 247, 0.4);
            background: linear-gradient(135deg, var(--dark-blue), var(--primary-blue));
        }

        .login-btn, .logout-btn {
            background: linear-gradient(135deg, var(--success-green), #059669);
            border: none;
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--danger-red), #b91c1c);
        }

        .login-btn:hover, .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .logout-btn:hover {
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
            background: linear-gradient(135deg, #b91c1c, #991b1b);
        }

        .navbar-auth {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .criadero-info {
            background: linear-gradient(135deg, var(--light-blue), #ffffff);
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            text-align: right;
        }

        .criadero-info:hover {
            transform: translateY(-2px);
        }

        .criadero-info .text-primary {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .criadero-info .text-muted {
            font-size: 0.8rem;
        }

        .navbar-auth .text-end {
            font-size: 0.9rem;
        }

        .navbar-auth .text-end .fw-semibold {
            font-size: 0.95rem;
        }

        .navbar-auth .text-end .text-muted {
            font-size: 0.8rem;
        }

        /* Sidebar mejorado */
        .offcanvas {
            width: var(--sidebar-width) !important;
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--light-blue) 100%);
            border: none;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
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
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(8px);
        }

        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--accent-blue), var(--secondary-blue));
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .sidebar-nav .nav-icon {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .sidebar-nav .nav-link:hover .nav-icon {
            transform: scale(1.2);
        }

        .sidebar-divider {
            border: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
            margin: 1rem 0;
        }

        .sidebar-heading {
            color: var(--light-blue);
            padding: 10px 20px;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            opacity: 0.9;
        }

        /* Contenido principal */
        .content-wrapper {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            margin: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .content-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        }

        /* Animación de carga */
        .content-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.3s ease;
        }

        .content-wrapper.loading::before {
            left: 100%;
        }

        /* Desactivar animación cuando un modal está activo */
        .modal-open .content-wrapper::before {
            display: none;
        }

        /* Estilo para alertas de Bootstrap */
        .alert {
            border-radius: 12px;
            margin: 1rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .alert-success {
            background-color: var(--success-green);
            border-color: var(--success-green);
            color: white;
        }

        .alert-danger {
            background-color: var(--danger-red);
            border-color: var(--danger-red);
            color: white;
        }

        .alert .btn-close {
            filter: brightness(0) invert(1);
        }

        /* Responsividad para móviles */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.4rem;
                transform: translateX(-50%) scale(0.9);
            }
            .navbar-brand img {
                height: 35px;
            }
            .menu-toggle {
                padding: 6px 10px;
                font-size: 0.9rem;
            }
            .login-btn, .logout-btn {
                padding: 5px 12px;
                font-size: 0.85rem;
            }
            .offcanvas {
                width: 280px !important;
            }
            .content-wrapper {
                margin: 10px;
                padding: 1.5rem;
            }
            .sidebar-nav .nav-link {
                padding: 10px 15px;
                gap: 10px;
            }
            .sidebar-nav .nav-icon {
                font-size: 1.1rem;
            }
            .sidebar-heading {
                padding: 8px 15px;
                font-size: 0.8rem;
            }
            .navbar-auth {
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                padding: 0.5rem 0;
            }
            .navbar .container-fluid {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }
            .navbar-brand {
                font-size: 1.2rem;
                transform: translateX(-50%) scale(0.85);
                position: relative;
                left: auto;
                margin: 0.5rem auto;
            }
            .navbar-brand img {
                height: 30px;
            }
            .menu-toggle {
                align-self: flex-start;
            }
            .navbar-auth {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
                margin-top: 0.5rem;
            }
            .criadero-info {
                padding: 6px 10px;
                font-size: 0.85rem;
                text-align: center;
                width: 100%;
            }
            .criadero-info .text-primary {
                font-size: 0.9rem;
            }
            .criadero-info .text-muted {
                font-size: 0.75rem;
            }
            .navbar-auth .text-end {
                text-align: center;
                font-size: 0.85rem;
            }
            .navbar-auth .text-end .fw-semibold {
                font-size: 0.9rem;
            }
            .navbar-auth .text-end .text-muted {
                font-size: 0.75rem;
            }
            .login-btn, .logout-btn {
                width: 100%;
                padding: 6px 10px;
                font-size: 0.85rem;
                text-align: center;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light bg-light border-bottom" role="navigation" aria-label="Navegación principal">
        <div class="container-fluid">
            <!-- Botón del sidebar -->
            <button class="btn menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Abrir menú lateral">
                <i class="bi bi-list"></i>
            </button>
            <!-- Logo y nombre (centrado) -->
            <a href="{{ route('inicio') }}" class="navbar-brand mb-0 h1 d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('images/logo2.png') }}" alt="iFish Logo" style="height: 45px;" class="img-fluid">
                <span>iFish</span>
            </a>
            <!-- Información del usuario y criadero -->
            <div class="navbar-auth">
                @auth
                    @php
                        $activeCriadero = session('active_criadero_id') ? \App\Models\Criadero::find(session('active_criadero_id')) : null;
                    @endphp

                    @unless(Auth::user()->rol === 'admin' || Auth::user()->criaderos()->count() < 2)
                        @if($activeCriadero)
                            <div class="criadero-info">
                                <div class="fw-bold text-primary">{{ $activeCriadero->nombre }}</div>
                                <a href="{{ route('criaderos.select') }}" class="small text-muted text-decoration-none">Cambiar de Criadero</a>
                            </div>
                        @else
                            <div class="criadero-info">
                                <div class="fw-bold text-warning">No hay criadero seleccionado</div>
                                <a href="{{ route('criaderos.select') }}" class="small text-muted text-decoration-none">Seleccionar Criadero</a>
                            </div>
                        @endif
                    @endunless

                    <!-- Información del usuario -->
                    <div class="text-end">
                        <div class="fw-semibold text-dark">{{ Auth::user()->name }}</div>
                        <small class="text-muted">{{ Auth::user()->rol }}</small>
                    </div>

                    <!-- Botón de cerrar sesión -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn logout-btn" title="Cerrar Sesión" aria-label="Cerrar Sesión">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                @else
                    <!-- Botón de iniciar sesión -->
                    <a href="{{ route('login') }}" class="btn login-btn" aria-label="Iniciar Sesión">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarLabel"><i class="bi bi-compass me-2"></i>Navegación</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar menú lateral"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column sidebar-nav" role="navigation" aria-label="Menú lateral">
                @include('partials.navbar')
            </nav>
        </div>
    </div>


    <!-- Contenido principal -->
    <div class="main-content">
        <div class="container-fluid p-0">
            
            {{-- Alertas Globales de Nivel de Comida (para Dueños) --}}
            @if (Auth::check() && Auth::user()->rol === 'Dueño' && isset($dispensadoresCriticos) && $dispensadoresCriticos->isNotEmpty())
            <div class="container">
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Atención:</strong> Tienes {{ count($dispensadoresCriticos) }} dispensador(es) con nivel de comida bajo.
                        <a href="{{ route('dashboard') }}" class="alert-link ms-2">Ver detalles en el Dashboard.</a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <footer style="background-color: var(--primary-blue);" class="text-white pt-5 pb-4">
        <div class="container text-center text-md-left">
            <div class="row text-center text-md-left">
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-white">iFish</h5>
                    <p>Sistema profesional para la gestión inteligente de criaderos de peces, optimizando la alimentación y el monitoreo.</p>
                </div>

                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-white">Enlaces Útiles</h5>
                    <p>
                        <a href="{{ route('ayuda') }}" class="text-white text-decoration-none">Ayuda</a>
                    </p>
                </div>

                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-white">Contacto</h5>
                    <p>
                        <i class="bi bi-house-door-fill me-2"></i> Cochabamba, Bolivia
                    </p>
                    <p>
                        <i class="bi bi-envelope-fill me-2"></i> soporte@ifishbo.app
                    </p>
                    <p>
                        <i class="bi bi-telephone-fill me-2"></i> +591 67408921
                    </p>
                </div>
            </div>

            <hr class="mb-4">

            <div class="row align-items-center">
                <div class="col-md-7 col-lg-8">
                    <p class="text-center text-md-start">© {{ date('Y') }} iFish. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-5 col-lg-4">
                    <div class="text-center text-md-end">
                        <ul class="list-unstyled list-inline">
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-facebook"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-twitter"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-google"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-linkedin"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modales -->
    @stack('modals')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Cerrar sidebar al hacer clic en un enlace
        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const sidebar = bootstrap.Offcanvas.getInstance(document.getElementById('sidebar'));
                if (sidebar) sidebar.hide();
            });
        });

        // Animación de carga para contenido dinámico
        document.addEventListener('DOMContentLoaded', () => {
            const contentWrapper = document.querySelector('.content-wrapper');
            contentWrapper.classList.add('loading');
            setTimeout(() => {
                contentWrapper.classList.remove('loading');
            }, 300);

            // Inicializar tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Desactivar animación de carga al abrir un modal
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('show.bs.modal', () => {
                    contentWrapper.classList.remove('loading');
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>