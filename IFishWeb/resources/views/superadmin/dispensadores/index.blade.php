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
                    <div class="col-md-5">
                        <label for="filter_criadero" class="form-label">Filtrar por Criadero</label>
                        <select name="filter_criadero" id="filter_criadero" class="form-select">
                            <option value="">Todos los Criaderos</option>
                            @foreach($criaderos as $criadero)
                                <option value="{{ $criadero->id }}" @if(isset($filters['filter_criadero']) && $filters['filter_criadero'] == $criadero->id) selected @endif>{{ $criadero->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="filter_estado" class="form-label">Filtrar por Estado</label>
                        <select name="filter_estado" id="filter_estado" class="form-select">
                            <option value="">Todos los Estados</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado }}" @if(isset($filters['filter_estado']) && $filters['filter_estado'] == $estado) selected @endif>{{ $estado }}</option>
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

    {{-- SECCIÓN DE DISPENSADORES ASIGNADOS (AGRUPADOS) --}}
    <h4 class="mt-5">Dispensadores Asignados</h4>
    @forelse($dispensadoresAsignados as $nombreCriadero => $dispensadores)
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-bold bg-light">
                <i class="bi bi-building me-2"></i>{{ $nombreCriadero }} ({{ count($dispensadores) }} dispensadores)
            </div>
            <ul class="list-group list-group-flush">
                @foreach($dispensadores as $dispensador)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            {{-- ▼▼▼ AQUÍ ESTÁ EL CAMBIO ▼▼▼ --}}
                            <strong>{{ $dispensador->modelo ?? 'Sin modelo' }}</strong>
                            <code class="text-muted ms-2"> ({{ $dispensador->mac_address }})</code>
                            <span class="badge bg-{{ strtolower($dispensador->estado) == 'activo' ? 'success' : 'secondary' }} ms-2">{{ $dispensador->estado }}</span>
                        </div>
                        <div>
                            <a href="{{ route('reportes.historial_dispensador', ['dispensador_id' => $dispensador->id_dispensador]) }}" class="btn btn-sm btn-info" title="Ver Historial">
                                <i class="bi bi-card-list"></i>
                            </a>
                            <a href="{{ route('superadmin.dispensadores-inventario.edit', $dispensador) }}" class="btn btn-sm btn-warning" title="Editar/Reasignar"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('superadmin.dispensadores-inventario.destroy', $dispensador) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar dispensador?');"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <div class="alert alert-secondary">No hay dispensadores asignados que coincidan con los filtros.</div>
    @endforelse

    {{-- SECCIÓN DE DISPENSADORES DISPONIBLES --}}
    <h4 class="mt-5">Disponibles en Inventario</h4>
    <div class="card shadow-sm mb-4">
         <div class="card-body">
            @forelse ($dispensadoresDisponibles as $dispensador)
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                     <div>
                        {{-- ▼▼▼ Y AQUÍ TAMBIÉN ▼▼▼ --}}
                        <strong>{{ $dispensador->modelo ?? 'Sin modelo' }}</strong>
                        <code class="text-muted ms-2"> ({{ $dispensador->mac_address }})</code>
                        <span class="badge bg-{{ strtolower($dispensador->estado) == 'activo' ? 'success' : 'secondary' }} ms-2">{{ $dispensador->estado }}</span>
                    </div>
                    <div>
                         <a href="{{ route('superadmin.dispensadores-inventario.history', $dispensador) }}" class="btn btn-sm btn-info" title="Ver Historial"><i class="bi bi-card-list"></i></a>
                         <a href="{{ route('superadmin.dispensadores-inventario.edit', $dispensador) }}" class="btn btn-sm btn-warning" title="Editar/Asignar"><i class="bi bi-pencil-square"></i></a>
                         <form action="{{ route('superadmin.dispensadores-inventario.destroy', $dispensador) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar dispensador?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button></form>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted p-3">No hay dispensadores disponibles que coincidan con los filtros.</div>
            @endforelse
        </div>
    </div>
@endsection