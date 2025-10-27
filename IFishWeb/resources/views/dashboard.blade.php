@extends('layouts.app')

@section('title', 'Dashboard - iFish')

@section('content')
<div class="container-fluid">
    {{-- =============================================================== --}}
    {{-- PANEL DE CONTROL PARA EL SUPER ADMIN --}}
    {{-- =============================================================== --}}
    @if (Auth::user()->rol === 'Admin')
        <h2 class="text-primary fw-bold">👋 ¡Hola, {{ Auth::user()->name }}!</h2>
        <p class="text-muted">Aquí tienes un resumen del estado global de la plataforma iFish.</p>

        <div class="row g-4 mt-3">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Criaderos Activos</h5>
                        <p class="display-5 fw-bold text-primary">{{ $data['totalCriaderosActivos'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Salud de la Plataforma</h5>
                        <p class="display-5 fw-bold text-success">{{ $data['saludPlataforma'] ?? 0 }}%</p>
                        <small class="text-muted">% de dispensadores online</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Alimentaciones Totales (Hoy)</h5>
                        <p class="display-5 fw-bold text-info">{{ $data['totalAlimentacionesHoy'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- =============================================================== --}}
    {{-- PANEL DE CONTROL PARA EL DUEÑO DE CRIADERO --}}
    {{-- =============================================================== --}}
    @if (Auth::user()->rol === 'Dueño')
        <h2 class="text-primary fw-bold">👋 ¡Hola, {{ Auth::user()->name }}!</h2>
        <p class="text-muted">Aquí tienes un resumen del estado de tu criadero hoy.</p>

        <div class="row g-4 mt-3">
            {{-- Próximas Alimentaciones --}}
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold"><i class="bi bi-clock-history me-2"></i>Próximas Alimentaciones</div>
                    <ul class="list-group list-group-flush">
                        @forelse($data['proximosHorarios'] ?? [] as $horario)
                            <li class="list-group-item">
                                <strong>{{ \Carbon\Carbon::parse($horario->hora_programada)->format('h:i A') }}</strong>
                                - {{ $horario->dispensador->modelo ?? 'Dispensador' }}
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No hay más alimentaciones programadas para hoy.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Alertas de Nivel de Comida --}}
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Alertas de Nivel de Comida</div>
                    <ul class="list-group list-group-flush">
                        @forelse($data['dispensadoresCriticos'] ?? [] as $dispensador)
                            <li class="list-group-item">
                                <strong>{{ $dispensador->modelo }}</strong>:
                                Nivel bajo ({{ number_format($dispensador->nivel_comida_actual_kg, 1) }} Kg)
                            </li>
                        @empty
                            <li class="list-group-item text-muted">¡Todo bien! Los dispensadores están con buen nivel.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Cumplimiento del Plan --}}
            <div class="col-lg-4">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Plan Cumplido Hoy</h5>
                        <p class="display-5 fw-bold text-success">{{ $data['planCumplidoHoy'] ?? 0 }}%</p>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $data['planCumplidoHoy'] ?? 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- =============================================================== --}}
    {{-- GRÁFICOS DE RESUMEN --}}
    {{-- =============================================================== --}}
    <div class="row g-4 mt-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold"><i class="bi bi-graph-up me-2"></i>Alimentaciones de los últimos 7 días</div>
                <div class="card-body">
                    <canvas id="graficoAlimentaciones"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold"><i class="bi bi-diagram-3 me-2"></i>Estado de los Dispensadores</div>
                <div class="card-body">
                    <canvas id="graficoDispensadores"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ======== Gráfico de Alimentaciones (línea) ========
    new Chart(document.getElementById('graficoAlimentaciones'), {
        type: 'line',
        data: {
            labels: {!! json_encode($data['labelsFechas'] ?? ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom']) !!},
            datasets: [{
                label: 'Alimentaciones',
                data: {!! json_encode($data['valoresAlimentaciones'] ?? [3,5,2,7,4,6,8]) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            plugins: { legend: { display: false }},
            scales: { y: { beginAtZero: true }}
        }
    });

    // ======== Gráfico de Dispensadores (doughnut) ========
    new Chart(document.getElementById('graficoDispensadores'), {
        type: 'doughnut',
        data: {
            labels: ['Online', 'Offline', 'Mantenimiento'],
            datasets: [{
                data: {!! json_encode($data['estadoDispensadores'] ?? [5,2,1]) !!},
                backgroundColor: ['#198754', '#dc3545', '#ffc107'],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Estado de Dispensadores' }
            }
        }
    });
     document.addEventListener('DOMContentLoaded', () => {
        @if(isset($data['dispensadoresCriticos']) && count($data['dispensadoresCriticos']) > 0)
            if (Notification.permission === "granted") {
                @foreach($data['dispensadoresCriticos'] as $disp)
                    new Notification("⚠️ Nivel bajo en {{ $disp->modelo }}", {
                        body: "Nivel actual: {{ number_format($disp->nivel_comida_actual_kg, 1) }} Kg",
                        icon: "/images/ifish_icon.png"
                    });
                @endforeach
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        new Notification("🔔 Notificaciones activadas para iFish");
                    }
                });
            }
        @endif
    });
</script>
@endpush
