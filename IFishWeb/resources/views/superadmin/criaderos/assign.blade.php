@extends('layouts.app')
@section('title', 'Asignar Dispensador')
@section('content')
    <h2 class="text-primary fw-bold">Asignar Dispensador a: <span class="text-dark">{{ $criadero->nombre }}</span></h2>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.criaderos.assign', $criadero) }}">
                @csrf
                <div class="mb-3">
                    <label for="dispensador_id" class="form-label">Selecciona un Dispensador del Inventario</label>
                    <select name="dispensador_id" id="dispensador_id" class="form-select" required>
                        <option value="">Elige un dispensador disponible...</option>
                        @forelse ($dispensadoresSinAsignar as $dispensador)
                            <option value="{{ $dispensador->id_dispensador }}">
                                MAC: {{ $dispensador->mac_address }} (Modelo: {{ $dispensador->modelo ?? 'N/A' }})
                            </option>
                        @empty
                            <option value="" disabled>No hay dispensadores sin asignar en el inventario.</option>
                        @endforelse
                    </select>
                </div>
                <div class="text-end">
                    <a href="{{ route('superadmin.criaderos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" @if($dispensadoresSinAsignar->isEmpty()) disabled @endif>Asignar Dispensador</button>
                </div>
            </form>
        </div>
    </div>
@endsection