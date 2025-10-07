@extends('layouts.app')
@section('title', 'Super Admin: Editar Usuario')
@section('content')
    <h2 class="text-primary fw-bold">Editando Usuario: {{ $usuario->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger mt-3"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.usuarios.update', $usuario) }}">
                @csrf
                @method('PUT')
                
                <h5 class="mb-3 text-muted">Datos de la Cuenta</h5>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="name" class="form-label">Nombre Completo</label><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $usuario->name) }}" required></div>
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Correo Electrónico</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $usuario->email) }}" required></div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Rol del Usuario (No editable)</label>
                        <input type="text" class="form-control" value="{{ $usuario->rol }}" readonly disabled>
                    </div>
                </div>
                
                <div class="text-end mt-4">
                    <a href="{{ route('superadmin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ▼▼▼ NUEVA SECCIÓN PARA LA GESTIÓN DE CONTRASEÑA ▼▼▼ --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0">Gestión de Contraseña</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Si el usuario ha olvidado su contraseña, puedes enviarle un enlace seguro para que la restablezca él mismo.</p>
            <form method="POST" action="{{ route('superadmin.usuarios.send-reset-link', $usuario) }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-envelope-at-fill me-2"></i>Enviar Enlace de Recuperación
                </button>
            </form>
        </div>
    </div>

    {{-- SECCIÓN PARA MOSTRAR CRIADEROS ASIGNADOS --}}
    @if($usuario->rol === 'Dueño' && $criaderosDelUsuario->isNotEmpty())
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="mb-0">Criaderos Asignados a este Dueño</h5>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($criaderosDelUsuario as $criadero)
                    <li class="list-group-item">{{ $criadero->nombre }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection