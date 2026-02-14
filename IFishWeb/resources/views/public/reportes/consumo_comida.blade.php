@extends('layouts.app')
@section('title', 'Reporte: Consumo de Comida')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-1">
                <i class="bi bi-pie-chart-fill me-2"></i> Reporte: Consumo de Comida
            </h2>
            <small class="text-muted">Análisis del consumo total de alimento en el criadero seleccionado.</small>
        </div>
        <a href="{{ route('reportes.consumo_comida.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar a PDF
        </a>
    </div>

    {{-- 🔔 Advertencia --}}
    @if(!empty($mensajeAdvertencia))
        <div class="alert alert-warning alert-dismissible fade show" style="white-space: pre-line;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $mensajeAdvertencia }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 📅 Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.consumo_comida') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="fecha_inicio" class="form-label">Desde:</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-5">
                        <label for="fecha_fin" class="form-label">Hasta:</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel-fill me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 📊 Resumen general --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-archive text-primary fs-2 mb-2"></i>
                    <h6 class="text-muted">Total Consumido</h6>
                    <h3 class="fw-bold text-primary">{{ number_format($totalActual / 1000, 2) }} Kg</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-calendar3 text-secondary fs-2 mb-2"></i>
                    <h6 class="text-muted">Periodo Anterior</h6>
                    <h3 class="fw-bold text-secondary">{{ number_format($totalAnterior / 1000, 2) }} Kg</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-graph-up-arrow fs-2 mb-2 {{ $variacion >= 0 ? 'text-success' : 'text-danger' }}"></i>
                    <h6 class="text-muted">Variación</h6>
                    <h3 class="fw-bold {{ $variacion >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $variacion >= 0 ? '+' : '' }}{{ $variacion }}%
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- 📈 Tabla + Gráfico --}}
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold">
                    <i class="bi bi-table me-2"></i> Consumo Detallado por Tipo
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo de Comida</th>
                                <th class="text-end">Total Consumido (Kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consumoPorTipo as $consumo)
                                <tr>
                                    <td>{{ $consumo->nombre_comida }}</td>
                                    <td class="text-end">{{ number_format($consumo->total_consumido / 1000, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">
                                        <i class="bi bi-info-circle me-2"></i>No hay datos en el rango seleccionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold">
                    <i class="bi bi-pie-chart-fill me-2"></i> Distribución del Consumo
                </div>
                <div class="card-body">
                    <canvas id="graficoConsumo"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('graficoConsumo'), {
            type: 'pie',
            data: {
                labels: @json($labelsGrafico),
                datasets: [{
                    label: 'Consumo (g)',
                    data: @json($dataGrafico),
                    backgroundColor: [
                        '#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#14b8a6','#84cc16','#f43f5e'
                    ],
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Distribución de Consumo por Tipo de Comida' }
                }
            }
        });
    </script>
@endpush
