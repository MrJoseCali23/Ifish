@extends('layouts.app')

@section('title', 'Crear Usuario - IFish')

@section('content')
<h2 class="text-success fw-bold mb-4">➕ Crear Nuevo Usuario</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('usuarios.store') }}" method="POST" class="glass-effect p-4 rounded shadow-sm">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Rol</label>
        <select name="rol" class="form-select" required>
            <option value="">Seleccione...</option>
            <option value="admin">Administrador</option>
            <option value="trabajador">Trabajador</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> Guardar
    </button>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
</form>
@endsection
