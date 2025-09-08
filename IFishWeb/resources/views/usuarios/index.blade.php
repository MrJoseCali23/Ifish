@extends('layouts.app')
@section('title', 'Super Admin: Gestión de Usuarios')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">👥 Gestión de Usuarios</h2>
        <a href="{{ route('superadmin.usuarios.create') }}" class="btn btn-success"><i class="bi bi-person-plus me-1"></i> Nuevo Usuario</a>
    </div>

    {{-- ▼▼▼ NUEVAS PESTAÑAS DE FILTRADO ▼▼▼ --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if($status == 'activos') active @endif" href="{{ route('superadmin.usuarios.index') }}">Activos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($status == 'inactivos') active @endif" href="{{ route('superadmin.usuarios.index', ['status' => 'inactivos']) }}">Inactivos / Archivados</a>
        </li>
    </ul>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="table-responsive shadow-sm glass-effect rounded">
        <table class="table table-hover align-middle">
            {{-- ... cabecera de la tabla (igual que antes) ... --}}
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->rol === 'Admin') <span class="badge bg-danger">Super Admin</span>
                            @elseif($usuario->rol === 'Dueño') <span class="badge bg-success">Dueño de Criadero</span>
                            @else <span class="badge bg-secondary">{{ $usuario->rol }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('superadmin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                            
                            {{-- ▼▼▼ LÓGICA DE BOTÓN INTELIGENTE ▼▼▼ --}}
                            @if($status == 'activos')
                                <form action="{{ route('superadmin.usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar esta cuenta?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-secondary" title="Desactivar Cuenta"><i class="bi bi-person-fill-slash"></i></button>
                                </form>
                            @else
                                {{-- Próximamente: Botón para reactivar --}}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection