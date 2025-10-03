@extends('layouts.app')

@section('title', 'Programación de Horarios - iFish')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">🕒 Programación de Horarios por Dispensador</h2>
        <a href="{{ route('horarios.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Horario
        </a>
    </div>

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

    <div class="accordion" id="accordionHorarios">
        {{-- ▼▼▼ AQUÍ ESTÁ LA CORRECCIÓN ▼▼▼ --}}
        @forelse ($dispensadoresDelCriadero as $dispensador)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $dispensador->id_dispensador }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $dispensador->id_dispensador }}" aria-expanded="true" aria-controls="collapse{{ $dispensador->id_dispensador }}">
                        <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                            <span>
                                <i class="bi bi-cpu-fill me-2"></i>
                                <strong>Dispensador: {{ $dispensador->modelo ?? 'Sin Modelo' }}</strong>
                                <code class="ms-2 small text-muted">({{ $dispensador->mac_address }})</code>
                                <small class="text-muted ms-2">(Estanque: {{ $dispensador->estanque->nombre_estanque }})</small>
                            </span>
                            <span class="badge bg-primary rounded-pill">{{ $dispensador->horarios->count() }} horarios</span>
                        </div>
                    </button>
                </h2>
                <div id="collapse{{ $dispensador->id_dispensador }}" class="accordion-collapse collapse show" aria-labelledby="heading{{ $dispensador->id_dispensador }}">
                    <div class="accordion-body">
                        {{-- Si un dispensador no tiene horarios, mostramos un mensaje --}}
                        @if($dispensador->horarios->isEmpty())
                            <p class="text-center text-muted">Este dispensador aún no tiene horarios programados.</p>
                        @else
                            <div class="row g-3">
                                @foreach ($dispensador->horarios as $horario)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-clock-fill me-2"></i>{{ \Carbon\Carbon::parse($horario->hora_programada)->format('h:i A') }}</h5>
                                                @php
                                                    $ejecutadoHoy = $horario->ultima_ejecucion && \Carbon\Carbon::parse($horario->ultima_ejecucion)->isToday();
                                                @endphp
                                                @if(!$horario->activo)
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @elseif($ejecutadoHoy)
                                                    <span class="badge bg-info text-dark">Dispensado Hoy</span>
                                                @else
                                                    <span class="badge bg-success">Activo</span>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text mb-1"><i class="bi bi-egg-fried text-muted me-2"></i>{{ $horario->tipoComida->nombre_comida ?? 'N/A' }}</p>
                                                <p class="card-text"><i class="bi bi-box text-muted me-2"></i><strong>{{ $horario->cantidad_gramos }}</strong> gramos</p>
                                            </div>
                                            <div class="card-footer text-end">
                                                <a href="{{ route('horarios.edit', $horario) }}" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                                <form action="{{ route('horarios.destroy', $horario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este horario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                No hay dispensadores asignados a estanques en este criadero.
            </div>
        @endforelse
    </div>
@endsection
