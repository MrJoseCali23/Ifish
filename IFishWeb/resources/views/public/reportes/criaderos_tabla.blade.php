@extends('layouts.app')
@section('title', 'Reporte: Lista de Criaderos')

@section('content')
    {{-- ================================================ --}}
    {{-- ENCABEZADO --}}
    {{-- ================================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-1">
                <i class="bi bi-building me-2"></i> Reporte: Lista de Criaderos
            </h2>
            <small class="text-muted">Visualización general de los criaderos agrupados por dueño.</small>
        </div>
        <a href="{{ route('reportes.criaderos.pdf') }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar a PDF
        </a>
    </div>

    {{-- ================================================ --}}
    {{-- ALERTAS Y RESUMEN GENERAL --}}
    {{-- ================================================ --}}
    @if(!empty($mensajeAdvertencia))
        <div class="alert 
            {{ str_contains($mensajeAdvertencia, '🚨') ? 'alert-danger' : 
               (str_contains($mensajeAdvertencia, '⚠️') ? 'alert-warning' : 
               (str_contains($mensajeAdvertencia, '✅') ? 'alert-success' : 'alert-info')) }}">
            {!! $mensajeAdvertencia !!}
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-collection text-primary fs-2 mb-2"></i>
                    <h6 class="text-muted">Total Criaderos</h6>
                    <h3 class="fw-bold text-primary">{{ $total }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-check-circle-fill text-success fs-2 mb-2"></i>
                    <h6 class="text-muted">Activos</h6>
                    <h3 class="fw-bold text-success">{{ $activos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-pause-circle-fill text-warning fs-2 mb-2"></i>
                    <h6 class="text-muted">Suspendidos</h6>
                    <h3 class="fw-bold text-warning">{{ $suspendidos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-x-circle-fill text-secondary fs-2 mb-2"></i>
                    <h6 class="text-muted">Inactivos</h6>
                    <h3 class="fw-bold text-secondary">{{ $inactivos }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================ --}}
    {{-- LISTADO AGRUPADO POR DUEÑO --}}
    {{-- ================================================ --}}
    <div class="accordion" id="accordionCriaderos">
        @forelse($criaderosPorDueño as $dueñoNombre => $criaderos)
            @php
                $slug = Str::slug($dueñoNombre);
                $idx = $loop->index;
                $headingId = "heading-{$slug}-{$idx}";
                $collapseId = "collapse-{$slug}-{$idx}";
            @endphp

            <div class="accordion-item shadow-sm mb-2">
                <h2 class="accordion-header" id="{{ $headingId }}">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                            type="button" data-bs-toggle="collapse" 
                            data-bs-target="#{{ $collapseId }}" 
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                            aria-controls="{{ $collapseId }}">
                        <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                            <span>
                                <i class="bi bi-person-circle me-2"></i>
                                <strong>{{ $dueñoNombre }}</strong>
                            </span>
                            <span class="badge bg-primary rounded-pill">{{ count($criaderos) }} criadero(s)</span>
                        </div>
                    </button>
                </h2>

                <div id="{{ $collapseId }}" 
                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                     aria-labelledby="{{ $headingId }}" 
                     data-bs-parent="#accordionCriaderos">
                    <div class="accordion-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
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
                                        <td>{{ $criadero->ubicacion ?? 'No especificada' }}</td>
                                        <td>
                                            @if($criadero->estado === 'Activo')
                                                <span class="badge bg-success">Activo</span>
                                            @elseif($criadero->estado === 'Suspendido')
                                                <span class="badge bg-warning text-dark">Suspendido</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $criadero->estado ?? 'Inactivo' }}</span>
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
                <i class="bi bi-info-circle me-2"></i>
                No hay criaderos registrados para mostrar.
            </div>
        @endforelse
    </div>

    {{-- ================================================ --}}
    {{-- BOTÓN VOLVER --}}
    {{-- ================================================ --}}
    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a la Central de Reportes
        </a>
    </div>
@endsection
