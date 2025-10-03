@extends('layouts.app')
@section('title', 'Super Admin: Nuevo Criadero')
@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevo Criadero</h2>
    <p class="text-muted">Crear un nuevo criadero y asignárselo a un Dueño.</p>

    @if ($errors->any())
        <div class="alert alert-danger mt-3"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.criaderos.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Criadero</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación (Opcional)</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}">
                </div>
                <div class="mb-3">
                    <label for="user_id" class="form-label">Asignar al Dueño</p>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <option value="" disabled selected>Selecciona un Dueño</option>
                        @foreach($dueños as $dueño)
                            <option value="{{ $dueño->id }}">{{ $dueño->name }} ({{ $dueño->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-end">
                    <a href="{{ route('superadmin.criaderos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Criadero</button>
                </div>
            </form>
        </div>
    </div>
@endsection
