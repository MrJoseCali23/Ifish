@auth
    <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}" aria-label="Ir al Dashboard" data-bs-toggle="tooltip" title="Ver el panel principal">
        <i class="bi bi-speedometer2 nav-icon"></i>
        <span>Dashboard</span>
    </a>

    @if (Auth::user()->rol === 'Admin')
        <hr class="sidebar-divider my-3">
        <div class="sidebar-heading text-white-75 small text-uppercase fw-semibold">Gestión de Plataforma</div>

        <a class="nav-link @if(request()->routeIs('superadmin.usuarios.*')) active @endif" href="{{ route('superadmin.usuarios.index') }}" aria-label="Gestionar Usuarios" data-bs-toggle="tooltip" title="Administrar usuarios de la plataforma">
            <i class="bi bi-people-fill nav-icon"></i>
            <span>Gestionar Usuarios</span>
        </a>
         <a class="nav-link @if(request()->routeIs('superadmin.criaderos.*')) active @endif" href="{{ route('superadmin.criaderos.index') }}" aria-label="Gestionar Criaderos" data-bs-toggle="tooltip" title="Administrar los criaderos registrados">
            <i class="bi bi-building nav-icon"></i>
            <span>Gestionar Criaderos</span>
        </a>
        <a class="nav-link @if(request()->routeIs(['superadmin.dispensadores-inventario.index', 'superadmin.dispensadores-inventario.create', 'superadmin.dispensadores-inventario.edit'])) active @endif" href="{{ route('superadmin.dispensadores-inventario.index') }}" aria-label="Inventario de Dispensadores" data-bs-toggle="tooltip" title="Ver y gestionar inventario de dispensadores">
            <i class="bi bi-box-seam-fill nav-icon"></i>
            <span>Inventario Dispensadores</span>
        </a>
        <a class="nav-link @if(request()->routeIs('superadmin.dispensadores-inventario.archivados')) active @endif" href="{{ route('superadmin.dispensadores-inventario.archivados') }}" aria-label="Dispensadores Archivados" data-bs-toggle="tooltip" title="Ver dispensadores archivados">
            <i class="bi bi-archive-fill nav-icon"></i>
            <span>Dispensadores Archivados</span>
        </a>

        <hr class="sidebar-divider my-3">
        <div class="sidebar-heading text-white-75 small text-uppercase fw-semibold">Supervisión Global</div>

        <a class="nav-link @if(request()->routeIs('estadisticas')) active @endif" href="{{ route('estadisticas') }}" aria-label="Estadísticas Globales" data-bs-toggle="tooltip" title="Ver estadísticas de toda la plataforma">
            <i class="bi bi-bar-chart-line nav-icon"></i>
            <span>Estadísticas Globales</span>
        </a>
        <a class="nav-link @if(request()->routeIs('reportes.*')) active @endif" href="{{ route('reportes.index') }}" aria-label="Reportes Globales" data-bs-toggle="tooltip" title="Generar reportes globales">
            <i class="bi bi-file-earmark-text-fill nav-icon"></i>
            <span>Reportes Globales</span>
        </a>
    @endif

    @if (Auth::user()->rol === 'Dueño')
        <hr class="sidebar-divider my-3">
        <div class="sidebar-heading text-white-75 small text-uppercase fw-semibold">Mi Criadero</div>

        <a class="nav-link @if(request()->routeIs('estanques.*')) active @endif" href="{{ route('estanques.index') }}" aria-label="Gestionar Estanques" data-bs-toggle="tooltip" title="Administrar estanques del criadero">
            <i class="bi bi-water nav-icon"></i>
            <span>Estanques</span>
        </a>
        <a class="nav-link @if(request()->routeIs('dispensadores.*')) active @endif" href="{{ route('dispensadores.index') }}" aria-label="Gestionar Dispensadores" data-bs-toggle="tooltip" title="Administrar dispensadores">
            <i class="bi bi-cpu-fill nav-icon"></i>
            <span>Dispensadores</span>
        </a>
        <a class="nav-link @if(request()->routeIs('horarios.*')) active @endif" href="{{ route('horarios.index') }}" aria-label="Gestionar Horarios" data-bs-toggle="tooltip" title="Configurar horarios de alimentación">
            <i class="bi bi-clock-history nav-icon"></i>
            <span>Horarios</span>
        </a>
        <a class="nav-link @if(request()->routeIs('tipos_comida.*')) active @endif" href="{{ route('tipos_comida.index') }}" aria-label="Gestionar Tipos de Comida" data-bs-toggle="tooltip" title="Definir tipos de comida disponibles">
            <i class="bi bi-egg-fried nav-icon"></i>
            <span>Tipos de Comida</span>
        </a>
        <a class="nav-link @if(request()->routeIs('estadisticas')) active @endif" href="{{ route('estadisticas') }}" aria-label="Estadísticas del Criadero" data-bs-toggle="tooltip" title="Ver estadísticas del criadero">
            <i class="bi bi-bar-chart-line nav-icon"></i>
            <span>Estadísticas</span>
        </a>
        <a class="nav-link @if(request()->routeIs('reportes.*')) active @endif" href="{{ route('reportes.index') }}" aria-label="Reportes del Criadero" data-bs-toggle="tooltip" title="Generar reportes del criadero">
            <i class="bi bi-file-earmark-text nav-icon"></i>
            <span>Reportes</span>
        </a>
    @endif

@endauth

<hr class="sidebar-divider my-3">
<a class="nav-link @if(request()->routeIs('ayuda')) active @endif" href="{{ route('ayuda') }}" aria-label="Ayuda y Soporte" data-bs-toggle="tooltip" title="Acceder a la sección de ayuda">
    <i class="bi bi-question-circle nav-icon"></i>
    <span>Ayuda</span>
</a>