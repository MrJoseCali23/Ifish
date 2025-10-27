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
            <!-- Campo oculto para cantidad_gramos -->
            <input type="hidden" name="cantidad_gramos" id="cantidad_gramos_hidden" required>

            <div class="mb-3">
                <label for="id_dispensador" class="form-label fw-bold">Dispensador</label>
                <select class="form-select @error('id_dispensador') is-invalid @enderror" name="id_dispensador" id="id_dispensador" required>
                    <option value="" disabled selected>Selecciona un dispensador...</option>
                    @foreach($dispensadores as $disp)
                        <option value="{{ $disp->id_dispensador }}" data-temp="{{ $disp->temperatura_agua }}" data-nivel="{{ $disp->nivel_comida_actual_kg * 1000 }}">
                            {{ $disp->modelo ?? 'Sin Modelo' }} ({{ $disp->estanque->nombre_estanque ?? 'Sin estanque' }})
                        </option>
                    @endforeach
                </select>
                @error('id_dispensador')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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

            <div id="seccion_manual">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="hora_programada" class="form-label fw-bold">Hora programada</label>
                        <input type="time" name="hora_programada" id="hora_programada" class="form-control @error('hora_programada') is-invalid @enderror" required>
                        @error('hora_programada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="cantidad_gramos_manual" class="form-label fw-bold">Cantidad (gramos)</label>
                        <select id="cantidad_gramos_manual" class="form-select @error('cantidad_gramos') is-invalid @enderror">
                            <option value="" disabled selected>Selecciona una cantidad...</option>
                            <!-- Opciones se llenan con JavaScript -->
                        </select>
                        @error('cantidad_gramos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div id="seccion_automatico" class="mt-4" style="display:none;">
                <h5 class="text-primary fw-bold"><i class="bi bi-calculator me-1"></i> Cálculo por Biomasa</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Número de peces</label>
                        <input type="number" id="num_peces" name="num_peces" class="form-control" min="1" placeholder="Ej: 300">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Peso promedio por pez (g)</label>
                        <input type="number" id="peso_promedio" name="peso_promedio" class="form-control" min="1" placeholder="Ej: 250">
                        <small class="text-muted">Peso estimado individual de cada pez.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Temperatura actual (°C)</label>
                        <div class="input-group">
                            <input type="number" id="temperatura" name="temperatura" class="form-control" step="0.1" readonly placeholder="Esperando sensor...">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-thermometer-half"></i></span>
                        </div>
                        <small class="text-muted">Lectura automática del sensor.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Frecuencia diaria</label>
                        <input type="number" id="frecuencia" name="frecuencia" class="form-control" min="1" max="6" placeholder="Ej: 4">
                        <small class="text-muted">Veces al día que alimentará.</small>
                    </div>
                    <!-- Campos ocultos para hora_inicio y hora_fin -->
                    <input type="hidden" name="hora_inicio" id="hora_inicio" value="08:00" required>
                    <input type="hidden" name="hora_fin" id="hora_fin" value="20:00" required>
                    <div class="col-md-4">
                        <label for="cantidad_gramos_auto" class="form-label fw-bold">Cantidad por horario (gramos)</label>
                        <select id="cantidad_gramos_auto" class="form-select @error('cantidad_gramos') is-invalid @enderror">
                            <option value="" disabled selected>Selecciona una cantidad...</option>
                            <!-- Opciones se llenan con JavaScript -->
                        </select>
                        @error('cantidad_gramos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                <button type="submit" class="btn btn-primary" id="submitBtn">Guardar Horario</button>
            </div>
        </form>
    </div>
</div>

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
    const cantidadManual = document.getElementById('cantidad_gramos_manual');
    const cantidadAuto = document.getElementById('cantidad_gramos_auto');
    const cantidadHidden = document.getElementById('cantidad_gramos_hidden');
    const horaProgramada = document.getElementById('hora_programada');
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const frecuencia = document.getElementById('frecuencia');
    const numPeces = document.getElementById('num_peces');
    const pesoPromedio = document.getElementById('peso_promedio');
    const form = document.getElementById('horarioForm');

    // Cambiar entre modos y deshabilitar campos no relevantes
    function toggleModo() {
        if (modoManual.checked) {
            seccionManual.style.display = 'block';
            seccionAuto.style.display = 'none';
            cantidadManual.disabled = false;
            cantidadAuto.disabled = true;
            horaProgramada.disabled = false;
            horaInicio.disabled = true;
            horaFin.disabled = true;
            frecuencia.disabled = true;
            numPeces.disabled = true;
            pesoPromedio.disabled = true;
            // Limpiar campos de modo automático
            numPeces.value = '';
            pesoPromedio.value = '';
            frecuencia.value = '';
        } else {
            seccionManual.style.display = 'none';
            seccionAuto.style.display = 'block';
            cantidadManual.disabled = true;
            cantidadAuto.disabled = false;
            horaProgramada.disabled = true;
            horaInicio.disabled = false;
            horaFin.disabled = false;
            frecuencia.disabled = false;
            numPeces.disabled = false;
            pesoPromedio.disabled = false;
        }
    }

    modoManual.addEventListener('change', toggleModo);
    modoAuto.addEventListener('change', toggleModo);

    // Ejecutar al cargar para establecer el estado inicial
    toggleModo();

    // Inicializar los <select> con opciones por defecto
    const nivelDefault = 1000; // 1 kg por defecto
    [cantidadManual, cantidadAuto].forEach(select => {
        select.innerHTML = '<option value="" disabled selected>Selecciona una cantidad...</option>';
        for (let i = 10; i <= nivelDefault; i += 10) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `${i} g`;
            select.appendChild(option);
        }
    });

    // Actualizar temperatura y opciones de cantidad según dispensador
    dispensadorSelect.addEventListener('change', (e) => {
        const option = e.target.selectedOptions[0];
        if (option) {
            const temp = option.dataset.temp;
            const nivelGramos = parseInt(option.dataset.nivel) || 1000;
            tempInput.value = temp ? parseFloat(temp).toFixed(1) : '';

            [cantidadManual, cantidadAuto].forEach(select => {
                select.innerHTML = '<option value="" disabled selected>Selecciona una cantidad...</option>';
                for (let i = 10; i <= Math.min(nivelGramos, 1000); i += 10) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = `${i} g`;
                    select.appendChild(option);
                }
            });
        }
    });

    // Cálculo automático
    btnCalcular.addEventListener('click', () => {
        const peces = parseFloat(numPeces.value);
        const peso = parseFloat(pesoPromedio.value);
        const temp = parseFloat(tempInput.value);
        const frec = parseInt(frecuencia.value);
        const nivelGramos = parseInt(dispensadorSelect.selectedOptions[0]?.dataset.nivel) || 1000;

        if (!peces || !peso || !frec) {
            alert('Por favor, completa número de peces, peso promedio y frecuencia.');
            return;
        }

        const biomasa = (peces * peso) / 1000;
        let tasa = 0.02;
        if (peso < 10) tasa = 0.05;
        else if (peso < 100) tasa = 0.04;
        else if (peso < 500) tasa = 0.03;
        else tasa = 0.02;

        if (!isNaN(temp)) {
            if (temp < 15) tasa *= 0.8;
            else if (temp > 25) tasa *= 1.1;
        }

        const totalKg = biomasa * tasa;
        const totalGramos = totalKg * 1000;
        const porHorario = totalGramos / frec;
        const porHorarioRedondeado = Math.round(porHorario / 10) * 10;
        const porHorarioFinal = Math.min(porHorarioRedondeado, nivelGramos);

        resultadoDiv.style.display = 'block';
        resultadoDiv.innerHTML = `
            🐟 <strong>Biomasa:</strong> ${biomasa.toFixed(2)} kg<br>
            🌡️ <strong>Temperatura:</strong> ${temp ? temp.toFixed(1) : 'N/D'} °C<br>
            🍽️ <strong>Tasa alimentación:</strong> ${(tasa * 100).toFixed(1)} %<br>
            📦 <strong>Cantidad diaria:</strong> ${totalGramos.toFixed(0)} g<br>
            🕒 <strong>Por horario:</strong> <span class="text-success fw-bold">${porHorarioFinal} g</span>
        `;
        cantidadAuto.value = porHorarioFinal;
    });

    // Antes de enviar el formulario, copiar el valor del <select> visible
    form.addEventListener('submit', (e) => {
        if (modoManual.checked) {
            cantidadHidden.value = cantidadManual.value;
            horaProgramada.disabled = false;
            horaInicio.disabled = true;
            horaFin.disabled = true;
            frecuencia.disabled = true;
            numPeces.disabled = true;
            pesoPromedio.disabled = true;
            // Limpiar campos de modo automático
            numPeces.value = '';
            pesoPromedio.value = '';
            frecuencia.value = '';
        } else {
            cantidadHidden.value = cantidadAuto.value;
            horaProgramada.disabled = true;
            horaInicio.disabled = false;
            horaFin.disabled = false;
            frecuencia.disabled = false;
            numPeces.disabled = false;
            pesoPromedio.disabled = false;
        }

        if (!cantidadHidden.value) {
            e.preventDefault();
            alert('Por favor, selecciona una cantidad de gramos.');
            return;
        }

        if (modoAuto.checked) {
            if (!numPeces.value || !pesoPromedio.value || !frecuencia.value) {
                e.preventDefault();
                alert('Por favor, completa número de peces, peso promedio y frecuencia en modo automático.');
                return;
            }
        }

        console.log('Formulario enviado');
        console.log('Datos:', new FormData(form));
    });
});
</script>
@endsection