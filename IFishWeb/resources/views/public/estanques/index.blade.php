@extends('layouts.app')

@section('title', 'Gestión de Estanques - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-water me-2"></i>Gestión de Estanques
        </h2>
        @can('create', App\Models\Estanque::class)
            <a href="{{ route('estanques.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Estanque
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0">Lista de Estanques</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre del Estanque</th>
                            <th>Ubicación</th>
                            <th>Dimensiones</th>
                            <th>Dispensadores Asignados</th>
                            <th>Fecha de Creación</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($estanques as $estanque)
                            <tr>
                                <td>
                                    <strong class="text-dark">{{ $estanque->nombre_estanque }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $estanque->ubicacion ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $estanque->dimensiones_metros ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">{{ $estanque->dispensadores_count }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $estanque->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-end">
                                    @can('update', $estanque)
                                        <a href="{{ route('estanques.edit', $estanque) }}" class="btn btn-sm btn-warning me-2" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $estanque)
                                        <form action="{{ route('estanques.destroy', $estanque) }}" method="POST" class="d-inline" onsubmit="return confirm('\u00bfEst\u00e1s seguro de que quieres eliminar este estanque?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay estanques registrados todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if ($estanques->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $estanques->links() }}
                </div>
             @endif
        </div>
    </div>
@endsection