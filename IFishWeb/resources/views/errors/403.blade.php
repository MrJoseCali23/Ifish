@extends('layouts.app')

@section('title', 'Acceso Denegado - iFish')

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: 70vh;">
    <div class="mb-4">
        <i class="bi bi-shield-lock-fill text-danger" style="font-size: 4rem;"></i>
    </div>
    <h1 class="fw-bold text-danger">403 - Acceso Denegado</h1>
    <p class="text-muted mb-4">
        Lo sentimos, no tienes permiso para acceder a esta sección del sistema.<br>
        Si crees que esto es un error, contacta al administrador de la plataforma.
    </p>
    <a href="{{ url()->previous() }}" class="btn btn-primary">
        <i class="bi bi-arrow-left me-1"></i> Volver atrás
    </a>
</div>
@endsection
