@extends('layouts.app')

@section('title', 'Página No Encontrada - iFish')

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center text-center" style="min-height: 70vh;">
    <div class="mb-4">
        <i class="bi bi-search text-warning" style="font-size: 4rem;"></i>
    </div>
    <h1 class="fw-bold text-warning">404 - Página No Encontrada</h1>
    <p class="text-muted mb-4">
        Lo sentimos, la página que buscas no existe o ha sido movida.<br>
        Verifica la URL o vuelve al inicio.
    </p>
    <a href="{{ url()->previous() }}" class="btn btn-primary">
        <i class="bi bi-arrow-left me-1"></i> Volver atrás
    </a>
</div>
@endsection
