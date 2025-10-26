@extends('layouts.app')
@section('title', 'Reporte: Salud de la Plataforma')

@section('content')
    <h2 class="text-primary fw-bold mb-3">
        <i class="bi bi-heart-pulse-fill me-2"></i> Reporte: Salud de la Plataforma
    </h2>
    <p class="text-muted">
        Este reporte muestra el estado de conexión de todos los dispensadores registrados.  
        Un dispensador se considera <strong>offline</strong> si no ha enviado datos en las últimas 24 horas.
    </p>

    {{-- 🔔 Mensaje dinámico --}}
    @if(!empty($mensajeAdvertencia))
        <div class="alert {{ str_contains($mensajeAdvertencia, '🚨') ? 'alert-danger' : (str_contains($mensajeAdvertencia, '✅') ? 'alert-success' : 'alert-warning') }} fade show" style="white-space: pre-line;">
            {!! $mensajeAdvertencia !!}
        </div>
    @endif

    {{-- 📊 Resumen general --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-wifi text-success fs-1 mb-2"></i>
                    <h5 class="card-title">Online</h5>
                    <p class="display-5 fw-bold text-success">{{ $dispensadoresOnline->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-wifi-off text-danger fs-1 mb-2"></i>
                    <h5 class="card-title">Offline</h5>
                    <p class="display-5 fw-bold text-danger">{{ $dispensadoresOffline->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-speedometer2 text-primary fs-1 mb-2"></i>
                    <h5 class="card-title">Salud Global</h5>
                    <p class="display-5 fw-bold text-primary">{{ $porcentajeOnline }}%</p>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: {{ $porcentajeOnline }}%;"></div>
                    </div>
                    <small class="text-muted">Porcentaje de dispensadores online</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 🟥 Dispensadores Offline --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold text-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Dispensadores con Alertas (Offline)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Modelo</th>
                            <th>MAC Address</th>
                            <th>Criadero</th>
                            <th>Último Reporte</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispensadoresOffline as $disp)
                            <tr>
                                <td><strong>{{ $disp->modelo }}</strong></td>
                                <td><code>{{ $disp->mac_address }}</code></td>
                                <td>{{ $disp->criadero->nombre ?? 'Sin Asignar' }}</td>
                                <td>
                                    @if($disp->ultimo_reporte)
                                        {{ \Carbon\Carbon::parse($disp->ultimo_reporte)->diffForHumans() }}
                                    @else
                                        <span class="text-muted">Nunca reportó</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-success py-3">
                                    <i class="bi bi-check-circle me-2"></i> ¡Todos los dispensadores están online!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 🟩 Dispensadores Online --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold text-success">
            <i class="bi bi-check-circle-fill me-2"></i> Dispensadores Online
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Modelo</th>
                            <th>MAC Address</th>
                            <th>Criadero</th>
                            <th>Último Reporte</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispensadoresOnline as $disp)
                            <tr>
                                <td><strong>{{ $disp->modelo }}</strong></td>
                                <td><code>{{ $disp->mac_address }}</code></td>
                                <td>{{ $disp->criadero->nombre ?? 'Sin Asignar' }}</td>
                                <td>{{ \Carbon\Carbon::parse($disp->ultimo_reporte)->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    No hay dispensadores activos en este momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 🔙 Volver --}}
    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a la Central de Reportes
        </a>
    </div>
@endsection
