@extends('layouts.app')
@section('title', 'Reporte: Historial de Alimentación')
@section('content')
    <h2 class="text-primary fw-bold"><i class="bi bi-file-earmark-text-fill me-2"></i> Reporte: Historial de Alimentación</h2>

    {{-- Formulario de Filtros Mejorado --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.historial_alimentacion.form') }}">
                <div class="row g-3">
                    {{-- Filtro de Criadero --}}
                    <div class="col-md-3"><label for="criadero_id" class="form-label">Criadero:</label>
                        <select name="criadero_id" class="form-select">
                            <option value="{{ session('active_criadero_id') }}">Solo Criadero Actual</option>
                            <option value="todos">Todos mis Criaderos</option>
                            <optgroup label="Elegir otro:">
                                @foreach($criaderosDelDueño as $criadero)
                                    <option value="{{ $criadero->id }}" @if(request('criadero_id') == $criadero->id) selected @endif>{{ $criadero->nombre }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    {{-- Filtro de Estanque --}}
                    <div class="col-md-3"><label for="estanque_id" class="form-label">Estanque:</label><select name="estanque_id" class="form-select"><option value="">Todos los Estanques</option>@foreach($estanques as $estanque)<option value="{{ $estanque->id_estanque }}" @if(request('estanque_id') == $estanque->id_estanque) selected @endif>{{ $estanque->nombre_estanque }}</option>@endforeach</select></div>
                    {{-- Filtros de Fecha --}}
                    <div class="col-md-2"><label for="fecha_inicio" class="form-label">Desde:</label><input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}"></div>
                    <div class="col-md-2"><label for="fecha_fin" class="form-label">Hasta:</label><input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}"></div>
                    <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Resultados --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <span>Resultados de la Búsqueda</span>
            <a href="{{ route('reportes.historial_alimentacion.pdf', request()->query()) }}" class="btn btn-sm btn-danger"><i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar a PDF</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            {{-- La columna Criadero solo aparece si se seleccionó "Todos mis Criaderos" --}}
                            @if(request('criadero_id') === 'todos')<th>Criadero</th>@endif
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
                                @if(request('criadero_id') === 'todos')<td>{{ $registro->dispensador->criadero->nombre ?? 'N/A' }}</td>@endif
                                <td>{{ $registro->dispensador->estanque->nombre_estanque ?? 'N/A' }}</td>
                                <td><code>{{ $registro->dispensador->modelo ?? 'N/A' }}</code></td>
                                <td>{{ $registro->tipoComida->nombre_comida ?? 'N/A' }}</td>
                                <td>{{ $registro->cantidad_dispensada_gramos }}</td>
                                <td><span class="badge bg-{{ $registro->tipo_alimentacion == 'Manual' ? 'info text-dark' : 'secondary' }}">{{ $registro->tipo_alimentacion }}</span></td>
                                <td>{{ $registro->iniciadoPor->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No se encontraron registros con los filtros seleccionados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registros->hasPages())<div class="d-flex justify-content-center mt-3">{{ $registros->links() }}</div>@endif
        </div>
    </div>
@endsection
