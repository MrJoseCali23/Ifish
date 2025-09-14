@extends('layouts.app')

@section('title', 'Gestión de Dispensadores - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Gestión de Dispensadores</h2>
        
        @can('create', App\Models\Dispensador::class)
        <a href="{{ route('superadmin.dispensadores-inventario.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Dispensador
        </a>
        @endcan
    </div>

    {{-- ▼▼▼ BLOQUE DE MENSAJES MEJORADO ▼▼▼ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- ▲▲▲ FIN DEL BLOQUE DE MENSAJES ▲▲▲ --}}


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
                                <small class="text-muted"><code>{{ $dispensadore->mac_address }}</code></small>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div>
                                    <p class="card-text mb-1"><i class="bi bi-water text-muted me-2"></i><strong>Estanque:</strong> {{ $dispensadore->estanque->nombre_estanque ?? 'No asignado' }}</p>
                                    @if($dispensadore->estado == 'Activo') <span class="badge bg-success">{{ $dispensadore->estado }}</span>
                                    @elseif($dispensadore->estado == 'Inactivo') <span class="badge bg-secondary">{{ $dispensadore->estado }}</span>
                                    @else <span class="badge bg-danger">{{ $dispensadore->estado }}</span>
                                    @endif
                                    <div class="mt-3">
                                        <p class="mb-1"><i class="bi bi-clock-history text-muted me-2"></i><strong>Horarios Programados:</strong></p>
                                        @if($dispensadore->horarios_count > 0)
                                            <ul class="list-group list-group-flush">
                                                @foreach($dispensadore->horarios->sortBy('hora_programada') as $horario)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-0 border-0">
                                                        <span><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($horario->hora_programada)->format('h:i A') }} - {{ $horario->cantidad_gramos }}g</span>
                                                        @php $ejecutadoHoy = $horario->ultima_ejecucion && \Carbon\Carbon::parse($horario->ultima_ejecucion)->isToday(); @endphp
                                                        @if(!$horario->activo) <span class="badge bg-secondary rounded-pill">Inactivo</span>
                                                        @elseif($ejecutadoHoy) <span class="badge bg-info text-dark rounded-pill">Ya dispensado</span>
                                                        @else <span class="badge bg-success rounded-pill">Activo</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted fst-italic small">Sin horarios programados.</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-auto pt-3">
                                    <label class="form-label d-block mb-1">Nivel de Comida: <strong>{{ number_format($dispensadore->nivel_comida_actual_kg, 2) }} Kg</strong></label>
                                    @php
                                        $capacidad_max_kg = 25;
                                        $porcentaje = ($capacidad_max_kg > 0) ? ($dispensadore->nivel_comida_actual_kg / $capacidad_max_kg) * 100 : 0;
                                        $porcentaje = min(100, $porcentaje);
                                        $color_barra = 'bg-success';
                                        if ($porcentaje < 50) $color_barra = 'bg-warning text-dark';
                                        if ($porcentaje < 20) $color_barra = 'bg-danger';
                                    @endphp
                                    <div class="progress" style="height: 20px;"><div class="progress-bar progress-bar-striped {{ $color_barra }}" role="progressbar" style="width: {{ $porcentaje }}%;" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">{{ round($porcentaje) }}%</div></div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                @can('manualFeed', $dispensadore)
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#manualFeedModal{{ $dispensadore->id_dispensador }}" title="Alimentación Manual"><i class="bi bi-send-fill"></i></button>
                                @endcan
                                @can('update', $dispensadore)
                                    <a href="{{ route('dispensadores.edit', $dispensadore) }}" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                @endcan
                                @can('delete', $dispensadore)
                                    <form action="{{ route('dispensadores.destroy', $dispensadore) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <!-- Modal para Alimentación Manual -->
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
                                            <label class="form-label">Tipo de Comida</label>
                                            <input type="text" class="form-control" value="Detectada de los horarios" readonly>
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
                @empty
                    <div class="col-12"><div class="alert alert-info text-center">No tienes dispensadores asignados a tu criadero.</div></div>
                @endforelse
            </div>

            @if ($dispensadores->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $dispensadores->links() }}</div>
            @endif
        </div>
    </div>
@endsection