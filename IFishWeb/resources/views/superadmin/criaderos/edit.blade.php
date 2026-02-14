@extends('layouts.app')
@section('title', 'Super Admin: Editar Criadero')
@section('content')
    <h2 class="text-primary fw-bold">Editando Criadero: {{ $criadero->nombre }}</h2>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.criaderos.update', $criadero) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Criadero</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $criadero->nombre }}" required>
                </div>
                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ $criadero->ubicacion }}">
                </div>
                <div class="mb-3">
                    <label for="user_id" class="form-label">Asignar Dueño</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        @foreach($dueños as $dueño)
                            <option value="{{ $dueño->id }}" @if($criadero->user_id == $dueño->id) selected @endif>
                                {{ $dueño->name }} ({{ $dueño->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    {{-- ▼▼▼ AQUÍ ESTÁ LA CORRECCIÓN ▼▼▼ --}}
                    <select name="estado" id="estado" class="form-select" required>
                        <option value="Activo" @if($criadero->estado == 'Activo') selected @endif>Activo</option>
                        <option value="Suspendido" @if($criadero->estado == 'Suspendido') selected @endif>Suspendido</option>
                        <option value="Archivado" @if($criadero->estado == 'Archivado') selected @endif>Archivado</option>
                    </select>
                    {{-- ▲▲▲ FIN DE LA CORRECCIÓN ▲▲▲ --}}
                </div>
                <div class="text-end"><button type="submit" class="btn btn-primary">Actualizar Criadero</button></div>
            </form>
        </div>
    </div>
@endsection