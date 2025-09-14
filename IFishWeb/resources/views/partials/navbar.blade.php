@auth
    {{-- ENLACE AL DASHBOARD (Visible para todos los que inician sesión) --}}
    <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    {{-- =============================================================== --}}
    {{-- MENÚ EXCLUSIVO PARA EL SUPER ADMIN --}}
    {{-- =============================================================== --}}
    @if (Auth::user()->rol === 'Admin')
        <hr class="sidebar-divider my-2"> 
        <div class="sidebar-heading text-white-50 small">GESTIÓN DE PLATAFORMA</div>

        <a class="nav-link @if(request()->routeIs('superadmin.criaderos.*')) active @endif" href="{{ route('superadmin.criaderos.index') }}">
            <i class="bi bi-building"></i>
            <span>Gestionar Criaderos</span>
        </a>
        <a class="nav-link @if(request()->routeIs('superadmin.usuarios.*')) active @endif" href="{{ route('superadmin.usuarios.index') }}">
            <i class="bi bi-people-fill"></i>
            <span>Gestionar Usuarios</span>
        </a>
        
        {{-- Ahora esta condición es mucho más específica y no entrará en conflicto --}}
        <a class="nav-link @if(request()->routeIs(['superadmin.dispensadores-inventario.index', 'superadmin.dispensadores-inventario.create', 'superadmin.dispensadores-inventario.edit'])) active @endif" href="{{ route('superadmin.dispensadores-inventario.index') }}">
            <i class="bi bi-box-seam-fill"></i>
            <span>Inventario Dispensadores</span>
        </a>

        <a class="nav-link @if(request()->routeIs('superadmin.dispensadores-inventario.archivados')) active @endif" href="{{ route('superadmin.dispensadores-inventario.archivados') }}">
            <i class="bi bi-archive-fill"></i>
            <span>Dispensadores Archivados</span>
        </a>
        
        <hr class="sidebar-divider my-2"> 
        <div class="sidebar-heading text-white-50 small">SUPERVISIÓN GLOBAL</div>

        <a class="nav-link @if(request()->routeIs('estadisticas')) active @endif" href="{{ route('estadisticas') }}">
            <i class="bi bi-bar-chart-line"></i>
            <span>Estadísticas Globales</span>
        </a>
        <a class="nav-link @if(request()->routeIs('reportes.*')) active @endif" href="{{ route('reportes.index') }}">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Reportes Globales</span>
        </a>
    @endif
    
    {{-- =============================================================== --}}
    {{-- MENÚ PARA USUARIOS DE CRIADERO (Dueño) --}}
    {{-- =============================================================== --}}
    @if (Auth::user()->rol === 'Dueño')
        <hr class="sidebar-divider my-2">
        <div class="sidebar-heading text-white-50 small">MI CRIADERO</div>

        <a class="nav-link @if(request()->routeIs('estanques.*')) active @endif" href="{{ route('estanques.index') }}">
            <i class="bi bi-water"></i>
            <span>Estanques</span>
        </a>
        <a class="nav-link @if(request()->routeIs('dispensadores.*')) active @endif" href="{{ route('dispensadores.index') }}">
            <i class="bi bi-cpu-fill"></i>
            <span>Dispensadores</span>
        </a>
        <a class="nav-link @if(request()->routeIs('horarios.*')) active @endif" href="{{ route('horarios.index') }}">
            <i class="bi bi-clock-history"></i>
            <span>Horarios</span>
        </a>
        <a class="nav-link @if(request()->routeIs('tipos_comida.*')) active @endif" href="{{ route('tipos_comida.index') }}">
            <i class="bi bi-egg-fried"></i>
            <span>Tipos de Comida</span>
        </a>
         <a class="nav-link @if(request()->routeIs('estadisticas')) active @endif" href="{{ route('estadisticas') }}">
            <i class="bi bi-bar-chart-line"></i>
            <span>Estadísticas</span>
        </a>
        <a class="nav-link @if(request()->routeIs('reportes.*')) active @endif" href="{{ route('reportes.index') }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Reportes</span>
        </a>
    @endif

@endauth

    {{-- ENLACE FINAL (visible para todos) --}}
    <hr class="sidebar-divider my-2">
    <a class="nav-link @if(request()->routeIs('ayuda')) active @endif" href="{{ route('ayuda') }}">
        <i class="bi bi-question-circle"></i>
        <span>Ayuda</span>
    </a>
    
