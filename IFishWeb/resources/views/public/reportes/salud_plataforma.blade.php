@extends('layouts.app')
@section('title', 'Reporte: Salud de la Plataforma')
@section('content')
    <h2 class="text-primary fw-bold"><i class="bi bi-heart-pulse-fill me-2"></i> Reporte: Salud de la Plataforma</h2>
    <p class="text-muted">Este reporte muestra el estado de conexión de todos los dispensadores. Un dispensador se considera "Offline" si no ha enviado un reporte en las últimas 24 horas.</p>

    {{-- Dispensadores con Alertas (Offline) --}}
    <h4 class="mt-4 text-danger">Dispensadores con Alertas (Offline)</h4>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Modelo</th><th>MAC Address</th><th>Criadero Asignado</th><th>Último Reporte</th></tr></thead>
                    <tbody>
                        @forelse($dispensadoresOffline as $dispensador)
                            <tr>
                                <td><strong>{{ $dispensador->modelo }}</strong></td>
                                <td><code>{{ $dispensador->mac_address }}</code></td>
                                <td>{{ $dispensador->criadero->nombre ?? 'Sin Asignar' }}</td>
                                <td>
                                    @if($dispensador->ultimo_reporte)
                                        {{ \Carbon\Carbon::parse($dispensador->ultimo_reporte)->diffForHumans() }}
                                    @else
                                        Nunca ha reportado
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-success">¡Buenas noticias! Todos los dispensadores están online.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Dispensadores Online --}}
    <h4 class="mt-5">Dispensadores Online</h4>
    <div class="card shadow-sm">
        {{-- ... (Aquí iría una tabla similar para los dispensadores online si quisieras listarlos todos) ... --}}
    </div>

    <div class="mt-4">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver a la Central de Reportes</a>
    </div>
@endsection