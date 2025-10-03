@extends('layouts.app')

@section('title', 'Nuevo Horario de Alimentación - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevos Horarios</h2>
    <p class="text-muted">Selecciona un dispensador para programar. El sistema usará automáticamente el tipo de comida que tiene asignado.</p>

    <form method="POST" action="{{ route('horarios.store') }}">
        @csrf
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">1. Selecciona el Dispensador</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="id_dispensador" class="form-label">Dispensador</label>
                        <select class="form-select @error('id_dispensador') is-invalid @enderror" id="id_dispensador" name="id_dispensador" required>
                            <option value="">Selecciona un dispensador preparado...</option>
                            @foreach ($dispensadores as $dispensador)
                                <option value="{{ $dispensador->id_dispensador }}" data-comida="{{ $dispensador->tipoComidaActual->nombre_comida ?? 'No asignada' }}">
                                    {{ $dispensador->modelo }} (Estanque: {{ $dispensador->estanque->nombre_comida ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_dispensador') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- ▼▼▼ CAMPO DE SOLO LECTURA PARA MOSTRAR LA COMIDA ▼▼▼ --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de Comida Asignada</label>
                        <input type="text" id="tipoComidaAsignada" class="form-control" value="Selecciona un dispensador" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="mb-0">2. Define la Cantidad y el Modo de Creación</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="cantidad_gramos" class="form-label">Cantidad a Dispensar (en gramos, por cada vez)</label>
                    <input type="number" class="form-control @error('cantidad_gramos') is-invalid @enderror" id="cantidad_gramos" name="cantidad_gramos" value="{{ old('cantidad_gramos') }}" required min="1">
                    @error('cantidad_gramos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modo_creacion" id="modoManual" value="manual" checked>
                    <label class="form-check-label" for="modoManual">Modo Manual</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modo_creacion" id="modoAutomatico" value="automatico">
                    <label class="form-check-label" for="modoAutomatico">Modo Automático</label>
                </div>

                <div id="camposManual" class="mt-3">
                    <label for="hora_programada" class="form-label">Hora Específica</label>
                    <input type="time" class="form-control" name="hora_programada">
                </div>

                <div id="camposAutomatico" class="mt-3 d-none">
                    <div class="row g-3">
                        <div class="col-md-4"><label for="hora_inicio" class="form-label">Desde las:</label><input type="time" name="hora_inicio" class="form-control" value="08:00"></div>
                        <div class="col-md-4"><label for="hora_fin" class="form-label">Hasta las:</label><input type="time" name="hora_fin" class="form-control" value="18:00"></div>
                        <div class="col-md-4"><label for="frecuencia" class="form-label">Frecuencia:</label><input type="number" name="frecuencia" class="form-control" value="3" min="2"><small class="text-muted">veces al día</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary btn-lg">Crear Horario(s)</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Script para mostrar el tipo de comida del dispensador seleccionado
    const selectDispensador = document.getElementById('id_dispensador');
    const inputComida = document.getElementById('tipoComidaAsignada');

    selectDispensador.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const nombreComida = selectedOption.getAttribute('data-comida');
        inputComida.value = nombreComida;
    });

    // Script para cambiar entre modo manual y automático
    const modoManualRadio = document.getElementById('modoManual');
    const modoAutomaticoRadio = document.getElementById('modoAutomatico');
    const camposManual = document.getElementById('camposManual');
    const camposAutomatico = document.getElementById('camposAutomatico');

    modoManualRadio.addEventListener('change', () => {
        if (modoManualRadio.checked) {
            camposManual.classList.remove('d-none');
            camposAutomatico.classList.add('d-none');
        }
    });

    modoAutomaticoRadio.addEventListener('change', () => {
        if (modoAutomaticoRadio.checked) {
            camposManual.classList.add('d-none');
            camposAutomatico.classList.remove('d-none');
        }
    });
</script>
@endpush
