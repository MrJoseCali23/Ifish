@extends('layouts.app')

@section('title', 'Estadísticas y Reportes - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Estadísticas y Reportes</h2>
    </div>

    {{-- SECCIÓN DE FILTROS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            Filtro de Historial de Alimentaciones
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('estadisticas') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}">
                    @error('fecha_inicio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                    <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}">
                    @error('fecha_fin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="bg-light p-2 rounded text-center w-100 h-100 d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-muted">Total en Rango</h6>
                        <p class="fs-4 fw-bold text-primary mb-0">{{ $totalAlimentacionesRango }}</p>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- SECCIÓN DE TARJETAS (KPIs) --}}
    <h4 class="mt-5 mb-3">Indicadores Clave</h4>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted">Alimento Programado para Hoy</h5>
                    <p class="card-text fs-2 fw-bold text-primary">{{ number_format($totalProgramadoHoyKg, 2) }} Kg</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted">Proyección de Inventario</h5>
                    <p class="card-text fs-2 fw-bold text-warning">{{ $diasRestantes }} Días</p>
                    <small class="text-muted">Días de alimento restantes al ritmo actual.</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted">Dispensador con Nivel Más Bajo</h5>
                    @if($dispensadorNivelBajo)
                        <p class="card-text fs-5 fw-bold mb-0"><code>{{ $dispensadorNivelBajo->mac_address }}</code></p>
                        <span class="badge bg-danger fs-6">{{ number_format($dispensadorNivelBajo->nivel_comida_actual_kg, 2) }} Kg</span>
                    @else
                        <p class="card-text fst-italic">No hay dispensadores</p>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- SECCIÓN DE GRÁFICOS --}}
    <h4 class="mt-5 mb-3">Análisis Gráfico</h4>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="card-title mb-0">Consumo de Alimento por Día (Últimos 7 días)</h5></div>
                <div class="card-body">
                    <canvas id="graficoConsumoDiario"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="card-title mb-0">Carga de Trabajo por Dispensador</h5></div>
                <div class="card-body">
                    <canvas id="graficoCargaDispensador"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    {{-- Incluimos la librería Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // --- Gráfico 1: Consumo Diario (Líneas) ---
        const ctxConsumo = document.getElementById('graficoConsumoDiario');
        new Chart(ctxConsumo, {
            type: 'line',
            data: {
                labels: @json($labelsConsumo),
                datasets: [{
                    label: 'Consumo de Alimento (en Kg)',
                    data: @json($dataConsumo),
                    fill: true,
                    borderColor: 'rgb(25, 135, 84)',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.2
                }]
            }
        });

        // --- Gráfico 2: Carga por Dispensador (Barras) ---
        const ctxCarga = document.getElementById('graficoCargaDispensador');
        new Chart(ctxCarga, {
            type: 'bar',
            data: {
                labels: @json($labelsCarga),
                datasets: [{
                    label: 'Kilos programados por día',
                    data: @json($dataCarga),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Pone las barras en horizontal
                scales: {
                    x: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
@endpush
