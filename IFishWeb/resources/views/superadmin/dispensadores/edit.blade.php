@extends('layouts.app')
@section('title', 'Super Admin: Editar Dispensador')
@section('content')
    <h2 class="text-primary fw-bold">Editar Dispensador del Inventario</h2>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.dispensadores-inventario.update', $dispensador) }}">
                @csrf
                @method('PUT')

                <div class="mb-3"><label for="mac_address" class="form-label">MAC Address</label><input type="text" class="form-control" id="mac_address" name="mac_address" value="{{ $dispensador->mac_address }}" required></div>
                <div class="mb-3"><label for="modelo" class="form-label">Modelo</label><input type="text" class="form-control" id="modelo" name="modelo" value="{{ $dispensador->modelo }}"></div>
                <div class="mb-3"><label for="estado" class="form-label">Estado</label><select name="estado" id="estado" class="form-select" required><option value="Activo" @if($dispensador->estado == 'Activo') selected @endif>Activo</option><option value="Inactivo" @if($dispensador->estado == 'Inactivo') selected @endif>Inactivo</option><option value="Error" @if($dispensador->estado == 'Error') selected @endif>Error</option></select></div>

                <div class="mb-3">
                    <label for="criadero_id" class="form-label">Asignar a Criadero</label>
                    <select name="criadero_id" id="criadero_id" class="form-select">
                        <option value="">Sin Asignar (Disponible en Inventario)</option>
                        @foreach ($criaderos as $criadero)
                            <option value="{{ $criadero->id }}" @if($dispensador->criadero_id == $criadero->id) selected @endif>{{ $criadero->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="text-end"><a href="{{ route('superadmin.dispensadores-inventario.index') }}" class="btn btn-secondary">Cancelar</a><button type="submit" class="btn btn-primary">Actualizar Dispensador</button></div>
            </form>
        </div>
    </div>
@endsection