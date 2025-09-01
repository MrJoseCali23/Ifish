@extends('layouts.app')

@section('title', 'Super Admin: Gestión de Criaderos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="bi bi-building me-2"></i> Gestión de Criaderos</h2>
        <a href="{{ route('superadmin.criaderos.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Criadero
        </a>
    </div>

    {{-- Filtros y Ordenamiento --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link @if($status != 'archivados') active @endif" href="{{ route('superadmin.criaderos.index') }}">Activos y Suspendidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if($status == 'archivados') active @endif" href="{{ route('superadmin.criaderos.index', ['status' => 'archivados']) }}">Archivados</a>
            </li>
        </ul>
        <div class="btn-group">
             <a href="{{ route('superadmin.criaderos.index', array_merge(request()->query(), ['sort' => 'nombre', 'direction' => 'asc'])) }}" class="btn btn-outline-secondary btn-sm">Nombre (A-Z)</a>
             <a href="{{ route('superadmin.criaderos.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => 'desc'])) }}" class="btn btn-outline-secondary btn-sm">Más Recientes</a>
        </div>
    </div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @forelse($criaderos as $criadero)
            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="card shadow-sm h-100 w-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><strong>{{ $criadero->nombre }}</strong></h5>
                        @if($criadero->estado === 'Activo') <span class="badge bg-success">{{ $criadero->estado }}</span>
                        @elseif($criadero->estado === 'Suspendido') <span class="badge bg-warning text-dark">{{ $criadero->estado }}</span>
                        @else <span class="badge bg-secondary">{{ $criadero->estado }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="card-text mb-1"><i class="bi bi-person-fill text-muted me-2"></i><strong>Dueño:</strong> {{ $criadero->owner->name ?? 'No asignado' }}</p>
                        <p class="card-text"><i class="bi bi-geo-alt-fill text-muted me-2"></i><strong>Ubicación:</strong> {{ $criadero->ubicacion ?? 'No especificada' }}</p>
                        <hr>
                        <p class="card-text small text-muted">Registrado el: {{ $criadero->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="card-footer text-end bg-light">
                        @if($status !== 'archivados')
                            {{-- Botones para criaderos activos/suspendidos --}}
                            <a href="{{ route('superadmin.criaderos.assignForm', $criadero) }}" class="btn btn-sm btn-success" title="Asignar Dispensador"><i class="bi bi-plus-lg"></i> Asignar Disp.</a>
                            <a href="{{ route('superadmin.criaderos.edit', $criadero) }}" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('superadmin.criaderos.archive', $criadero) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Archivar este criadero?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" title="Archivar"><i class="bi bi-archive-fill"></i></button>
                            </form>
                        @else
                            {{-- Botón para criaderos archivados --}}
                            <form action="{{ route('superadmin.criaderos.restore', $criadero) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Restaurar este criadero?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Restaurar"><i class="bi bi-arrow-counterclockwise"></i> Restaurar</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No hay criaderos en esta sección.
                </div>
            </div>
        @endforelse
    </div>
    
    @if ($criaderos->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $criaderos->links() }}
        </div>
    @endif
@endsection