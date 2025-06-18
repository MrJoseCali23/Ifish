@extends('layouts.app')

@section('title', 'Tipos de Comida - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Gestión de Tipos de Comida</h2>
        <a href="{{ route('tipos_comida.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Tipo de Comida
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
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Proveedor</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos_comida as $tipo)
                            <tr>
                                <td>{{ $tipo->nombre_comida }}</td>
                                <td>{{ $tipo->descripcion }}</td>
                                <td>{{ $tipo->proveedor }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tipos_comida.edit', $tipo) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('tipos_comida.destroy', $tipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este tipo de comida?');">
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
                                <td colspan="4" class="text-center">No hay tipos de comida registrados todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tipos_comida->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $tipos_comida->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection