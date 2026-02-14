@extends('layouts.app')

@section('title', 'Crear Nuevo Dispensador - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevo Dispensador</h2>

    <div class="card shadow glass-effect mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('dispensadores.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="id_estanque" class="form-label">Asignar al Estanque</label>
                    <select class="form-select @error('id_estanque') is-invalid @enderror" id="id_estanque" name="id_estanque" required>
                        <option value="" disabled selected>Selecciona un estanque...</option>
                        @foreach ($estanques as $estanque)
                            <option value="{{ $estanque->id_estanque }}" {{ old('id_estanque') == $estanque->id_estanque ? 'selected' : '' }}>
                                {{ $estanque->nombre_estanque }} (Ubicación: {{ $estanque->ubicacion }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_estanque')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="mac_address" class="form-label">MAC Address del Dispositivo</label>
                    <input type="text" class="form-control @error('mac_address') is-invalid @enderror" id="mac_address" name="mac_address" value="{{ old('mac_address') }}" required placeholder="Ej: 3C:71:BF:F1:E6:E1">
                    @error('mac_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo (ej: iDispenser V2)</label>
                    <input type="text" class="form-control @error('modelo') is-invalid @enderror" id="modelo" name="modelo" value="{{ old('modelo') }}">
                     @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <a href="{{ route('dispensadores.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Dispensador</button>
                </div>
            </form>
        </div>
    </div>
@endsection