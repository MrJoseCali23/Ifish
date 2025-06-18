@extends('layouts.app')

@section('title', 'Programación de Horarios - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">🕒 Programación de Horarios</h2>
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

    {{-- INICIO DEL NUEVO DISEÑO DE TARJETAS --}}
    <div class="row g-4">
        @forelse ($horarios as $horario)
            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="card shadow-sm h-100 w-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-clock-fill me-2"></i>
                            {{-- Usamos Carbon para formatear la hora a un formato amigable (ej: 08:30 AM) --}}
                            {{ \Carbon\Carbon::parse($horario->hora_programada)->format('h:i A') }}
                        </h5>
                        @if($horario->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="card-text mb-1" title="Dispensador">
                            <i class="bi bi-cpu-fill text-muted me-2"></i>
                            <code>{{ $horario->dispensador->mac_address ?? 'N/A' }}</code>
                        </p>
                         <p class="card-text mb-1" title="Estanque">
                            <i class="bi bi-water text-muted me-2"></i>
                            {{-- Fíjate cómo navegamos a través de las relaciones: horario -> dispensador -> estanque --}}
                            <strong>{{ $horario->dispensador->estanque->nombre_estanque ?? 'N/A' }}</strong>
                        </p>
                        <p class="card-text mb-1" title="Tipo de Comida">
                            <i class="bi bi-egg-fried text-muted me-2"></i>
                            {{ $horario->tipoComida->nombre_comida ?? 'N/A' }}
                        </p>
                        <p class="card-text" title="Cantidad">
                            <i class="bi bi-box text-muted me-2"></i>
                            <strong>{{ $horario->cantidad_gramos }}</strong> gramos
                        </p>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('horarios.edit', $horario) }}" class="btn btn-sm btn-warning" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('horarios.destroy', $horario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este horario?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No hay horarios programados todavía.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if ($horarios->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $horarios->links() }}
        </div>
    @endif
@endsection