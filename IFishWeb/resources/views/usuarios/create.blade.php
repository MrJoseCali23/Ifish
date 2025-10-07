@extends('layouts.app')
@section('title', ' Admin: Invitar Nuevo Dueño')
@section('content')
    <h2 class="text-primary fw-bold">Invitar Nuevo Dueño y Crear su Primer Criadero</h2>

    @if ($errors->any())
        <div class="alert alert-danger mt-3"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.usuarios.store') }}">
                @csrf
                <h5 class="mb-3 text-muted">Datos de la Cuenta del Dueño</h5>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="name" class="form-label">Nombre Completo</label><input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required></div>
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Correo Electrónico</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required></div>
                </div>
                {{-- Los campos de contraseña han sido eliminados --}}
                
                <hr class="my-4">

                <h5 class="mb-3 text-muted">Datos de su Primer Criadero</h5>
                <div class="mb-3">
                    <label for="nombre_criadero" class="form-label">Nombre del Criadero</label>
                    <input type="text" class="form-control" id="nombre_criadero" name="nombre_criadero" value="{{ old('nombre_criadero') }}" required>
                </div>
                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación (Opcional)</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}">
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('superadmin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Enviar Invitación</button>
                </div>
            </form>
        </div>
    </div>
@endsection
