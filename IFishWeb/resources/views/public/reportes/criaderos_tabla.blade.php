@extends('layouts.app')
@section('title', 'Reporte: Lista de Criaderos')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="bi bi-table me-2"></i> Reporte: Lista de Criaderos</h2>
        {{-- EL BOTÓN PARA SACAR EL PDF --}}
        <a href="{{ route('reportes.criaderos.pdf') }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar a PDF
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nombre del Criadero</th>
                            <th>Dueño</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($criaderos as $criadero)
                            <tr>
                                <td><strong>{{ $criadero->nombre }}</strong></td>
                                <td>{{ $criadero->owner->name ?? 'N/A' }}</td>
                                <td>{{ $criadero->ubicacion }}</td>
                                <td>
                                    @if($criadero->estado === 'Activo') <span class="badge bg-success">{{ $criadero->estado }}</span>
                                    @elseif($criadero->estado === 'Suspendido') <span class="badge bg-warning text-dark">{{ $criadero->estado }}</span>
                                    @else <span class="badge bg-secondary">{{ $criadero->estado }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay criaderos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection