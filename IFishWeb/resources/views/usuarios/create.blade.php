@extends('layouts.app')
@section('title', 'Admin: Crear Nuevo Usuario')
@section('content')
    <h2 class="text-primary fw-bold">Crear Nuevo Usuario</h2>

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.usuarios.store') }}">
                @csrf

                <h5 class="mb-3 text-muted">Datos de la Cuenta</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email') }}" required>
                    </div>
                </div>

                {{-- Selección de tipo de usuario --}}
                <div class="mb-3">
                    <label for="rol" class="form-label">Tipo de Usuario</label>
                    <select name="rol" id="rol" class="form-select" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="dueno" {{ old('rol') == 'dueno' ? 'selected' : '' }}>Dueño de Criadero</option>
                        <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>

                <hr class="my-4">

                {{-- Campos de criadero: solo visibles si elige "dueño" --}}
                <div id="criadero-section" style="display: none;">
                    <h5 class="mb-3 text-muted">Datos de su Primer Criadero</h5>
                    <div class="mb-3">
                        <label for="nombre_criadero" class="form-label">Nombre del Criadero</label>
                        <input type="text" class="form-control" id="nombre_criadero" name="nombre_criadero"
                               value="{{ old('nombre_criadero') }}">
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación (Opcional)</label>
                        <input type="text" class="form-control" id="ubicacion" name="ubicacion"
                               value="{{ old('ubicacion') }}">
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('superadmin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mostrar/ocultar sección del criadero según el tipo seleccionado
        const rolSelect = document.getElementById('rol');
        const criaderoSection = document.getElementById('criadero-section');

        function toggleCriaderoSection() {
            if (rolSelect.value === 'dueno') {
                criaderoSection.style.display = 'block';
            } else {
                criaderoSection.style.display = 'none';
            }
        }

        rolSelect.addEventListener('change', toggleCriaderoSection);
        document.addEventListener('DOMContentLoaded', toggleCriaderoSection);
    </script>
@endsection
