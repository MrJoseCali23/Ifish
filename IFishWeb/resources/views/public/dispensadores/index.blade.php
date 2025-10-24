@extends('layouts.app')
@section('title', 'Gestión de Dispensadores - iFish')

@section('content')
    {{-- Mensajes de éxito o error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-cpu-fill nav-icon me-2"></i>Gestión de Dispensadores
        </h2>
        @can('create', App\Models\Dispensador::class)
            <a href="{{ route('superadmin.dispensadores-inventario.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Dispensador
            </a>
        @endcan
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row g-4">
                @forelse ($dispensadores as $dispensadore)
                    <div class="col-12 col-md-6 col-lg-4 d-flex">
                        <div class="card shadow-sm h-100 w-100">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-cpu-fill me-2"></i>
                                    {{ $dispensadore->modelo ?? 'Sin Modelo' }}
                                </h5>

                                @php
                                    $isOnline = $dispensadore->ultimo_reporte &&
                                        \Carbon\Carbon::parse($dispensadore->ultimo_reporte)->diffInMinutes(now()) < 15;
                                @endphp
                                <span id="estado-{{ $dispensadore->id_dispensador }}">
                                    @if($isOnline)
                                        <span class="badge bg-success"><i class="bi bi-wifi me-1"></i> Online</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-wifi-off me-1"></i> Offline</span>
                                    @endif
                                </span>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <div>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <strong>Estanque:</strong>
                                            {{ $dispensadore->estanque->nombre_estanque ?? 'No asignado' }}
                                        </li>

                                        <li class="mb-2">
                                            <strong>Estado:</strong>
                                            @if($dispensadore->estado == 'Activo')
                                                <span class="badge bg-success">{{ $dispensadore->estado }}</span>
                                            @elseif($dispensadore->estado == 'Inactivo')
                                                <span class="badge bg-secondary">{{ $dispensadore->estado }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $dispensadore->estado }}</span>
                                            @endif
                                        </li>

                                        <li class="mb-2">
                                            <strong>Tipo de Comida:</strong>
                                            {{ $dispensadore->tipoComidaActual->nombre_comida ?? 'No asignada' }}
                                        </li>

                                        {{-- ▼ Temperatura con estilo en vivo ▼ --}}
                                        <li class="mb-2">
                                            <strong>Temp. Agua:</strong>
                                            <span id="temp-{{ $dispensadore->id_dispensador }}" class="live-temp fw-bold fs-5 text-danger">
                                                {{ number_format($dispensadore->temperatura_agua, 1) }} °C
                                            </span>
                                            <span class="badge bg-danger blink ms-2">● En vivo</span>
                                        </li>
                                    </ul>

                                    <div class="mt-3">
                                        <p class="mb-1">
                                            <strong>Horarios:</strong> {{ $dispensadore->horarios_count ?? 0 }} programados
                                        </p>
                                    </div>
                                </div>

                                {{-- ▼ Nivel de comida con barra de progreso ▼ --}}
                                <div class="mt-auto pt-3">
                                    <label class="form-label d-block mb-1">
                                        <strong>Nivel de Comida:</strong>
                                        {{ number_format($dispensadore->nivel_comida_actual_kg, 2) }} Kg
                                    </label>
                                    @php
                                        $capacidad_max_kg = 1;
                                        $porcentaje = ($capacidad_max_kg > 0) ? ($dispensadore->nivel_comida_actual_kg / $capacidad_max_kg) * 100 : 0;
                                        $color_barra = $porcentaje > 20 ? 'bg-success' : 'bg-danger';
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div id="nivel-{{ $dispensadore->id_dispensador }}"
                                             class="progress-bar progress-bar-striped {{ $color_barra }}"
                                             role="progressbar"
                                             style="width: {{ $porcentaje }}%;">
                                            {{ round($porcentaje) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Último reporte:
                                        {{ $dispensadore->ultimo_reporte ? \Carbon\Carbon::parse($dispensadore->ultimo_reporte)->diffForHumans() : 'Nunca' }}
                                    </small>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                @can('manualFeed', $dispensadore)
                                    <button type="button" class="btn btn-sm btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#manualFeedModal{{ $dispensadore->id_dispensador }}"
                                            title="Alimentación Manual">
                                        <i class="bi bi-send-fill"></i>
                                    </button>
                                @endcan

                                @can('update', $dispensadore)
                                    <a href="{{ route('dispensadores.edit', $dispensadore) }}"
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            No tienes dispensadores asignados a tu criadero.
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($dispensadores->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $dispensadores->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ▼ Estilos para temperatura y "en vivo" ▼ --}}
    <style>
        .live-temp {
            font-size: 1.8rem;
            color: #e63946;
            font-weight: 700;
            text-shadow: 0 0 5px rgba(230, 57, 70, 0.6);
        }
        .blink {
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 0.9; }
            50% { opacity: 0.4; }
        }
    </style>

    {{-- ▼ Script de actualización automática cada 15 segundos ▼ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(() => {
                fetch("{{ route('dispensadores.data') }}")
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(d => {
                            const tempSpan = document.querySelector(`#temp-${d.id_dispensador}`);
                            const estadoSpan = document.querySelector(`#estado-${d.id_dispensador}`);
                            const nivelBar = document.querySelector(`#nivel-${d.id_dispensador}`);

                            // Temperatura
                            if (tempSpan) {
                                const temp = parseFloat(d.temperatura_agua ?? 0).toFixed(1);
                                tempSpan.innerHTML = `${temp} °C`;
                                // 🔥 color dinámico
                                if (temp > 30) tempSpan.style.color = '#ff4d4f';
                                else if (temp < 18) tempSpan.style.color = '#007bff';
                                else tempSpan.style.color = '#e63946';
                            }

                            // Estado Online/Offline
                            if (estadoSpan) {
                                const minutos = (Date.now() - new Date(d.ultimo_reporte)) / 60000;
                                estadoSpan.innerHTML = minutos < 15 
                                    ? '<span class="badge bg-success"><i class="bi bi-wifi me-1"></i> Online</span>' 
                                    : '<span class="badge bg-secondary"><i class="bi bi-wifi-off me-1"></i> Offline</span>';
                            }

                            // Nivel de comida
                            if (nivelBar) {
                                const porcentaje = Math.min((d.nivel_comida_actual_kg / 1) * 100, 100);
                                nivelBar.style.width = `${porcentaje}%`;
                                nivelBar.textContent = `${Math.round(porcentaje)}%`;
                                nivelBar.classList.toggle('bg-success', porcentaje > 20);
                                nivelBar.classList.toggle('bg-danger', porcentaje <= 20);
                            }
                        });
                    });
            }, 15000);
        });
    </script>
@endsection

@push('modals')
    @foreach ($dispensadores as $dispensadore)
        <div class="modal fade" id="manualFeedModal{{ $dispensadore->id_dispensador }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Alimentación Manual: {{ $dispensadore->modelo }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form method="POST" action="{{ route('dispensadores.manualFeed', $dispensadore) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Comida Cargada</label>
                                <input type="text" class="form-control"
                                       value="{{ $dispensadore->tipoComidaActual->nombre_comida ?? 'No asignada' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="cantidad_dispensada_gramos_{{ $dispensadore->id_dispensador }}" class="form-label">
                                    Cantidad a Dispensar (en gramos)
                                </label>
                                <input type="number" class="form-control"
                                       id="cantidad_dispensada_gramos_{{ $dispensadore->id_dispensador }}"
                                       name="cantidad_dispensada_gramos" required min="1" placeholder="Ej: 150">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Dispensar Manualmente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endpush
