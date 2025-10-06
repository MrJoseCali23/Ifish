@extends('layouts.app')
@section('title', 'Super Admin: Gestión de Usuarios')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Gestión de Usuarios</h2>
        <a href="{{ route('superadmin.usuarios.create') }}" class="btn btn-success"><i class="bi bi-person-plus me-1"></i> Nuevo Usuario</a>
    </div>

    {{-- ▼▼▼ PESTAÑAS DE FILTRADO ▼▼▼ --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if($status == 'activos') active @endif" href="{{ route('superadmin.usuarios.index', ['status' => 'activos']) }}">Activos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($status == 'inactivos') active @endif" href="{{ route('superadmin.usuarios.index', ['status' => 'inactivos']) }}">Inactivos / Archivados</a>
        </li>
    </ul>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="table-responsive shadow-sm glass-effect rounded">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th><th>Email</th><th>Rol</th><th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->rol === 'Admin') <span class="badge bg-danger">Super Admin</span>
                            @elseif($usuario->rol === 'Dueño') <span class="badge bg-success">Dueño de Criadero</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('superadmin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                            @if($status == 'activos')
                                {{-- Si el usuario está activo, mostramos el botón para desactivar --}}
                                <form action="{{ route('superadmin.usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar esta cuenta?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-secondary" title="Desactivar Cuenta"><i class="bi bi-person-fill-slash"></i></button>
                                </form>
                            @else
                                <form action="{{ route('superadmin.usuarios.restore', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Reactivar esta cuenta?');">
                                    @csrf
                                    <button class="btn btn-sm btn-success" title="Reactivar Cuenta"><i class="bi bi-person-check-fill"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No hay usuarios en esta sección.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
