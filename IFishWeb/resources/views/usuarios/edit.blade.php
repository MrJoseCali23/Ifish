@extends('layouts.app')

@section('title', 'Super Admin: Editar Usuario')

@section('content')
    <h2 class="text-primary fw-bold">Editando Usuario: {{ $usuario->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.usuarios.update', $usuario) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3"><label for="name" class="form-label">Nombre Completo</label><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $usuario->name) }}" required></div>
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Correo Electrónico</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $usuario->email) }}" required></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3"><label for="password" class="form-label">Nueva Contraseña</label><input type="password" class="form-control" id="password" name="password"><small class="text-muted">Dejar en blanco para no cambiar la contraseña.</small></div>
                    <div class="col-md-6 mb-3"><label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation"></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="rol" class="form-label">Rol del Usuario</label>
                        {{-- ▼▼▼ INICIO DEL BLOQUE ACTUALIZADO ▼▼▼ --}}
                        <select name="rol" id="rol" class="form-select" required>
                            <option value="Dueño" @if($usuario->rol == 'Dueño') selected @endif>Dueño de Criadero</option>
                            <option value="Admin" @if($usuario->rol == 'Admin') selected @endif>Super Admin</option>
                        </select>
                        {{-- ▲▲▲ FIN DEL BLOQUE ACTUALIZADO ▲▲▲ --}}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="criadero_id" class="form-label">Asignar al Criadero</label>
                        <select name="criadero_id" id="criadero_id" class="form-select">
                            <option value="">Ninguno (Solo para Super Admin)</option>
                            @foreach ($criaderos as $criadero)
                                <option value="{{ $criadero->id }}" @if($usuario->criadero_id == $criadero->id) selected @endif>{{ $criadero->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('superadmin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
@endsection