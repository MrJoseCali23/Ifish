@extends('layouts.app')
@section('title', 'Estadísticas y Reportes - iFish')
@section('content')

{{-- =============================================================== --}}
{{-- PANEL DE ESTADÍSTICAS PARA EL SUPER ADMIN --}}
{{-- =============================================================== --}}
@if ($rol === 'Admin')
    <h2 class="text-primary fw-bold">
        <i class="bi bi-bar-chart-line nav-icon"></i>
        Panel de Control del Administrador</h2>
    <p class="text-muted">Una vista global del estado y crecimiento de la plataforma iFish.</p>
    
    <div class="row g-4 mt-3 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm text-center h-100"><div class="card-body">
                <h5 class="card-title text-muted">Criaderos Activos</h5>
                <p class="card-text display-4 fw-bold text-primary">{{ $data['totalCriaderosActivos'] }}</p>
            </div></div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm text-center h-100"><div class="card-body">
                <h5 class="card-title text-muted">Salud de la Plataforma</h5>
                <p class="card-text display-4 fw-bold text-success">{{ $data['saludPlataforma'] }}%</p>
                <small class="text-muted">% de dispensadores online</small>
            </div></div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm text-center h-100"><div class="card-body">
                <h5 class="card-title text-muted">Alimentaciones Totales (Hoy)</h5>
                <p class="card-text display-4 fw-bold text-info">{{ $data['totalAlimentacionesHoy'] }}</p>
            </div></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><h5 class="card-title mb-0">Crecimiento de la Plataforma (Nuevos Criaderos por Mes)</h5></div>
        <div class="card-body"><canvas id="graficoCrecimiento"></canvas></div>
    </div>
@endif


{{-- =============================================================== --}}
{{-- PANEL DE ESTADÍSTICAS PARA EL DUEÑO DE CRIADERO --}}
{{-- =============================================================== --}}
@if ($rol === 'Dueño')
    <h2 class="text-primary fw-bold">
        <i class="bi bi-bar-chart-line nav-icon"></i>
        Panel de Control de tu Criadero</h2>
    <p class="text-muted">Un resumen del estado y rendimiento de tu operación.</p>

    <div class="row g-4 mt-3 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm text-center h-100"><div class="card-body">
                <h5 class="card-title text-muted">Dispensador a Rellenar</h5>
                @if($data['dispensadorARellenar'])
                    <p class="card-text fs-5 fw-bold mb-0"><code>{{ $data['dispensadorARellenar']->modelo }}</code></p>
                    <span class="badge bg-danger fs-6">{{ number_format($data['dispensadorARellenar']->nivel_comida_actual_kg, 2) }} Kg</span>
                @else
                    <p class="card-text fst-italic mt-3">N/A</p>
                @endif
            </div></div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm text-center h-100"><div class="card-body">
                <h5 class="card-title text-muted">Plan Cumplido Hoy</h5>
                <p class="card-text display-4 fw-bold text-success">{{ $data['planCumplidoHoy'] }}%</p>
                <div class="progress" style="height: 5px;"><div class="progress-bar bg-success" role="progressbar" style="width: {{ $data['planCumplidoHoy'] }}%;" ></div></div>
            </div></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><h5 class="card-title mb-0">Análisis de Consumo de Alimento (Kg)</h5></div>
        <div class="card-body">
            <form method="GET" action="{{ route('estadisticas') }}" class="row g-3 align-items-center mb-3">
                <div class="col-md-5"><label for="fecha_inicio" class="form-label">Desde:</label><input type="date" name="fecha_inicio" class="form-control" value="{{ $data['fechaInicio'] }}"></div>
                <div class="col-md-5"><label for="fecha_fin" class="form-label">Hasta:</label><input type="date" name="fecha_fin" class="form-control" value="{{ $data['fechaFin'] }}"></div>
                <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
            </form>
            <canvas id="graficoConsumoDiario"></canvas>
        </div>
    </div>
@endif

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Usamos un if para ejecutar solo el script del gráfico que se está mostrando
        @if ($rol === 'Admin')
            new Chart(document.getElementById('graficoCrecimiento'), {
                type: 'bar',
                data: {
                    labels: @json($data['labelsCrecimiento']),
                    datasets: [{
                        label: 'Nuevos Criaderos',
                        data: @json($data['dataCrecimiento']),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)'
                    }]
                }
            });
        @elseif ($rol === 'Dueño')
            new Chart(document.getElementById('graficoConsumoDiario'), {
                type: 'line',
                data: {
                    labels: @json($data['labelsConsumo']),
                    datasets: [{
                        label: 'Consumo (Kg)',
                        data: @json($data['dataConsumo']),
                        borderColor: 'rgb(25, 135, 84)',
                        tension: 0.1
                    }]
                }
            });
        @endif
    </script>
@endpush
