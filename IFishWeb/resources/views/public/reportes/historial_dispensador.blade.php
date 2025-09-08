@extends('layouts.app')
@section('title', 'Reporte: Historial de Dispensadores')
@section('content')
    <h2 class="text-primary fw-bold"><i class="bi bi-card-list me-2"></i> Reporte: Historial de Eventos de Dispensadores</h2>

    {{-- Formulario de Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.historial_dispensador') }}">
                <div class="row g-3">
                    <div class="col-md-4"><label for="dispensador_id">Dispensador:</label><select name="dispensador_id" class="form-select"><option value="">Todos los Dispensadores</option>@foreach($dispensadores as $dispensador)<option value="{{ $dispensador->id_dispensador }}" @if($request->dispensador_id == $dispensador->id_dispensador) selected @endif>{{ $dispensador->modelo }} ({{$dispensador->mac_address}})</option>@endforeach</select></div>
                    <div class="col-md-3"><label for="fecha_inicio">Desde:</label><input type="date" name="fecha_inicio" class="form-control" value="{{ $request->fecha_inicio }}"></div>
                    <div class="col-md-3"><label for="fecha_fin">Hasta:</label><input type="date" name="fecha_fin" class="form-control" value="{{ $request->fecha_fin }}"></div>
                    <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Resultados --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    {{-- ▼▼▼ INICIO DEL CÓDIGO QUE FALTABA ▼▼▼ --}}
                    <thead class="table-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Tipo de Evento</th>
                            <th>Descripción</th>
                            <th>Realizado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($eventos as $evento)
                            <tr>
                                <td>{{ $evento->created_at->format('d/m/Y H:i:s') }}</td>
                                <td><span class="badge bg-secondary">{{ str_replace('_', ' ', Str::title($evento->tipo_evento)) }}</span></td>
                                <td>{{ $evento->descripcion }}</td>
                                <td>{{ $evento->usuario->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No se encontraron eventos con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    {{-- ▲▲▲ FIN DEL CÓDIGO QUE FALTABA ▲▲▲ --}}
                </table>
            </div>
            @if($eventos->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $eventos->links() }}
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