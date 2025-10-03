@extends('layouts.app')
@section('title', 'Gestionar Dispensador')
@section('content')
    <h2 class="text-primary fw-bold">Gestionar Dispensador: <span class="text-dark">{{ $dispensadore->modelo ?? 'Sin Modelo' }}</span></h2>
    <p class="text-muted"><code>{{ $dispensadore->mac_address }}</code></p>
    
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('dispensadores.update', $dispensadore) }}">
                @csrf
                @method('PUT')
                
                {{-- CAMPOS DE SOLO LECTURA --}}
                <div class="row mb-3">
                    <div class="col-md-6"><label class="form-label">MAC Address (Fijo)</label><input type="text" class="form-control" value="{{ $dispensadore->mac_address }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Modelo (Fijo)</label><input type="text" class="form-control" value="{{ $dispensadore->modelo }}" readonly></div>
                </div>
                <hr>
                {{-- CAMPOS EDITABLES POR EL DUEÑO --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="id_estanque" class="form-label">Asignar al Estanque</label>
                        <select class="form-select" id="id_estanque" name="id_estanque" required>
                            @foreach ($estanques as $estanque)
                                <option value="{{ $estanque->id_estanque }}" @if($dispensadore->id_estanque == $estanque->id_estanque) selected @endif>{{ $estanque->nombre_estanque }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">Estado Operativo</label>
                        <select class="form-select" name="estado" id="estado">
                            <option value="Activo" @if($dispensadore->estado == 'Activo') selected @endif>Activo</option>
                            <option value="Inactivo" @if($dispensadore->estado == 'Inactivo') selected @endif>Inactivo</option>
                            <option value="Error" @if($dispensadore->estado == 'Error') selected @endif>Reportar Error</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="current_tipo_comida_id" class="form-label">Tipo de Comida Cargada</label>
                        <select name="current_tipo_comida_id" id="current_tipo_comida_id" class="form-select">
                            <option value="">Ninguna</option>
                            @foreach ($tiposComida as $tipo)
                                <option value="{{ $tipo->id_tipo_comida }}" @if($dispensadore->current_tipo_comida_id == $tipo->id_tipo_comida) selected @endif>
                                    {{ $tipo->nombre_comida }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="text-end mt-4">
                    <a href="{{ route('dispensadores.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Dispensador</button>
                </div>
            </form>
        </div>
    </div>
@endsection
