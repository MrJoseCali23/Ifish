{{-- Siempre visible --}}
<a class="nav-link @if(Route::currentRouteName() == 'estadisticas') active @endif" href="{{ route('estadisticas') }}">
    <i class="bi bi-bar-chart-line"></i>
    <span>Estadísticas</span>
</a>


{{-- Solo si ha iniciado sesión --}}
@auth
    <a class="nav-link @if(Route::currentRouteName() == 'dashboard') active @endif" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <a class="nav-link @if(Route::currentRouteName() == 'estanques') active @endif" href="{{ route('estanques') }}">
        <i class="bi bi-water"></i>
        <span>Estanques</span>
    </a>

    <a class="nav-link @if(Route::currentRouteName() == 'especies') active @endif" href="{{ route('especies') }}">
        <i class="bi bi-list-ul"></i>
        <span>Especies</span>
    </a>

    <a class="nav-link @if(Route::currentRouteName() == 'arduino') active @endif" href="{{ route('arduino') }}">
        <i class="bi bi-usb-symbol"></i>
        <span>Arduino</span>
    </a>

    <a class="nav-link @if(Route::currentRouteName() == 'reportes') active @endif" href="{{ route('reportes') }}">
        <i class="bi bi-file-earmark-text"></i>
        <span>Reportes</span>
    </a>

    {{-- Solo visible para ADMIN --}}
    @if(Auth::user()->rol === 'Admin')
        <a class="nav-link @if(Route::currentRouteName() == 'usuarios') active @endif" href="{{ route('usuarios.index') }}">
            <i class="bi bi-people-fill"></i>
            <span>Usuarios</span>
        </a>

        <a class="nav-link @if(Route::currentRouteName() == 'configuracion') active @endif" href="{{ route('configuracion') }}">
            <i class="bi bi-gear"></i>
            <span>Configuración</span>
        </a>
    @endif
@endauth
<a class="nav-link @if(Route::currentRouteName() == 'ayuda') active @endif" href="{{ route('ayuda') }}">
    <i class="bi bi-question-circle"></i>
    <span>Ayuda</span>
</a>