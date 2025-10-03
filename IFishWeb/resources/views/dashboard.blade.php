@extends('layouts.app')

@section('title', 'Dashboard - iFish')

@section('content')
    {{-- =============================================================== --}}
    {{-- PANEL DE CONTROL PARA EL SUPER ADMIN --}}
    {{-- =============================================================== --}}
    @if (Auth::user()->rol === 'Admin')
        <h2 class="text-primary fw-bold">👋¡Hola, {{ Auth::user()->name }}!</h2>
        <p class="text-muted">Aquí tienes un resumen del estado global de la plataforma iFish.</p>

        <div class="row g-4 mt-3">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Criaderos Activos</h5>
                        <p class="card-text display-4 fw-bold text-primary">{{ $data['totalCriaderosActivos'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Salud de la Plataforma</h5>
                        <p class="card-text display-4 fw-bold text-success">{{ $data['saludPlataforma'] }}%</p>
                        <small class="text-muted">% de dispensadores online</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Alimentaciones Totales (Hoy)</h5>
                        <p class="card-text display-4 fw-bold text-info">{{ $data['totalAlimentacionesHoy'] }}</p>
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
                        @forelse($data['proximosHorarios'] as $horario)
                            <li class="list-group-item">
                                <strong>{{ \Carbon\Carbon::parse($horario->hora_programada)->format('h:i A') }}</strong> - {{ $horario->dispensador->modelo }}
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
                         @forelse($data['dispensadoresCriticos'] as $dispensador)
                            <li class="list-group-item">
                                <strong>{{ $dispensador->modelo }}</strong>: Nivel bajo ({{ number_format($dispensador->nivel_comida_actual_kg, 1) }} Kg)
                            </li>
                        @empty
                            <li class="list-group-item text-muted">¡Buenas noticias! Todos los dispensadores tienen buen nivel de comida.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Cumplimiento del Plan --}}
            <div class="col-lg-4">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Plan Cumplido Hoy</h5>
                        <p class="display-4 fw-bold text-success">{{ $data['planCumplidoHoy'] }}%</p>
                        <div class="progress" style="height: 5px;"><div class="progress-bar bg-success" role="progressbar" style="width: {{ $data['planCumplidoHoy'] }}%;"></div></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
