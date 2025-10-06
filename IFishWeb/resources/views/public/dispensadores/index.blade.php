@extends('layouts.app')

@section('title', 'Gestión de Dispensadores - iFish')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-cpu-fill nav-icon"></i>
            Gestión de Dispensadores</h2>
        @can('create', App\Models\Dispensador::class)
            <a href="{{ route('superadmin.dispensadores-inventario.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Dispensador
            </a>
        @endcan
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row g-4">
                @forelse ($dispensadores as $dispensadore)
                    <div class="col-12 col-md-6 col-lg-4 d-flex">
                        <div class="card shadow-sm h-100 w-100">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-cpu-fill me-2"></i>
                                    {{ $dispensadore->modelo ?? 'Sin Modelo' }}
                                </h5>
                                <small class="text-muted"><code>{{ $dispensadore->mac_address ?? 'Sin MAC' }}</code></small>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong>Estanque:</strong> {{ $dispensadore->estanque->nombre_estanque ?? 'No asignado' }}</li>
                                    <li class="mb-2"><strong>Estado:</strong> 
                                        @if($dispensadore->estado == 'Activo') <span class="badge bg-success">{{ $dispensadore->estado }}</span>
                                        @elseif($dispensadore->estado == 'Inactivo') <span class="badge bg-secondary">{{ $dispensadore->estado }}</span>
                                        @else <span class="badge bg-danger">{{ $dispensadore->estado }}</span>
                                        @endif
                                    </li>
                                    <li class="mb-2"><strong>Tipo de Comida:</strong> {{ $dispensadore->tipoComidaActual->nombre_comida ?? 'No asignada' }}</li>
                                    <li><strong>Horarios:</strong> {{ $dispensadore->horarios_count ?? 0 }} programados</li>
                                </ul>
                            </div>
                            <div class="card-footer text-end">
                                @can('manualFeed', $dispensadore)
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#manualFeedModal{{ $dispensadore->id_dispensador }}" title="Alimentación Manual">
                                        <i class="bi bi-send-fill"></i>
                                    </button>
                                @endcan
                                @can('update', $dispensadore)
                                    <a href="{{ route('dispensadores.edit', $dispensadore) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            No tienes dispensadores asignados a tu criadero.
                        </div>
                    </div>
                @endforelse
            </div>
            @if ($dispensadores->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $dispensadores->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

{{-- ▼▼▼ INICIO DE LA MEJORA ▼▼▼ --}}
{{-- Usamos @push para enviar todo el código de los modals a una sección 'modals' --}}
{{-- que podemos poner al final de nuestro layout principal. --}}
@push('modals')
    @foreach ($dispensadores as $dispensadore)
        <div class="modal fade" id="manualFeedModal{{ $dispensadore->id_dispensador }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alimentación Manual: {{ $dispensadore->modelo }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('dispensadores.manualFeed', $dispensadore) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Comida Cargada</label>
                                <input type="text" class="form-control" value="{{ $dispensadore->tipoComidaActual->nombre_comida ?? 'No asignada' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="cantidad_dispensada_gramos_{{ $dispensadore->id_dispensador }}" class="form-label">Cantidad a Dispensar (en gramos)</label>
                                <input type="number" class="form-control" id="cantidad_dispensada_gramos_{{ $dispensadore->id_dispensador }}" name="cantidad_dispensada_gramos" required min="1" placeholder="Ej: 150">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Dispensar Manualmente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endpush
{{-- ▲▲▲ FIN DE LA MEJORA ▲▲▲ --}}