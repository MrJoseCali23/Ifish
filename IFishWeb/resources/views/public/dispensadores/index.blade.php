@extends('layouts.app')
@section('title', 'Gestión de Dispensadores - iFish')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold">
                <i class="bi bi-cpu-fill me-2"></i>Gestión de Dispensadores
            </h2>
            <small class="text-muted" id="last-update">Última actualización: ahora</small>
        </div>
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
                                    <i class="bi bi-cpu-fill me-2"></i>{{ $dispensadore->modelo ?? 'Sin Modelo' }}
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
                                    <ul class="list-unstyled mb-3">
                                        <li><strong>Estanque:</strong> {{ $dispensadore->estanque->nombre_estanque ?? 'No asignado' }}</li>
                                        <li>
                                            <strong>Estado:</strong>
                                            @if($dispensadore->estado == 'Activo')
                                                <span class="badge bg-success">{{ $dispensadore->estado }}</span>
                                            @elseif($dispensadore->estado == 'Inactivo')
                                                <span class="badge bg-secondary">{{ $dispensadore->estado }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $dispensadore->estado }}</span>
                                            @endif
                                        </li>
                                        <li><strong>Tipo de Comida:</strong> {{ $dispensadore->tipoComidaActual->nombre_comida ?? 'No asignada' }}</li>
                                        <li>
                                            <strong>Temp. Agua:</strong>
                                            <span id="temp-{{ $dispensadore->id_dispensador }}" class="live-temp fw-bold fs-5">
                                                {{ number_format($dispensadore->temperatura_agua, 1) }} °C
                                            </span>
                                            <span class="ms-2 text-success live-dot">● En vivo</span>
                                        </li>
                                    </ul>
                                    <p class="mb-1"><strong>Horarios:</strong> {{ $dispensadore->horarios_count ?? 0 }} programados</p>
                                </div>

                                <div class="mt-auto pt-3">
                                    <label class="form-label d-block mb-1"><strong>Nivel de Comida:</strong>
                                        {{ number_format($dispensadore->nivel_comida_actual_kg, 2) }} Kg
                                    </label>
                                    @php
                                        $capacidad_max_kg = 0.50;
                                        $porcentaje = ($dispensadore->nivel_comida_actual_kg / $capacidad_max_kg) * 100;
                                        $color_barra = $porcentaje > 20 ? 'bg-success' : 'bg-danger';
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div id="nivel-{{ $dispensadore->id_dispensador }}"
                                             class="progress-bar progress-bar-striped {{ $color_barra }}"
                                             role="progressbar" style="width: {{ $porcentaje }}%;">
                                            {{ round($porcentaje) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Último reporte: {{ $dispensadore->ultimo_reporte ? \Carbon\Carbon::parse($dispensadore->ultimo_reporte)->diffForHumans() : 'Nunca' }}
                                    </small>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                @can('manualFeed', $dispensadore)
                                    <button type="button" class="btn btn-sm btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#manualFeedModal{{ $dispensadore->id_dispensador }}">
                                        <i class="bi bi-send-fill"></i>
                                    </button>
                                @endcan
                                @can('update', $dispensadore)
                                    <a href="{{ route('dispensadores.edit', $dispensadore) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">No tienes dispensadores asignados.</div>
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

    <style>
        .live-temp {
            color: #e63946;
            font-weight: 700;
            text-shadow: 0 0 6px rgba(230, 57, 70, 0.5);
        }
        .live-dot {
            font-weight: 600;
            animation: blink 1.4s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lastUpdate = document.getElementById('last-update');

            async function actualizarDatos() {
                try {
                    const res = await fetch("{{ route('dispensadores.data') }}");
                    const data = await res.json();

                    data.forEach(d => {
                        const temp = document.getElementById(`temp-${d.id_dispensador}`);
                        const estado = document.getElementById(`estado-${d.id_dispensador}`);
                        const nivel = document.getElementById(`nivel-${d.id_dispensador}`);

                        if (temp) {
                            const t = parseFloat(d.temperatura_agua ?? 0).toFixed(1);
                            temp.textContent = `${t} °C`;
                            temp.style.color = t > 30 ? '#ff4d4f' : (t < 18 ? '#007bff' : '#e63946');
                        }

                        if (estado) {
                            const minutos = (Date.now() - new Date(d.ultimo_reporte)) / 60000;
                            estado.innerHTML = minutos < 15
                                ? '<span class="badge bg-success"><i class="bi bi-wifi me-1"></i> Online</span>'
                                : '<span class="badge bg-secondary"><i class="bi bi-wifi-off me-1"></i> Offline</span>';
                        }

                        if (nivel) {
                            const porcentaje = Math.min((d.nivel_comida_actual_kg / 1) * 100, 100);
                            nivel.style.width = `${porcentaje}%`;
                            nivel.textContent = `${Math.round(porcentaje)}%`;
                            nivel.classList.toggle('bg-success', porcentaje > 20);
                            nivel.classList.toggle('bg-danger', porcentaje <= 20);
                        }

                        // Actualizar opciones del select en el modal
                        const cantidadSelect = document.getElementById(`cantidad_dispensada_gramos_${d.id_dispensador}`);
                        if (cantidadSelect) {
                            const nivelGramos = parseInt((d.nivel_comida_actual_kg || 1) * 1000);
                            cantidadSelect.innerHTML = '<option value="" disabled selected>Selecciona una cantidad...</option>';
                            for (let i = 10; i <= Math.min(nivelGramos, 10000); i += 10) {
                                const option = document.createElement('option');
                                option.value = i;
                                option.textContent = `${i} g`;
                                cantidadSelect.appendChild(option);
                            }
                        }
                    });

                    lastUpdate.textContent = "Última actualización: " + new Date().toLocaleTimeString();
                } catch (e) {
                    console.error("Error al actualizar datos:", e);
                }
            }

            actualizarDatos();
            setInterval(actualizarDatos, 15000);
        });
    </script>
@endsection

@push('modals')
    @foreach ($dispensadores as $dispensadore)
        <div class="modal fade" id="manualFeedModal{{ $dispensadore->id_dispensador }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-basket me-2"></i> Alimentación Manual
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                                <select class="form-select @error('cantidad_dispensada_gramos') is-invalid @enderror"
                                        id="cantidad_dispensada_gramos_{{ $dispensadore->id_dispensador }}"
                                        name="cantidad_dispensada_gramos" required>
                                    <option value="" disabled selected>Selecciona una cantidad...</option>
                                    <!-- Opciones se llenan con JavaScript -->
                                </select>
                                @error('cantidad_dispensada_gramos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">No debe exceder {{ $dispensadore->nivel_comida_actual_kg * 1000 }} g.</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Dispensar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endpush