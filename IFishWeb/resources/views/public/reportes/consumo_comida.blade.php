@extends('layouts.app')
@section('title', 'Reporte: Consumo de Comida')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="bi bi-pie-chart-fill me-2"></i> Reporte: Consumo de Comida</h2>
        <a href="{{ route('reportes.consumo_comida.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar a PDF
        </a>
    </div>

    {{-- Formulario de Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.consumo_comida') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5"><label for="fecha_inicio">Desde:</label><input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}"></div>
                    <div class="col-md-5"><label for="fecha_fin">Hasta:</label><input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- Resultados --}}
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header">Consumo Detallado</div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Tipo de Comida</th><th class="text-end">Total Consumido</th></tr></thead>
                        <tbody>
                            @forelse($consumoPorTipo as $consumo)
                                <tr>
                                    <td>{{ $consumo->nombre_comida }}</td>
                                    <td class="text-end">{{ number_format($consumo->total_consumido / 1000, 2) }} Kg</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No hay datos de consumo en el rango de fechas seleccionado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header">Distribución del Consumo</div>
                <div class="card-body">
                    <canvas id="graficoConsumo"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
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
                    label: 'Consumo (gramos)',
                    data: @json($dataGrafico),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Distribución de Consumo por Tipo de Comida' }
                }
            }
        });
    </script>
@endpush