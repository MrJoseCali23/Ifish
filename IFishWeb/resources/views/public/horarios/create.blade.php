@extends('layouts.app')

@section('title', 'Nuevo Horario de Alimentación - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Programar Nuevo Horario de Alimentación</h2>

    <div class="card shadow glass-effect mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('horarios.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="id_dispensador" class="form-label">Dispensador</label>
                    <select class="form-select @error('id_dispensador') is-invalid @enderror" id="id_dispensador" name="id_dispensador" required>
                        <option value="" disabled selected>Selecciona un dispensador...</option>
                        @foreach ($dispensadores as $dispensador)
                            <option value="{{ $dispensador->id_dispensador }}" {{ old('id_dispensador') == $dispensador->id_dispensador ? 'selected' : '' }}>
                                MAC: {{ $dispensador->mac_address }} (Estanque: {{ $dispensador->estanque->nombre_estanque ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_dispensador')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="id_tipo_comida" class="form-label">Tipo de Comida</label>
                    <select class="form-select @error('id_tipo_comida') is-invalid @enderror" id="id_tipo_comida" name="id_tipo_comida" required>
                        <option value="" disabled selected>Selecciona un tipo de comida...</option>
                        @foreach ($tipos_comida as $tipo)
                            <option value="{{ $tipo->id_tipo_comida }}" {{ old('id_tipo_comida') == $tipo->id_tipo_comida ? 'selected' : '' }}>
                                {{ $tipo->nombre_comida }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_tipo_comida')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="hora_programada" class="form-label">Hora de Alimentación</label>
                    <input type="time" class="form-control @error('hora_programada') is-invalid @enderror" id="hora_programada" name="hora_programada" value="{{ old('hora_programada') }}" required>
                    @error('hora_programada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cantidad_gramos" class="form-label">Cantidad (en gramos)</label>
                    <input type="number" class="form-control @error('cantidad_gramos') is-invalid @enderror" id="cantidad_gramos" name="cantidad_gramos" value="{{ old('cantidad_gramos') }}" required min="1">
                    @error('cantidad_gramos')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Horario</button>
                </div>
            </form>
        </div>
    </div>
@endsection