@extends('layouts.app')

@section('title', 'Editar Dispensador - iFish')

@section('content')
    <h2 class="text-primary fw-bold">Editando Dispensador: {{ $dispensadore->mac_address }}</h2>

    <div class="card shadow glass-effect mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('dispensadores.update', ['dispensadore' => $dispensadore]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="id_estanque" class="form-label">Asignar al Estanque</label>
                    <select class="form-select @error('id_estanque') is-invalid @enderror" id="id_estanque" name="id_estanque" required>
                        @foreach ($estanques as $estanque)
                            <option value="{{ $estanque->id_estanque }}" @if($dispensadore->id_estanque == $estanque->id_estanque) selected @endif>
                                {{ $estanque->nombre_estanque }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_estanque')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="mac_address" class="form-label">MAC Address del Dispositivo</label>
                    <input type="text" class="form-control @error('mac_address') is-invalid @enderror" id="mac_address" name="mac_address" value="{{ old('mac_address', $dispensadore->mac_address) }}" required>
                    @error('mac_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control @error('modelo') is-invalid @enderror" id="modelo" name="modelo" value="{{ old('modelo', $dispensadore->modelo) }}">
                     @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" name="estado" id="estado">
                        <option value="Activo" @if($dispensadore->estado == 'Activo') selected @endif>Activo</option>
                        <option value="Inactivo" @if($dispensadore->estado == 'Inactivo') selected @endif>Inactivo</option>
                        <option value="Error" @if($dispensadore->estado == 'Error') selected @endif>Error</option>
                    </select>
                </div>

                <div class="text-end">
                    <a href="{{ route('dispensadores.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Dispensador</button>
                </div>
            </form>
        </div>
    </div>
@endsection