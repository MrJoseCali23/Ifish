@extends('layouts.app')
@section('title', 'Central de Reportes - iFish')
@section('content')
    <h2 class="text-primary fw-bold"><i class="bi bi-collection-fill me-2"></i> Central de Reportes</h2>
    <p class="text-muted">Selecciona un reporte para generar y analizar los datos de tu operación.</p>

    <div class="row g-4 mt-3">
        
        {{-- Tarjeta para el Reporte de Historial de Alimentación (Visible para Dueños) --}}
        @if(Auth::user()->rol === 'Dueño')
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="text-primary fs-2 mb-3"><i class="bi bi-file-earmark-text-fill"></i></div>
                        <h5 class="card-title">Historial de Alimentación</h5>
                        <p class="card-text text-muted">Consulta la bitácora detallada de todas las alimentaciones en un rango de fechas.</p>
                        <div class="mt-auto">
                            <a href="{{ route('reportes.historial_alimentacion.form') }}" class="btn btn-primary">Generar Reporte</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="text-primary fs-2 mb-3"><i class="bi bi-pie-chart-fill"></i></div>
                        <h5 class="card-title">Reporte de Consumo</h5>
                        <p class="card-text text-muted">Analiza qué tipos de comida se están consumiendo más en tu criadero.</p>
                        <div class="mt-auto">
                            <a href="{{ route('reportes.consumo_comida') }}" class="btn btn-primary">Generar Reporte</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tarjeta para el Reporte de Criaderos (Visible SOLO para Super Admin) --}}
        @if(Auth::user()->rol === 'Admin')
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="text-primary fs-2 mb-3"><i class="bi bi-building"></i></div>
                        <h5 class="card-title">Reporte de Criaderos</h5>
                        <p class="card-text text-muted">Genera una lista de todos los clientes registrados y sus estados actuales.</p>
                        <div class="mt-auto">
                            {{-- Este botón apunta a la nueva página de la tabla --}}
                            <a href="{{ route('reportes.criaderos.tabla') }}" class="btn btn-primary">Ver Reporte</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="text-primary fs-2 mb-3"><i class="bi bi-heart-pulse-fill"></i></div>
                        <h5 class="card-title">Salud de la Plataforma</h5>
                        <p class="card-text text-muted">Supervisa el estado de conexión de todos los dispensadores en la red.</p>
                        <div class="mt-auto">
                            <a href="{{ route('reportes.salud_plataforma') }}" class="btn btn-primary">Ver Reporte</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="text-primary fs-2 mb-3"><i class="bi bi-card-list"></i></div>
                        <h5 class="card-title">Historial de Dispensadores</h5>
                        <p class="card-text text-muted">Audita la "hoja de vida" completa de cada dispensador: asignaciones, mantenimientos, etc.</p>
                        <div class="mt-auto">
                            <a href="{{ route('reportes.historial_dispensador') }}" class="btn btn-primary">Generar Reporte</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection