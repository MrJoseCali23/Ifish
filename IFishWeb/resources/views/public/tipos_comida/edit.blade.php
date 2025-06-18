@extends('layouts.app')

@section('title', 'Editar Tipo de Comida - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Editando Tipo de Comida</h2>

    <div class="card shadow glass-effect mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('tipos_comida.update', $tipos_comida) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nombre_comida" class="form-label">Nombre del Tipo de Comida</label>
                    <input type="text" class="form-control" id="nombre_comida" name="nombre_comida" value="{{ old('nombre_comida', $tipos_comida->nombre_comida) }}" required>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $tipos_comida->descripcion) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="proveedor" class="form-label">Proveedor (Opcional)</label>
                    <input type="text" class="form-control" id="proveedor" name="proveedor" value="{{ old('proveedor', $tipos_comida->proveedor) }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('tipos_comida.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Tipo de Comida</button>
                </div>
            </form>
        </div>
    </div>
@endsection