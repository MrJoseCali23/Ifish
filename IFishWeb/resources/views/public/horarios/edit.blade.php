@extends('layouts.app')
@section('title', 'Editar Horario de Alimentación')
@section('content')
    <h2 class="text-primary fw-bold">Editando Horario</h2>
    <p class="text-muted">Modifica los detalles de este horario. El tipo de comida se actualizará automáticamente según el dispensador que elijas.</p>

    <form method="POST" action="{{ route('horarios.update', $horario) }}">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">1. Configuración del Dispensador y Cantidad</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_dispensador" class="form-label">Dispensador</label>
                        <select class="form-select" id="id_dispensador" name="id_dispensador" required>
                            @foreach ($dispensadores as $dispensador)
                                <option value="{{ $dispensador->id_dispensador }}" 
                                        data-comida="{{ $dispensador->tipoComidaActual->nombre_comida ?? 'No asignada' }}"
                                        @if($horario->id_dispensador == $dispensador->id_dispensador) selected @endif>
                                    {{ $dispensador->modelo }} (Estanque: {{ $dispensador->estanque->nombre_estanque ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Comida Asignada</label>
                        <input type="text" id="tipoComidaAsignada" class="form-control" value="{{ $horario->dispensador->tipoComidaActual->nombre_comida ?? 'No asignada' }}" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="cantidad_gramos" class="form-label">Cantidad a Dispensar (gramos)</label>
                    <input type="number" class="form-control" id="cantidad_gramos" name="cantidad_gramos" value="{{ $horario->cantidad_gramos }}" required min="1">
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="mb-0">2. Hora y Estado</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hora_programada" class="form-label">Hora Específica</glabel>
                        <input type="time" class="form-control" name="hora_programada" value="{{ \Carbon\Carbon::parse($horario->hora_programada)->format('H:i') }}" required>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" @if($horario->activo) checked @endif>
                            <label class="form-check-label" for="activo">Horario Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Horario</button>
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
</script>
@endpush
