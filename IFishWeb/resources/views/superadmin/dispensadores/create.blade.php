@extends('layouts.app')
@section('title', 'Super Admin: Añadir Dispensador')
@section('content')
    <h2 class="text-primary fw-bold">Añadir Nuevo Dispensador al Inventario</h2>

    {{-- ▼▼▼ BLOQUE DE ERRORES AÑADIDO ▼▼▼ --}}
    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <strong>¡Ups! Hubo algunos problemas con los datos introducidos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- ▲▲▲ FIN DEL BLOQUE DE ERRORES ▲▲▲ --}}


    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.dispensadores-inventario.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="mac_address" class="form-label">MAC Address</label>
                    <input type="text" class="form-control" id="mac_address" name="mac_address" placeholder="Ej: DC:4F:22:7D:8B:7C" required>
                </div>
                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado Inicial</label>
                    <select name="estado" id="estado" class="form-select" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="text-end">
                    <a href="{{ route('superadmin.dispensadores-inventario.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar en Inventario</button>
                </div>
            </form>
        </div>
    </div>
@endsection