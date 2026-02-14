@extends('layouts.guest')

@section('title', 'Establecer Contraseña')

@section('content')

    <!-- Logo y encabezado -->
    <div class="text-center mb-4">
        <a href="{{ route('inicio') }}">
            <img src="{{ asset('images/logo2.png') }}" alt="iFish Logo" style="height: 60px;">
        </a>
        <h3 class="mt-3 text-blue-800 font-bold">¡Restablece tu contraseña!</h3>
        <p class="text-muted">Has solicitado restablecer tu contraseña. Ingresa una nueva contraseña segura para tu cuenta.</p>
    </div>

    <!-- Mensajes flash -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <!-- Formulario -->
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <!-- Correo como texto -->
        <div class="mb-3">
            <label class="form-label">Correo electrónico: </label>
            <p class="form-control-plaintext text-center mb-3">{{ $email ?? old('email') }}</p>
        </div>

        <!-- Contraseña -->
        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirmar Contraseña -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg" style="background-color: #1e40af; border-color: #1e40af;">
                Guardar Contraseña y Entrar
            </button>
        </div>
    </form>

@endsection
