@extends('layouts.app')
@section('title', 'Reporte: Historial de Dispensadores')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-1">
                <i class="bi bi-card-list me-2"></i> Reporte: Historial de Dispensadores
            </h2>
            <small class="text-muted">
                Rango del <strong>{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</strong>
                al <strong>{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</strong>.
            </small>
        </div>
    </div>

    {{-- Mensaje de advertencia --}}
    @if(!empty($mensajeAdvertencia))
        <div class="alert alert-warning alert-dismissible fade show mt-2" style="white-space: pre-line;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $mensajeAdvertencia }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Formulario de filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.reportes.historial_dispensador') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="dispensador_id" class="form-label">Dispensador:</label>
                        <select name="dispensador_id" class="form-select">
                            <option value="">Todos los Dispensadores</option>
                            @foreach($dispensadores as $dispensador)
                                <option value="{{ $dispensador->id_dispensador }}" 
                                    @selected($request->dispensador_id == $dispensador->id_dispensador)>
                                    {{ $dispensador->modelo }} ({{ $dispensador->mac_address }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Desde:</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                    </div>

                    <div class="col-md-3">
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

    {{-- Resultados --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            <i class="bi bi-clipboard-data me-2"></i> Resultados de la búsqueda
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Dispensador</th>
                            <th>Tipo de Evento</th>
                            <th>Descripción</th>
                            <th>Realizado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($eventos as $evento)
                            <tr>
                                <td>{{ $evento->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <strong>{{ $evento->dispensador->modelo ?? 'N/A' }}</strong><br>
                                    <code class="small text-muted">{{ $evento->dispensador->mac_address ?? '' }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ str_replace('_', ' ', Str::title($evento->tipo_evento)) }}
                                    </span>
                                </td>
                                <td>{{ $evento->descripcion ?? '—' }}</td>
                                <td>{{ $evento->usuario->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    No se encontraron eventos en el rango seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación y resumen --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                @if($eventos->total() > 0)
                    <small class="text-muted">
                        Mostrando {{ $eventos->count() }} de {{ $eventos->total() }} evento(s) encontrados.
                    </small>
                @endif
                {{ $eventos->links() }}
            </div>
        </div>
    </div>

    {{-- Volver --}}
    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a la Central de Reportes
        </a>
    </div>
@endsection
