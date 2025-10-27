@extends('layouts.app')
@section('title', 'Reporte: Historial de Alimentación')

@section('content')
    <h2 class="text-primary fw-bold">
        <i class="bi bi-file-earmark-text-fill me-2"></i> Reporte: Historial de Alimentación
    </h2>

        @if(!empty($mensajeAdvertencia))
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert" style="white-space: pre-line;">
                {{ $mensajeAdvertencia }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4 mt-4">
        <div class="card-body">
            <form id="filtroForm" method="GET" action="{{ route('reportes.historial_alimentacion.form') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="criadero_id" class="form-label">Criadero:</label>
                        <select name="criadero_id" class="form-select">
                            <option value="{{ session('active_criadero_id') }}">Solo Criadero Actual</option>
                            <option value="todos" @if(request('criadero_id') === 'todos') selected @endif>Todos mis Criaderos</option>
                            <optgroup label="Elegir otro:">
                                @foreach($criaderosDelDueño as $criadero)
                                    <option value="{{ $criadero->id }}" 
                                        @if(request('criadero_id') == $criadero->id) selected @endif>
                                        {{ $criadero->nombre }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="estanque_id" class="form-label">Estanque:</label>
                        <select name="estanque_id" class="form-select">
                            <option value="">Todos los Estanques</option>
                            @foreach($estanques as $estanque)
                                <option value="{{ $estanque->id_estanque }}" 
                                    @if(request('estanque_id') == $estanque->id_estanque) selected @endif>
                                    {{ $estanque->nombre_estanque }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Desde:</label>
                        <input type="date" name="fecha_inicio" class="form-control"
                               max="{{ now()->toDateString() }}"
                               value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta:</label>
                        <input type="date" name="fecha_fin" class="form-control"
                               max="{{ now()->toDateString() }}"
                               value="{{ $fechaFin }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel-fill me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="bi bi-table me-2"></i> Resultados de la Búsqueda</span>
            <a href="{{ route('reportes.historial_alimentacion.pdf', request()->query()) }}" 
               class="btn btn-sm btn-danger">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar PDF
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            @if(request('criadero_id') === 'todos')
                                <th>Criadero</th>
                            @endif
                            <th>Estanque</th>
                            <th>Dispensador</th>
                            <th>Tipo de Comida</th>
                            <th>Cantidad (gr)</th>
                            <th>Tipo</th>
                            <th>Iniciado Por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y h:i A') }}</td>
                                @if(request('criadero_id') === 'todos')
                                    <td>{{ $registro->dispensador->estanque->criadero->nombre ?? 'N/A' }}</td>
                                @endif
                                <td>{{ $registro->dispensador->estanque->nombre_estanque ?? 'N/A' }}</td>
                                <td><code>{{ $registro->dispensador->modelo ?? 'N/A' }}</code></td>
                                <td>{{ $registro->tipoComida->nombre_comida ?? 'N/A' }}</td>
                                <td>{{ $registro->cantidad_dispensada_gramos }}</td>
                                <td>
                                    <span class="badge bg-{{ $registro->tipo_alimentacion == 'Manual' ? 'info text-dark' : 'secondary' }}">
                                        {{ $registro->tipo_alimentacion }}
                                    </span>
                                </td>
                                <td>{{ $registro->iniciadoPor->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No se encontraron registros con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registros->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $registros->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a la Central de Reportes
        </a>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById('filtroForm');
            const inicio = document.querySelector('[name="fecha_inicio"]');
            const fin = document.querySelector('[name="fecha_fin"]');
            const hoy = new Date().toISOString().split('T')[0];

            form.addEventListener('submit', (e) => {
                if (inicio.value && fin.value && fin.value < inicio.value) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Rango inválido',
                        text: 'La fecha final no puede ser anterior a la fecha inicial.',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }
                if (inicio.value > hoy || fin.value > hoy) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'Fechas no válidas',
                        text: 'No puedes seleccionar fechas futuras.',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }
            });

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin resultados',
                    text: '{{ session('warning') }}',
                    confirmButtonText: 'Entendido'
                });
            @endif
        });
    </script>
@endpush
