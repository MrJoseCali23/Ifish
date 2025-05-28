<a class="nav-link @if(Route::currentRouteName() == 'dashboard') active @endif" href=" ">
    <i class="bi bi-speedometer2"></i>
    <span>Dashboard</span>
</a>

<a class="nav-link @if(str_contains(Route::currentRouteName(), 'statistics')) active @endif" href=" ">
    <i class="bi bi-bar-chart-line"></i>
    <span>Estadísticas</span>
</a>

<a class="nav-link @if(str_contains(Route::currentRouteName(), 'catches')) active @endif" href=" ">
    <i class="bi bi-water"></i>
    <span>Capturas</span>
</a>

<a class="nav-link @if(str_contains(Route::currentRouteName(), 'species')) active @endif" href=" ">
    <i class="bi bi-list-ul"></i>
    <span>Especies</span>
</a>

<a class="nav-link @if(str_contains(Route::currentRouteName(), 'locations')) active @endif" href=" ">
    <i class="bi bi-geo-alt"></i>
    <span>Ubicaciones</span>
</a>

<a class="nav-link @if(str_contains(Route::currentRouteName(), 'reports')) active @endif" href=" ">
    <i class="bi bi-file-earmark-text"></i>
    <span>Reportes</span>
</a>

<div style="height: 20px;"></div>

<a class="nav-link @if(str_contains(Route::currentRouteName(), 'settings')) active @endif" href=" ">
    <i class="bi bi-gear"></i>
    <span>Configuración</span>
</a>

<a class="nav-link @if(str_contains(Route::currentRouteName(), 'help')) active @endif" href=" ">
    <i class="bi bi-question-circle"></i>
    <span>Ayuda</span>
</a>
