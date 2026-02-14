@extends('layouts.app')

@section('title', 'Crear Nuevo Estanque - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevo Estanque</h2>

    <div class="card shadow glass-effect mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('estanques.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="nombre_estanque" class="form-label">Nombre del Estanque</label>
                    <input type="text" class="form-control @error('nombre_estanque') is-invalid @enderror" id="nombre_estanque" name="nombre_estanque" value="{{ old('nombre_estanque') }}" required>
                    @error('nombre_estanque')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}">
                     @error('ubicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="dimensiones_metros" class="form-label">Dimensiones (ej: 20x10x2)</label>
                    <input type="text" class="form-control @error('dimensiones_metros') is-invalid @enderror" id="dimensiones_metros" name="dimensiones_metros" value="{{ old('dimensiones_metros') }}">
                     @error('dimensiones_metros')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <a href="{{ route('estanques.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Estanque</button>
                </div>
            </form>
        </div>
    </div>
@endsection