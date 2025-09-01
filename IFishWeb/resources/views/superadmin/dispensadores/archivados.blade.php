@extends('layouts.app')
@section('title', 'Super Admin: Dispensadores Archivados')
@section('content')
    <h2 class="text-primary fw-bold"><i class="bi bi-archive-fill me-2"></i> Dispensadores de Criaderos Archivados</h2>
    <p class="text-muted">Esta es una lista de dispensadores que pertenecen a criaderos archivados. Desde aquí puedes reasignarlos a un criadero activo.</p>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>MAC Address</th>
                        <th>Modelo</th>
                        <th>Criadero Archivado</th>
                        <th>Estado Actual</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dispensadoresArchivados as $dispensador)
                        <tr>
                            <td><code>{{ $dispensador->mac_address }}</code></td>
                            <td>{{ $dispensador->modelo }}</td>
                            <td>{{ $dispensador->criadero->nombre }}</td>
                            <td><span class="badge bg-secondary">{{ $dispensador->estado }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('superadmin.dispensadores-inventario.edit', $dispensador) }}" class="btn btn-sm btn-warning" title="Reasignar"><i class="bi bi-pencil-square"></i> Reasignar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No hay dispensadores en criaderos archivados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection