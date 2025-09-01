@extends('layouts.app')
@section('title', 'Super Admin: Nuevo Criadero y Dueño')
@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevo Criadero y su Dueño</h2>
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
            <form method="POST" action="{{ route('superadmin.criaderos.store') }}">
                @csrf
                <h5 class="mb-3">Datos del Criadero</h5>
                <div class="mb-3"><label for="nombre_criadero" class="form-label">Nombre del Criadero</label><input type="text" class="form-control" id="nombre_criadero" name="nombre_criadero" required></div>
                <div class="mb-3"><label for="ubicacion" class="form-label">Ubicación</label><input type="text" class="form-control" id="ubicacion" name="ubicacion"></div>
                <hr>
                <h5 class="mb-3 mt-4">Datos del Usuario Dueño</h5>
                <div class="mb-3"><label for="name" class="form-label">Nombre del Dueño</label><input type="text" class="form-control" id="name" name="name" required></div>
                <div class="mb-3"><label for="email" class="form-label">Email del Dueño</label><input type="email" class="form-control" id="email" name="email" required></div>
                <div class="mb-3"><label for="password" class="form-label">Contraseña para el Dueño</label><input type="password" class="form-control" id="password" name="password" required></div>
                <div class="mb-3"><label for="password_confirmation" class="form-label">Confirmar Contraseña</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required></div>
                <div class="text-end"><button type="submit" class="btn btn-primary">Guardar Criadero y Crear Dueño</button></div>
            </form>
        </div>
    </div>
@endsection