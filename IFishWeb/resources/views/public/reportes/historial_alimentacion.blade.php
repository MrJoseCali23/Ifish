@extends('layouts.app')
@section('title', 'Reporte: Historial de Alimentación')
@section('content')
    <h2 class="text-primary fw-bold"><i class="bi bi-file-earmark-text-fill me-2"></i> Reporte: Historial de Alimentación</h2>

    {{-- Formulario de Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.historial_alimentacion.form') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="fecha_inicio">Desde:</label>
                        {{-- Usamos la variable $fechaInicio que ya viene corregida del controlador --}}
                        <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ $fechaInicio }}">
                        @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin">Hasta:</label>
                        {{-- Usamos la variable $fechaFin que ya viene corregida del controlador --}}
                        <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ $fechaFin }}">
                        @error('fecha_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="estanque_id">Estanque:</label>
                        <select name="estanque_id" class="form-select">
                            <option value="">Todos los Estanques</option>
                            @foreach($estanques as $estanque)
                                <option value="{{ $estanque->id_estanque }}" @if($request->estanque_id == $estanque->id_estanque) selected @endif>{{ $estanque->nombre_estanque }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Resultados --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <span>Resultados</span>
            <a href="{{ route('reportes.historial_alimentacion.pdf', request()->query()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar a PDF
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Fecha y Hora</th><th>Estanque</th><th>Dispensador</th><th>Tipo de Comida</th><th>Cantidad</th><th>Tipo</th><th>Iniciado Por</th></tr></thead>
                    <tbody>
                        @forelse($registros as $registro)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y h:i A') }}</td>
                                <td>{{ $registro->dispensador->estanque->nombre_estanque ?? 'N/A' }}</td>
                                <td><code>{{ $registro->dispensador->mac_address ?? 'N/A' }}</code></td>
                                <td>{{ $registro->tipoComida->nombre_comida ?? 'N/A' }}</td>
                                <td>{{ $registro->cantidad_dispensada_gramos }} gr.</td>
                                <td><span class="badge bg-{{ $registro->tipo_alimentacion == 'Manual' ? 'info text-dark' : 'secondary' }}">{{ $registro->tipo_alimentacion }}</span></td>
                                <td>{{ $registro->iniciadoPor->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No se encontraron registros con los filtros seleccionados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registros->hasPages())<div class="d-flex justify-content-center mt-3">{{ $registros->links() }}</div>@endif
        </div>
    </div>
@endsection