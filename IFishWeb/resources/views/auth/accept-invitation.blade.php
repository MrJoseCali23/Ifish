@extends('layouts.guest')

@section('title', 'Establecer Contraseña')

@section('content')

    <div class="text-center mb-4">
        <a href="{{ route('inicio') }}">
            <img src="{{ asset('images/logo2.png') }}" alt="iFish Logo" style="height: 60px;">
        </a>
        <h3 class="mt-3 text-blue-800 font-bold">¡Crea tu contraseña!</h3>
        <p class="text-muted">¡Ya casi estás en iFish! Elige una contraseña segura para tu nueva cuenta.</p>
    </div>

    <form method="POST" action="{{ route('invitation.set-password', ['token' => $token]) }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Correo como texto -->
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <!-- <p class="form-control-plaintext text-center mb-3">{{ $email }}</p> -->
            <p class="form-control-plaintext text-center mb-3">{{ $email ?? old('email') }}</p>
        </div>

        <!-- Contraseña -->
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
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
