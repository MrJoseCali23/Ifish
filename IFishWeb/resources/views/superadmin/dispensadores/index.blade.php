@extends('layouts.app')
@section('title', 'Super Admin: Inventario de Dispensadores')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="bi bi-box-seam-fill me-2"></i> Inventario de Dispensadores</h2>
        <a href="{{ route('superadmin.dispensadores-inventario.create') }}" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Añadir Dispensador</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    {{-- FORMULARIO DE FILTROS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.dispensadores-inventario.index') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5"><label for="filter_criadero" class="form-label">Filtrar por Criadero</label><select name="filter_criadero" id="filter_criadero" class="form-select"><option value="">Todos los Criaderos</option>@foreach($criaderos as $criadero)<option value="{{ $criadero->id }}" @if(isset($filters['filter_criadero']) && $filters['filter_criadero'] == $criadero->id) selected @endif>{{ $criadero->nombre }}</option>@endforeach</select></div>
                    <div class="col-md-5"><label for="filter_estado" class="form-label">Filtrar por Estado</label><select name="filter_estado" id="filter_estado" class="form-select"><option value="">Todos los Estados</option>@foreach($estados as $estado)<option value="{{ $estado }}" @if(isset($filters['filter_estado']) && $filters['filter_estado'] == $estado) selected @endif>{{ $estado }}</option>@endforeach</select></div>
                    <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- SECCIÓN DE DISPENSADORES ASIGNADOS --}}
    <h4 class="mt-5">Dispensadores Asignados</h4>
    <div class="accordion" id="accordionDueños">
        @forelse($dispensadoresPorDueño as $dueñoNombre => $dispensadores)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ Str::slug($dueñoNombre) }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($dueñoNombre) }}" aria-expanded="true">
                        <div class="w-100 d-flex justify-content-between align-items-center pe-3"><span><i class="bi bi-person-circle me-2"></i><strong>Dueño: {{ $dueñoNombre }}</strong></span><span class="badge bg-primary rounded-pill">{{ count($dispensadores) }} dispensadores</span></div>
                    </button>
                </h2>
                <div id="collapse-{{ Str::slug($dueñoNombre) }}" class="accordion-collapse collapse show">
                    <div class="accordion-body p-0"><ul class="list-group list-group-flush">@foreach($dispensadores as $dispensador)<li class="list-group-item d-flex justify-content-between align-items-center p-2 border-bottom">
                        <div>
                            <strong>{{ $dispensador->modelo ?? 'Sin modelo' }}</strong>
                            <code class="text-muted ms-2">({{ $dispensador->mac_address }})</code>
                            <span class="badge bg-info text-dark ms-2">{{ $dispensador->criadero->nombre }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('superadmin.reportes.historial_dispensador', ['dispensador_id' => $dispensador->id_dispensador]) }}" class="btn btn-sm btn-primary me-2" title="Ver Historial"><i class="bi bi-card-list"></i></a>
                            <a href="{{ route('superadmin.dispensadores-inventario.edit', $dispensador) }}" class="btn btn-sm btn-success me-2" title="Editar/Reasignar"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('superadmin.dispensadores-inventario.destroy', $dispensador) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar dispensador?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </li>@endforeach</ul></div>
                </div>
            </div>
        @empty
            <div class="alert alert-secondary">No hay dispensadores asignados que coincidan con los filtros.</div>
        @endforelse
    </div>

    <h4 class="mt-5">Disponibles en Inventario</h4>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @if($dispensadoresDisponibles->isNotEmpty())
                @foreach($dispensadoresDisponibles as $dispensador)
                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                        <div>
                            <strong>{{ $dispensador->modelo ?? 'Sin modelo' }}</strong>
                            <code class="text-muted ms-2"> ({{ $dispensador->mac_address }})</code>
                            <span class="badge bg-{{ $dispensador->estado == 'Activo' ? 'success' : 'secondary' }} ms-2">{{ $dispensador->estado }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('superadmin.reportes.historial_dispensador', ['dispensador_id' => $dispensador->id_dispensador]) }}" class="btn btn-sm btn-primary me-2" title="Ver Historial"><i class="bi bi-card-list"></i></a>
                            <a href="{{ route('superadmin.dispensadores-inventario.edit', $dispensador) }}" class="btn btn-sm btn-success me-2" title="Editar/Asignar"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('superadmin.dispensadores-inventario.destroy', $dispensador) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar dispensador?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted p-3">No hay dispensadores disponibles que coincidan con los filtros.</div>
            @endif
        </div>
    </div>
@endsection