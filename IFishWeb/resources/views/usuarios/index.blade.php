@extends('layouts.app')

@section('title', 'Usuarios - IFish')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary fw-bold">Gestión de Usuarios</h2>
    <a href="{{ route('superadmin.usuarios.create') }}" class="btn btn-success">
        <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive shadow-sm glass-effect rounded">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        {{-- ▼▼▼ INICIO DEL BLOQUE ACTUALIZADO ▼▼▼ --}}
                        @if($usuario->rol === 'Admin')
                            <span class="badge bg-danger">Super Admin</span>
                        @elseif($usuario->rol === 'Dueño')
                            <span class="badge bg-success">Dueño de Criadero</span>
                        @else
                            {{-- Esto manejará el rol 'Trabajador' o cualquier otro futuro rol --}}
                            <span class="badge bg-secondary">{{ $usuario->rol }}</span>
                        @endif
                        {{-- ▲▲▲ FIN DEL BLOQUE ACTUALIZADO ▲▲▲ --}}
                    </td>
                    <td class="text-end">
                        <a href="{{ route('superadmin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('superadmin.usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection