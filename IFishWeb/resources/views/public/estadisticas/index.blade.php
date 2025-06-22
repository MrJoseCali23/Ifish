@extends('layouts.app')

@section('title', 'Estadísticas y Reportes - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Estadísticas y Reportes</h2>
    </div>

    {{-- SECCIÓN 1: FILTRO POR FECHAS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('estadisticas') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label for="fecha_inicio" class="form-label">Mostrar consumo desde:</label>
                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}">
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-5">
                    <label for="fecha_fin" class="form-label">Hasta:</label>
                    <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}">
                    @error('fecha_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SECCIÓN 2: TARJETAS DE RESUMEN (KPIs) --}}
    <h4 class="mt-4 mb-3">Resumen Rápido</h4>
    <div class="row g-4 mb-4">
        <div class="col-lg col-md-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body"><h5 class="card-title text-muted">Almacenamiento Total</h5><p class="card-text fs-2 fw-bold text-primary">{{ number_format($inventarioTotalKg, 2) }} Kg</p></div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body"><h5 class="card-title text-muted">Comida Restante</h5><p class="card-text fs-2 fw-bold text-warning">{{ $diasRestantes }} Días</p></div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body"><h5 class="card-title text-muted">Dispensador a Rellenar</h5>
                @if($dispensadorARellenar)
                    <p class="card-text fs-6 fw-bold mb-0"><code>{{ $dispensadorARellenar->mac_address }}</code></p>
                    <span class="badge bg-danger fs-6">{{ number_format($dispensadorARellenar->nivel_comida_actual_kg, 2) }} Kg</span>
                @else
                    <p class="card-text fst-italic mt-3">N/A</p>
                @endif
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body"><h5 class="card-title text-muted">Plan Cumplido Hoy</h5>
                    <p class="card-text fs-2 fw-bold text-success">{{ $planCumplidoHoy }}%</p>
                    <div class="progress" style="height: 5px;"><div class="progress-bar bg-success" role="progressbar" style="width: {{ $planCumplidoHoy }}%;" ></div></div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body"><h5 class="card-title text-muted">Alimentaciones de Hoy</h5>
                    <p class="card-text mb-0"><span class="fw-bold">{{ $programadasHoy }}</span> Programadas</p>
                    <p class="card-text"><span class="fw-bold">{{ $manualesHoy }}</span> Manuales</p>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: GRÁFICO PRINCIPAL --}}
    <h4 class="mt-5 mb-3">Análisis de Consumo</h4>
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="card-title mb-0">Comida Gastada (Kg) del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</h5></div>
                <div class="card-body">
                    <canvas id="graficoConsumoDiario"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- Gráfico de Consumo Diario (Líneas) ---
        new Chart(document.getElementById('graficoConsumoDiario'), {
            type: 'line',
            data: {
                labels: @json($labelsConsumo),
                datasets: [{
                    label: 'Consumo (Kg)',
                    data: @json($dataConsumo),
                    fill: true,
                    borderColor: 'rgb(25, 135, 84)',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush