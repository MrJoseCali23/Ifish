@extends('layouts.app')

@section('title', 'Nuevo Horario de Alimentación - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-clock-history nav-icon me-2"></i> Nuevo Horario de Alimentación
        </h2>
        <a href="{{ route('horarios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('horarios.store') }}" id="horarioForm">
                @csrf

                {{-- 🔹 Selección de Dispensador --}}
                <div class="mb-3">
                    <label for="id_dispensador" class="form-label fw-bold">Dispensador</label>
                    <select class="form-select @error('id_dispensador') is-invalid @enderror" name="id_dispensador" id="id_dispensador" required>
                        <option value="" disabled selected>Selecciona un dispensador...</option>
                        @foreach($dispensadores as $disp)
                            <option value="{{ $disp->id_dispensador }}"
                                data-temp="{{ $disp->temperatura_agua }}"
                                data-nivel="{{ $disp->nivel_comida_actual_kg }}">
                                {{ $disp->modelo ?? 'Sin Modelo' }} ({{ $disp->estanque->nombre_estanque ?? 'Sin estanque' }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_dispensador')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 🔹 Selección de modo --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Modo de creación</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="modo_creacion" id="modo_manual" value="manual" checked>
                        <label class="form-check-label" for="modo_manual">Modo Manual (hora fija y cantidad definida)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="modo_creacion" id="modo_automatico" value="automatico">
                        <label class="form-check-label" for="modo_automatico">Modo Automático (calcula cantidad según biomasa)</label>
                    </div>
                </div>

                {{-- 🟢 MODO MANUAL --}}
                <div id="seccion_manual">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="hora_programada" class="form-label fw-bold">Hora programada</label>
                            <input type="time" name="hora_programada" id="hora_programada" class="form-control @error('hora_programada') is-invalid @enderror">
                            @error('hora_programada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="cantidad_gramos" class="form-label fw-bold">Cantidad (gramos)</label>
                            <input type="number" name="cantidad_gramos" id="cantidad_gramos" class="form-control @error('cantidad_gramos') is-invalid @enderror" min="1" placeholder="Ej: 200">
                            @error('cantidad_gramos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- 🔵 MODO AUTOMÁTICO --}}
                <div id="seccion_automatico" class="mt-4" style="display:none;">
                    <h5 class="text-primary fw-bold"><i class="bi bi-calculator me-1"></i> Cálculo por Biomasa</h5>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Número de peces</label>
                            <input type="number" id="num_peces" class="form-control" min="1" placeholder="Ej: 300">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Peso promedio por pez (g)</label>
                            <input type="number" id="peso_promedio" class="form-control" min="1" placeholder="Ej: 250">
                            <small class="text-muted">Peso estimado individual de cada pez.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Temperatura actual (°C)</label>
                            <div class="input-group">
                                <input type="number" id="temperatura" class="form-control" step="0.1" readonly placeholder="Esperando sensor...">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-thermometer-half"></i></span>
                            </div>
                            <small class="text-muted">Lectura automática del sensor.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Frecuencia diaria</label>
                            <input type="number" id="frecuencia" class="form-control" min="1" max="6" placeholder="Ej: 4">
                            <small class="text-muted">Veces al día que alimentará.</small>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-outline-primary" id="btnCalcular">
                            <i class="bi bi-calculator me-1"></i> Calcular Cantidad Sugerida
                        </button>
                    </div>

                    <div id="resultado_calculo" class="alert alert-info mt-3" style="display:none;"></div>
                </div>

                <div class="mt-4 text-end">
                    <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Horario</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 📜 Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modoManual = document.getElementById('modo_manual');
            const modoAuto = document.getElementById('modo_automatico');
            const seccionManual = document.getElementById('seccion_manual');
            const seccionAuto = document.getElementById('seccion_automatico');
            const dispensadorSelect = document.getElementById('id_dispensador');
            const tempInput = document.getElementById('temperatura');
            const btnCalcular = document.getElementById('btnCalcular');
            const resultadoDiv = document.getElementById('resultado_calculo');
            const cantidadInput = document.getElementById('cantidad_gramos');

            // Mostrar/ocultar secciones según modo
            modoManual.addEventListener('change', () => {
                seccionManual.style.display = 'block';
                seccionAuto.style.display = 'none';
            });
            modoAuto.addEventListener('change', () => {
                seccionManual.style.display = 'none';
                seccionAuto.style.display = 'block';
            });

            // Autocompletar temperatura desde el dispensador
            dispensadorSelect.addEventListener('change', (e) => {
                const option = e.target.selectedOptions[0];
                if (option) {
                    const temp = option.dataset.temp;
                    tempInput.value = temp ? parseFloat(temp).toFixed(1) : '';
                }
            });

            // Calcular cantidad sugerida
            btnCalcular.addEventListener('click', () => {
                const peces = parseFloat(document.getElementById('num_peces').value);
                const peso = parseFloat(document.getElementById('peso_promedio').value);
                const temp = parseFloat(document.getElementById('temperatura').value);
                const frecuencia = parseInt(document.getElementById('frecuencia').value);

                if (!peces || !peso || !frecuencia) {
                    alert('Por favor, completa todos los campos para calcular.');
                    return;
                }

                // Calcular biomasa (en kg)
                const biomasa = (peces * peso) / 1000;

                // Determinar tasa según peso y temperatura
                let tasa = 0.02;
                if (peso < 10) tasa = 0.05;
                else if (peso < 100) tasa = 0.04;
                else if (peso < 500) tasa = 0.03;
                else tasa = 0.02;

                // Ajuste por temperatura
                if (!isNaN(temp)) {
                    if (temp < 15) tasa *= 0.8;
                    else if (temp > 25) tasa *= 1.1;
                }

                const totalKg = biomasa * tasa;
                const totalGramos = totalKg * 1000;
                const porHorario = totalGramos / frecuencia;

                resultadoDiv.style.display = 'block';
                resultadoDiv.innerHTML = `
                    🐟 <strong>Biomasa:</strong> ${biomasa.toFixed(2)} kg<br>
                    🌡️ <strong>Temperatura:</strong> ${temp ? temp.toFixed(1) : 'N/D'} °C<br>
                    🍽️ <strong>Tasa alimentación:</strong> ${(tasa * 100).toFixed(1)} %<br>
                    📦 <strong>Cantidad diaria:</strong> ${totalGramos.toFixed(0)} g<br>
                    🕒 <strong>Por horario:</strong> <span class="text-success fw-bold">${porHorario.toFixed(0)} g</span>
                `;

                cantidadInput.value = Math.round(porHorario);
            });
        });
    </script>
@endsection
