@extends('layouts.app')

@section('title', 'Super Admin: Crear Usuario')

@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevo Usuario</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Ups! Hubo algunos problemas con los datos introducidos:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            {{-- La acción del formulario apunta a la ruta del superadmin que ya creamos --}}
            <form method="POST" action="{{ route('superadmin.usuarios.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="rol" class="form-label">Rol del Usuario</label>
                        <select name="rol" id="rol" class="form-select" required>
                            <option value="" disabled selected>Selecciona un rol...</option>
                            <option value="Dueño">Dueño de Criadero</option>
                            <option value="Admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="criadero_id" class="form-label">Asignar al Criadero</label>
                        <select name="criadero_id" id="criadero_id" class="form-select">
                            <option value="">Ninguno (Solo para Super Admin)</option>
                            @foreach ($criaderos as $criadero)
                                <option value="{{ $criadero->id }}">{{ $criadero->nombre }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Si el rol es 'Dueño' o 'Trabajador', debes asignarle un criadero.</small>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('superadmin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
@endsection