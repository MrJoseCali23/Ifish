<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'IFish - Sistema Profesional de Estadísticas Pesqueras')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
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

        /* Navbar profesional */
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

        /* Botón hamburguesa mejorado */
        .menu-toggle {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border: none;
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 1.2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(175, 188, 233, 0.3);
        }

        .menu-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 227, 247, 0.4);
            background: linear-gradient(135deg, var(--dark-blue), var(--primary-blue));
        }

        .menu-toggle:active {
            transform: translateY(0);
        }

        /* Botón de login profesional */
        .login-btn {
            background: linear-gradient(135deg, var(--success-green), #059669);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }

        /* Sidebar ultra profesional */
        .offcanvas {
            width: var(--sidebar-width) !important;
            background: linear-gradient(200deg, var(--primary-blue) 0%, var(--light-blue) 100%);
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
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .btn-close {
            filter: invert(1);
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .btn-close:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .offcanvas-body {
            padding: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.1) 100%);
        }

        /* Navegación del sidebar */
        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 15px 25px;
            margin: 4px 15px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .sidebar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            transition: width 0.3s ease;
            z-index: 0;
        }

        .sidebar-nav .nav-link:hover::before {
            width: 100%;
        }

        .sidebar-nav .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(8px);
            border-left-color: var(--accent-blue);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--accent-blue), var(--secondary-blue));
            color: white;
            font-weight: 600;
            border-left-color: white;
            box-shadow: 0 4px 20px rgba(96, 165, 250, 0.4);
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav .nav-link span {
            position: relative;
            z-index: 1;
        }

        /* Contenido principal mejorado */
        .main-content {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: calc(100vh - 80px);
            position: relative;
        }

        .main-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(135deg, var(--light-blue) 0%, rgba(219, 234, 254, 0.3) 100%);
            z-index: 0;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
            background: white;
            margin: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 2rem;
        }

        /* Efectos adicionales */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Animaciones suaves */
        * {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Responsive mejorado */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.6rem;
            }
            
            .content-wrapper {
                margin: 10px;
                padding: 1.5rem;
                border-radius: 15px;
            }
            
            .offcanvas {
                width: 280px !important;
            }
        }

        /* Scrollbar personalizado para sidebar */
        .offcanvas-body::-webkit-scrollbar {
            width: 6px;
        }

        .offcanvas-body::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .offcanvas-body::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .offcanvas-body::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
    
    @stack('styles')
</head>
<body>

    <!-- Header -->
    <nav class="navbar navbar-light bg-light border-bottom">
        <div class="container-fluid">
            <button class="btn menu-toggle me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-water me-2"></i>IFish
            </span>
            <a href="#" class="btn login-btn">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
            </a>
        </div>
    </nav>

    <!-- Sidebar (offcanvas) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <i class="bi bi-compass me-2"></i>Navegación
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column sidebar-nav">
                @include('partials.navbar')
            </nav>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        <div class="container-fluid p-0">
            <div class="content-wrapper glass-effect">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Efectos adicionales de interacción
        document.addEventListener('DOMContentLoaded', function() {
            // Efecto de ondas en botones
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255,255,255,0.6);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
        
        // CSS para animación de ondas
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
    
    @stack('scripts')
</body>
</html>
