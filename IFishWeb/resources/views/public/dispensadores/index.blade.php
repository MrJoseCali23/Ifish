@extends('layouts.app')

@section('title', 'Gestión de Dispensadores - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Gestión de Dispensadores</h2>
        <a href="{{ route('dispensadores.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Dispensador
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>MAC Address</th>
                            <th>Estanque Asignado</th>
                            <th>Modelo</th>
                            <th>Estado</th>
                            <th>Nivel Comida (Kg)</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dispensadores as $dispensador)
                            <tr>
                                <td><code>{{ $dispensador->mac_address }}</code></td>
                                {{-- Usamos la relación para obtener el nombre del estanque --}}
                                <td>{{ $dispensador->estanque->nombre_estanque ?? 'No asignado' }}</td>
                                <td>{{ $dispensador->modelo }}</td>
                                <td>
                                    @if($dispensador->estado == 'Activo')
                                        <span class="badge bg-success">{{ $dispensador->estado }}</span>
                                    @elseif($dispensador->estado == 'Inactivo')
                                        <span class="badge bg-secondary">{{ $dispensador->estado }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $dispensador->estado }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format($dispensador->nivel_comida_actual_kg, 2) }} Kg</td>
                                <td class="text-end">
                                    <a href="{{ route('dispensadores.edit', $dispensador) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('dispensadores.destroy', $dispensador) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este dispensador?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay dispensadores registrados todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($dispensadores->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $dispensadores->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection