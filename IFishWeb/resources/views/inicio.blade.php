@extends('layouts.app')

@section('content')
<div class="text-center mb-5">
    <h1 class="fw-bold mb-3">Bienvenido al sistema IFish</h1>
    <p class="fs-5 text-muted">Automatización inteligente para el monitoreo y gestión de alimentación de peces.</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4 glass-effect text-center p-4">
            <h4 class="mb-3"><i class="bi bi-cloud-sun-fill me-2"></i>Clima actual en Colomi</h4>
            <div id="weather-info" class="fs-5 text-secondary">
                Cargando clima...
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const weatherInfo = document.getElementById('weather-info');

    fetch("https://api.weatherapi.com/v1/current.json?key={{ env('WEATHER_API_KEY') }}&q=Colomi,BO&lang=es")
        .then(response => response.json())
        .then(data => {
            const temp = data.current.temp_c;
            const desc = data.current.condition.text;
            const icon = "https:" + data.current.condition.icon;
            const humidity = data.current.humidity;
            const uv = data.current.uv;
            const weatherCode = data.current.condition.code;
            const isDay = data.current.is_day;

            // Mostrar información del clima
            weatherInfo.innerHTML = `
                <img src="${icon}" alt="${desc}" style="height: 48px;" class="mb-2">
                <div><strong>${temp}°C</strong> – ${desc}</div>
                <div class="mt-2">💧 <strong>Humedad:</strong> ${humidity}%</div>
                <div>☀️ <strong>UV:</strong> ${uv}</div>
            `;
        })
        .catch(() => {
            weatherInfo.innerText = "No se pudo cargar el clima.";
        });
});
</script>
@endpush
