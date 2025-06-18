@extends('layouts.app')

@section('title', 'Nuevo Tipo de Comida - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevo Tipo de Comida</h2>

    <div class="card shadow glass-effect mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('tipos_comida.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="nombre_comida" class="form-label">Nombre del Tipo de Comida</label>
                    <input type="text" class="form-control @error('nombre_comida') is-invalid @enderror" id="nombre_comida" name="nombre_comida" value="{{ old('nombre_comida') }}" required>
                    @error('nombre_comida')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="proveedor" class="form-label">Proveedor (Opcional)</label>
                    <input type="text" class="form-control" id="proveedor" name="proveedor" value="{{ old('proveedor') }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('tipos_comida.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Tipo de Comida</button>
                </div>
            </form>
        </div>
    </div>
@endsection