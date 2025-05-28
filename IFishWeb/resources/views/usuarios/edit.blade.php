@extends('layouts.app')

@section('title', 'Editar Usuario - IFish')

@section('content')
<h2 class="text-warning fw-bold mb-4">✏️ Editar Usuario</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="glass-effect p-4 rounded shadow-sm">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" required value="{{ old('name', $usuario->name) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" class="form-control" required value="{{ old('email', $usuario->email) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Rol</label>
        <select name="rol" class="form-select" required>
            <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>Administrador</option>
            <option value="trabajador" {{ $usuario->rol === 'trabajador' ? 'selected' : '' }}>Trabajador</option>
        </select>
    </div>

    <button type="submit" class="btn btn-warning">
        <i class="bi bi-save me-1"></i> Actualizar
    </button>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
</form>
@endsection
