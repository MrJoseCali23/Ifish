@extends('layouts.app')

@section('title', 'Super Admin: Gestión de Usuarios')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-people-fill nav-icon"></i>
            Gestión de Usuarios</h2>
        <a href="{{ route('superadmin.usuarios.create') }}" class="btn btn-success">
            <i class="bi bi-person-plus me-1"></i> Invitar Nuevo Dueño
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Pestañas de Filtrado --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if($status == 'activos') active @endif" href="{{ route('superadmin.usuarios.index', ['status' => 'activos']) }}">Activos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($status == 'inactivos') active @endif" href="{{ route('superadmin.usuarios.index', ['status' => 'inactivos']) }}">Inactivos / Archivados</a>
        </li>
    </ul>

    <div class="table-responsive shadow-sm glass-effect rounded">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado de Cuenta</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->rol === 'Admin')
                                <span class="badge bg-danger">Super Admin</span>
                            @elseif($usuario->rol === 'Dueño')
                                <span class="badge bg-success">Dueño de Criadero</span>
                            @endif
                        </td>
                        <td>
                            @if($usuario->estado === 'Inactivo')
                                <span class="badge bg-secondary">Desactivado</span>
                            @elseif(is_null($usuario->password) && $usuario->invitation_token)
                                <span class="badge bg-warning text-dark">Invitación Pendiente</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            {{-- ▼▼▼ CAMBIO AQUÍ ▼▼▼ --}}
                            {{-- Añadimos un margen a la derecha (me-1) al botón de editar --}}
                            <a href="{{ route('superadmin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning me-1" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            
                            {{-- Lógica de Botón Inteligente --}}
                            @if($status == 'activos')
                                {{-- Si el usuario está activo, mostramos el botón para desactivar --}}
                                <form action="{{ route('superadmin.usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar esta cuenta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary" title="Desactivar Cuenta">
                                        <i class="bi bi-person-fill-slash"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Si el usuario está inactivo, mostramos el botón para reactivar --}}
                                <form action="{{ route('superadmin.usuarios.restore', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Reactivar esta cuenta?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Reactivar Cuenta">
                                        <i class="bi bi-person-check-fill"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay usuarios en esta sección.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
         @if ($usuarios->hasPages())
           <div class="flex flex-col items-center mt-3">
                <!-- Texto de ayuda -->
                <span class="text-sm text-gray-700 dark:text-gray-400">
                    Mostrando 
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ $usuarios->firstItem() }}
                    </span>
                    a 
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ $usuarios->lastItem() }}
                    </span>
                    de 
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ $usuarios->total() }}
                    </span> 
                    registros
                </span>

                <!-- Botones -->
                <div class="inline-flex mt-2 xs:mt-0">
                    {{-- Anterior --}}
                    @if ($usuarios->onFirstPage())
                        <span class="flex items-center justify-center px-4 h-10 text-base font-medium text-gray-400 bg-gray-200 rounded-s cursor-not-allowed">
                            Anterior
                        </span>
                    @else
                        <a href="{{ $usuarios->previousPageUrl() }}" 
                        class="flex items-center justify-center px-4 h-10 text-base font-medium text-white bg-gray-800 rounded-s hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            Anterior
                        </a>
                    @endif

                    {{-- Siguiente --}}
                    @if ($usuarios->hasMorePages())
                        <a href="{{ $usuarios->nextPageUrl() }}" 
                        class="flex items-center justify-center px-4 h-10 text-base font-medium text-white bg-gray-800 border-0 border-s border-gray-700 rounded-e hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            Siguiente
                        </a>
                    @else
                        <span class="flex items-center justify-center px-4 h-10 text-base font-medium text-gray-400 bg-gray-200 border-s border-gray-300 rounded-e cursor-not-allowed">
                            Siguiente
                        </span>
                    @endif
                </div>
            </div>
        @endif

@endsection