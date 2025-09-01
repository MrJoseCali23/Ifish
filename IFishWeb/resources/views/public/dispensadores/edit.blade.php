@extends('layouts.app')
@section('title', 'Editar Dispensador')
@section('content')
    <h2 class="text-primary fw-bold">Gestionar Dispensador: <code>{{ $dispensadore->mac_address }}</code></h2>
    @if ($errors->any())
    <div class="alert alert-danger mt-3">
        <strong>¡Ups! Hubo algunos problemas:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <p class="text-muted">Desde aquí puedes asignar el dispensador a un estanque o cambiar su estado operativo.</p>
    
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('dispensadores.update', $dispensadore) }}">
                @csrf
                @method('PUT')
                
                {{-- CAMPOS DE SOLO LECTURA --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">MAC Address</label>
                        <input type="text" class="form-control" value="{{ $dispensadore->mac_address }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Modelo</label>
                        <input type="text" class="form-control" value="{{ $dispensadore->modelo }}" readonly>
                    </div>
                </div>
                <hr>
                {{-- CAMPOS EDITABLES POR EL USUARIO --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_estanque" class="form-label">Asignar al Estanque</label>
                        <select class="form-select" id="id_estanque" name="id_estanque" required>
                            <option value="">Selecciona un estanque...</option>
                            @foreach ($estanques as $estanque)
                                <option value="{{ $estanque->id_estanque }}" @if($dispensadore->id_estanque == $estanque->id_estanque) selected @endif>
                                    {{ $estanque->nombre_estanque }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="estado" class="form-label">Estado Operativo</label>
                        <select class="form-select" name="estado" id="estado">
                            <option value="Activo" @if($dispensadore->estado == 'Activo') selected @endif>Activo</option>
                            <option value="Inactivo" @if($dispensadore->estado == 'Inactivo') selected @endif>Inactivo (para mantenimiento)</option>
                            <option value="Error" @if($dispensadore->estado == 'Error') selected @endif>Reportar Error</option>
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