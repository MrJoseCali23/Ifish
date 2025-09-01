@extends('layouts.app')

@section('title', 'Gestión de Estanques - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            Gestión de Estanques
            @if(Auth::user()->criadero)
                <small class="text-muted fs-5">/ {{ Auth::user()->criadero->nombre }}</small>
            @endif
        </h2>
        <a href="{{ route('estanques.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Estanque
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
                            {{-- YA NO ESTÁ EL ID --}}
                            <th>Nombre del Estanque</th>
                            <th>Ubicación</th>
                            <th>Dimensiones</th>
                            <th>Creado por</th>
                            <th>Fecha Creación</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                        <tbody>
                                    @forelse ($estanques as $estanque)
                                        <tr>
                                            <td>{{ $estanque->nombre_estanque }}</td>
                                            <td>{{ $estanque->ubicacion }}</td>
                                            <td>{{ $estanque->dimensiones_metros }}</td>
                                            <td>{{ $estanque->creadoPor->name ?? 'Usuario no disponible' }}</td>
                                            <td>{{ $estanque->created_at->format('d/m/Y H:i') }}</td>
                                            
                                            {{-- CÓDIGO DE LOS BOTONES AÑADIDO AQUÍ --}}
                                            <td class="text-end">
    
                                                {{-- El botón de Editar solo se mostrará si el usuario tiene el permiso 'update' --}}
                                                {{-- que definimos en la EstanquePolicy. --}}
                                                @can('update', $estanque)
                                                    <a href="{{ route('estanques.edit', $estanque) }}" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endcan

                                                {{-- El botón de Eliminar solo se mostrará si el usuario tiene el permiso 'delete' --}}
                                                @can('delete', $estanque)
                                                    <form action="{{ route('estanques.destroy', $estanque) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este estanque? Perderás todos los datos asociados.');">
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $estanques->links() }}
                </div>
             @endif
        </div>
    </div>
@endsection