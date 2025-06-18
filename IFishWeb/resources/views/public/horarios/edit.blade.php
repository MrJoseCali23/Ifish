@extends('layouts.app')

@section('title', 'Editar Horario - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Editando Horario de Alimentación</h2>

    <div class="card shadow glass-effect mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('horarios.update', $horario) }}">
                @csrf
                @method('PUT')

                {{-- ... Los campos para Dispensador, Tipo de Comida, Hora y Cantidad son casi idénticos al formulario de create.blade.php, pero usando $horario para pre-seleccionar los valores. --}}

                <div class="mb-3">
                    <label for="id_dispensador" class="form-label">Dispensador</label>
                    <select class="form-select" id="id_dispensador" name="id_dispensador" required>
                        @foreach ($dispensadores as $dispensador)
                            <option value="{{ $dispensador->id_dispensador }}" @if($horario->id_dispensador == $dispensador->id_dispensador) selected @endif>
                                MAC: {{ $dispensador->mac_address }} (Estanque: {{ $dispensador->estanque->nombre_estanque ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_tipo_comida" class="form-label">Tipo de Comida</label>
                    <select class="form-select" id="id_tipo_comida" name="id_tipo_comida" required>
                        @foreach ($tipos_comida as $tipo)
                            <option value="{{ $tipo->id_tipo_comida }}" @if($horario->id_tipo_comida == $tipo->id_tipo_comida) selected @endif>
                                {{ $tipo->nombre_comida }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="hora_programada" class="form-label">Hora de Alimentación</label>
                    <input type="time" class="form-control" id="hora_programada" name="hora_programada" value="{{ old('hora_programada', $horario->hora_programada) }}" required>
                </div>

                <div class="mb-3">
                    <label for="cantidad_gramos" class="form-label">Cantidad (en gramos)</label>
                    <input type="number" class="form-control" id="cantidad_gramos" name="cantidad_gramos" value="{{ old('cantidad_gramos', $horario->cantidad_gramos) }}" required min="1">
                </div>

                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" @if($horario->activo) checked @endif>
                  <label class="form-check-label" for="activo">Horario Activo</label>
                </div>

                <div class="text-end">
                    <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Horario</button>
                </div>
            </form>
        </div>
    </div>
@endsection