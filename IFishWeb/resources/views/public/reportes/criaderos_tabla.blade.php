@extends('layouts.app')
@section('title', 'Reporte: Lista de Criaderos')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="bi bi-table me-2"></i> Reporte: Lista de Criaderos</h2>
        <a href="{{ route('reportes.criaderos.pdf') }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar a PDF
        </a>
    </div>

    {{-- ▼▼▼ INICIO DEL NUEVO DISEÑO DE ACORDEÓN ▼▼▼ --}}
    <div class="accordion" id="accordionCriaderos">
        @forelse($criaderosPorDueño as $dueñoNombre => $criaderos)
            <div class="accordion-item">
                @php $slug = Str::slug($dueñoNombre);
                       $idx = $loop->index;
                       $headingId = "heading-{$slug}-{$idx}";
                       $collapseId = "collapse-{$slug}-{$idx}";
                @endphp
                <h2 class="accordion-header" id="{{ $headingId }}">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                        <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                            <span>
                                <i class="bi bi-person-circle me-2"></i>
                                <strong>Dueño: {{ $dueñoNombre }}</strong>
                            </span>
                            <span class="badge bg-primary rounded-pill">{{ count($criaderos) }} criadero(s)</span>
                        </div>
                    </button>
                </h2>
                <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="{{ $headingId }}" data-bs-parent="#accordionCriaderos">
                    <div class="accordion-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre del Criadero</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criaderos as $criadero)
                                    <tr>
                                        <td><strong>{{ $criadero->nombre }}</strong></td>
                                        <td>{{ $criadero->ubicacion }}</td>
                                        <td>
                                            @if($criadero->estado === 'Activo') <span class="badge bg-success">{{ $criadero->estado }}</span>
                                            @elseif($criadero->estado === 'Suspendido') <span class="badge bg-warning text-dark">{{ $criadero->estado }}</span>
                                            @else <span class="badge bg-secondary">{{ $criadero->estado }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                No hay criaderos registrados para mostrar en el reporte.
            </div>
        @endforelse
    </div>
    {{-- ▲▲▲ FIN DEL NUEVO DISEÑO DE ACORDEÓN ▲▲▲ --}}

    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a la Central de Reportes
        </a>
    </div>
@endsection