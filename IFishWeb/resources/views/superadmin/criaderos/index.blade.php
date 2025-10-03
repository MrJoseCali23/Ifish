@extends('layouts.app')

@section('title', 'Super Admin: Gestión de Criaderos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="bi bi-building me-2"></i> Gestión de Criaderos</h2>
        <a href="{{ route('superadmin.criaderos.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Criadero
        </a>
    </div>

    {{-- Pestañas de Filtrado --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if(request('status') != 'archivados') active @endif" href="{{ route('superadmin.criaderos.index') }}">Activos y Suspendidos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(request('status') == 'archivados') active @endif" href="{{ route('superadmin.criaderos.index', ['status' => 'archivados']) }}">Archivados</a>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="accordion" id="accordionCriaderos">
        @forelse($criaderosPorDueño as $dueñoNombre => $criaderos)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ Str::slug($dueñoNombre) }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($dueñoNombre) }}" aria-expanded="true">
                        <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                            <span>
                                <i class="bi bi-person-circle me-2"></i>
                                <strong>Dueño: {{ $dueñoNombre }}</strong>
                            </span>
                            <span class="badge bg-primary rounded-pill">{{ count($criaderos) }} criadero(s)</span>
                        </div>
                    </button>
                </h2>
                <div id="collapse-{{ Str::slug($dueñoNombre) }}" class="accordion-collapse collapse show">
                    <div class="accordion-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($criaderos as $criadero)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-shop me-2"></i>
                                        <strong>{{ $criadero->nombre }}</strong>
                                        <small class="text-muted ms-2"> ({{ $criadero->ubicacion }})</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $criadero->estado === 'Activo' ? 'success' : 'secondary' }} me-3">{{ $criadero->estado }}</span>
                                        
                                        {{-- ▼▼▼ SECCIÓN DE BOTONES SIMPLIFICADA ▼▼▼ --}}
                                        {{-- Solo permitimos editar los detalles del criadero --}}
                                        <a href="{{ route('superadmin.criaderos.edit', $criadero) }}" class="btn btn-sm btn-warning me-1" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                        {{-- ▲▲▲ FIN DE LA SECCIÓN SIMPLIFICADA ▲▲▲ --}}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                No hay criaderos en esta sección.
            </div>
        @endforelse
    </div>
@endsection