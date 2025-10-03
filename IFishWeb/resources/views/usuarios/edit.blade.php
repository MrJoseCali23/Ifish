@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')
    <div class="container">
        <h2>Editar Usuario</h2>
        <form method="POST" action="{{ route('superadmin.usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rol" class="form-label">Rol del Usuario</label>
                    <select name="rol" id="rol" class="form-select" required>
                        <option value="Dueño" @if($usuario->rol == 'Dueño') selected @endif>Dueño de Criadero</option>
                        <option value="Admin" @if($usuario->rol == 'Admin') selected @endif>Super Admin</option>
                    </select>
                    @error('rol')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" id="password" class="form-control">
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
            </div>
            @if($usuario->rol === 'Dueño' && $criaderos->isNotEmpty())
                <div class="mb-3">
                    <label class="form-label">Criaderos Asignados</label>
                    <ul class="list-group">
                        @foreach($criaderos as $criadero)
                            <li class="list-group-item">{{ $criadero->nombre }} ({{ $criadero->ubicacion }}) - Estado: {{ $criadero->estado }}</li>
                        @endforeach
                    </ul>
                </div>
            @elseif($usuario->rol === 'Dueño')
                <div class="mb-3">
                    <p class="text-muted">No hay criaderos asignados a este usuario.</p>
                </div>
            @endif
            <div class="text-end mt-4">
                <a href="{{ route('superadmin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </div>
        </form>
    </div>
@endsection