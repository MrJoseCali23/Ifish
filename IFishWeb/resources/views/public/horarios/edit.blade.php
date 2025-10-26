@extends('layouts.app')

@section('title', 'Editar Horario de Alimentación - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-clock-history nav-icon me-2"></i> Editar Horario de Alimentación
        </h2>
        <a href="{{ route('horarios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-lg">
        <div class="card-body">
            <form method="POST" action="{{ route('horarios.update', $horario) }}" id="editHorarioForm">
                @csrf
                @method('PUT')

                {{-- 🔹 Dispensador --}}
                <div class="mb-4">
                    <label for="id_dispensador" class="form-label fw-bold">Dispensador</label>
                    <select class="form-select" name="id_dispensador" id="id_dispensador" required>
                        @foreach ($dispensadores as $disp)
                            <option value="{{ $disp->id_dispensador }}"
                                data-nivel="{{ $disp->nivel_comida_actual_kg }}"
                                data-temp="{{ $disp->temperatura_agua }}"
                                {{ $disp->id_dispensador == $horario->id_dispensador ? 'selected' : '' }}>
                                {{ $disp->modelo ?? 'Sin Modelo' }} — Estanque: {{ $disp->estanque->nombre_estanque ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 🔹 Hora programada --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="hora_programada" class="form-label fw-bold">Hora programada</label>
                        <input type="time" id="hora_programada" name="hora_programada"
                               class="form-control @error('hora_programada') is-invalid @enderror"
                               value="{{ old('hora_programada', \Carbon\Carbon::parse($horario->hora_programada)->format('H:i')) }}"
                               required>
                        @error('hora_programada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 🔹 Cantidad --}}
                    <div class="col-md-4">
                        <label for="cantidad_gramos" class="form-label fw-bold">Cantidad (gramos)</label>
                        <input type="number" id="cantidad_gramos" name="cantidad_gramos"
                               class="form-control @error('cantidad_gramos') is-invalid @enderror"
                               min="1"
                               value="{{ old('cantidad_gramos', $horario->cantidad_gramos) }}"
                               placeholder="Ej: 200"
                               required>
                        @error('cantidad_gramos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">No debe exceder el nivel de comida actual.</small>
                    </div>

                    {{-- 🔹 Nivel actual del dispensador --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Nivel de comida actual (Kg)</label>
                        <input type="text" id="nivel_actual" class="form-control bg-light" readonly>
                    </div>
                </div>

                {{-- 🔹 Temperatura --}}
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Temperatura actual (°C)</label>
                        <div class="input-group">
                            <input type="number" id="temperatura_actual" class="form-control bg-light" readonly>
                            <span class="input-group-text bg-primary text-white"><i class="bi bi-thermometer-half"></i></span>
                        </div>
                    </div>

                    {{-- 🔹 Estado del horario --}}
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo"
                                   {{ old('activo', $horario->activo) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="activo">Activo</label>
                        </div>
                    </div>
                </div>

                {{-- 🔹 Botones --}}
                <div class="text-end mt-4">
                    <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ⚙️ Script dinámico --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dispensadorSelect = document.getElementById('id_dispensador');
            const nivelInput = document.getElementById('nivel_actual');
            const tempInput = document.getElementById('temperatura_actual');
            const cantidadInput = document.getElementById('cantidad_gramos');
            const horaInput = document.getElementById('hora_programada');

            // Inicializar con valores del dispensador actual
            actualizarDatos();

            dispensadorSelect.addEventListener('change', actualizarDatos);

            function actualizarDatos() {
                const option = dispensadorSelect.selectedOptions[0];
                if (option) {
                    nivelInput.value = parseFloat(option.dataset.nivel || 0).toFixed(2);
                    tempInput.value = parseFloat(option.dataset.temp || 0).toFixed(1);
                }
            }

            // Validar cantidad en tiempo real
            cantidadInput.addEventListener('input', () => {
                const nivel = parseFloat(nivelInput.value);
                const cantidadKg = cantidadInput.value / 1000;

                if (cantidadKg > nivel) {
                    cantidadInput.classList.add('is-invalid');
                    cantidadInput.setCustomValidity('Supera el nivel de comida actual del dispensador.');
                } else {
                    cantidadInput.classList.remove('is-invalid');
                    cantidadInput.setCustomValidity('');
                }
            });

            // Validar hora pasada (solo aviso visual)
            horaInput.addEventListener('change', () => {
                const now = new Date();
                const [h, m] = horaInput.value.split(':');
                const horaSeleccionada = new Date();
                horaSeleccionada.setHours(h, m, 0, 0);

                if (horaSeleccionada < now) {
                    horaInput.classList.add('is-invalid');
                    horaInput.setCustomValidity('No puedes programar una hora pasada.');
                } else {
                    horaInput.classList.remove('is-invalid');
                    horaInput.setCustomValidity('');
                }
            });
        });
    </script>
@endsection
