@extends('layouts.app')
@section('title', 'Super Admin: Editar Dispensador')
@section('content')
    <h2 class="text-primary fw-bold">Editar Dispensador del Inventario</h2>
    <p class="text-muted"><code>{{ $dispensador->mac_address }}</code></p>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.dispensadores-inventario.update', $dispensador) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" value="{{ $dispensador->modelo }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mac_address" class="form-label">MAC Address</label>
                        <input type="text" class="form-control" id="mac_address" name="mac_address" value="{{ $dispensador->mac_address }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="Activo" @if($dispensador->estado == 'Activo') selected @endif>Activo</option>
                            <option value="Inactivo" @if($dispensador->estado == 'Inactivo') selected @endif>Inactivo</option>
                            <option value="Error" @if($dispensador->estado == 'Error') selected @endif>Error</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="criadero_id" class="form-label">Asignar a Criadero</label>
                        {{-- ▼▼▼ INICIO DEL MENÚ DESPLEGABLE ORDENADO ▼▼▼ --}}
                        <select name="criadero_id" id="criadero_id" class="form-select">
                            <option value="">Sin Asignar (Disponible en Inventario)</option>
                            @foreach ($dueñosConCriaderos as $dueño)
                                <optgroup label="Dueño: {{ $dueño->name }}">
                                    @foreach ($dueño->criaderos as $criadero)
                                        <option value="{{ $criadero->id }}" @if($dispensador->criadero_id == $criadero->id) selected @endif>
                                            {{ $criadero->nombre }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('superadmin.dispensadores-inventario.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Dispensador</button>
                </div>
            </form>
        </div>
    </div>
@endsection
