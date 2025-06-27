@extends('layouts.app')
@section('title', 'iFish - Bienvenido a la Acuicultura Inteligente')

@section('content')
<div class="text-center mb-5">
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
    <div class="container py-5">
        {{-- SECCIÓN PRINCIPAL ('HERO') --}}
        <div class="row align-items-center g-5 py-5">
            <div class="col-lg-7 text-center text-lg-start">
                <h1 class="display-4 fw-bold lh-1 text-primary mb-3">iFish: Acuicultura Inteligente a tu Alcance</h1>
                <p class="col-lg-10 fs-4 text-muted">
                    Una solución integral para monitorear, gestionar y optimizar la alimentación en criaderos de peces, conectando tus dispensadores al poder de la nube y los datos.
                </p>
            </div>
            <div class="col-10 col-sm-8 col-lg-5 mx-auto">
                @isset($clima)
                <div class="card shadow-lg glass-effect">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted">{{ $clima['ciudad'] ?? 'Clima Local' }}</h5>
                        <div class="display-2 fw-bold my-2">
                            <i class="bi {{ $clima['icono'] ?? 'bi-thermometer-half' }}"></i>
                            {{ $clima['temperatura'] ?? 'N/A' }}°C
                        </div>
                        <p class="fs-4 text-secondary mb-0">{{ $clima['descripcion'] ?? 'No disponible' }}</p>
                    </div>
                </div>
                @endisset
            </div>
        </div>

        {{-- SECCIÓN DE CARACTERÍSTICAS --}}
        <div class="px-4 py-5" id="featured-3">
            <h2 class="pb-2 border-bottom text-center">Funcionalidades Principales</h2>
            <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
                <div class="feature col text-center"><div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-1 mb-3 p-4 rounded-circle shadow"><i class="bi bi-cpu-fill"></i></div><h3 class="fs-2">Control Remoto</h3><p>Gestiona todos tus dispensadores, programa horarios y activa alimentaciones manuales desde cualquier lugar a través de nuestra plataforma web.</p></div>
                <div class="feature col text-center"><div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-1 mb-3 p-4 rounded-circle shadow"><i class="bi bi-graph-up-arrow"></i></div><h3 class="fs-2">Alimentación Eficiente</h3><p>Implementa planes de alimentación basados en la biomasa de tus estanques, permitiendo que el sistema calcule y dispense la cantidad óptima de alimento.</p></div>
                <div class="feature col text-center"><div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-1 mb-3 p-4 rounded-circle shadow"><i class="bi bi-bar-chart-line-fill"></i></div><h3 class="fs-2">Estadísticas Clave</h3><p>Toma decisiones informadas con gráficos y datos sobre el consumo de alimento, estado de tus equipos y rendimiento de tu producción.</p></div>
            </div>
        </div>

        {{-- SECCIÓN DE TECNOLOGÍAS --}}
        <div class="px-4 pt-5 my-5 text-center border-top"><h2 class="pb-2">Tecnologías Utilizadas</h2><div class="col-lg-6 mx-auto"><p class="lead mb-4">Este proyecto integra un stack de tecnologías moderno para crear una solución completa de IoT.</p><div class="d-flex gap-4 justify-content-center flex-wrap"><span class="badge fs-5 text-bg-danger">Laravel</span><span class="badge fs-5 text-bg-primary">Bootstrap 5</span><span class="badge fs-5 text-bg-info text-dark">MySQL</span><span class="badge fs-5 text-bg-success">Chart.js</span><span class="badge fs-5 text-bg-secondary">Arduino (ESP8266)</span></div></div></div>
    </div>

    {{-- FOOTER --}}
    <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"><p class="col-md-4 mb-0 text-muted">&copy; {{ date('Y') }} Proyecto iFish</p><a href="/" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none"><img src="{{ asset('images/logo2.png') }}" alt="iFish Logo" style="height: 32px;"></a></footer></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const weatherInfo = document.getElementById('weather-info');

    fetch("{{ route('weather.colomi') }}") // Llama a tu propia ruta de Laravel
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
    .then(data => {
        const temp = data.current.temp_c;
        const desc = data.current.condition.text;
        const icon = "https:" + data.current.condition.icon;
        const humidity = data.current.humidity;
        const uv = data.current.uv;

    // --- INICIO DEBUG ---
    console.log("Valor de 'icon':", icon);
    console.log("Valor de 'desc':", desc);
    console.log("Datos de condición de la API:", data.current.condition);
    // --- FIN DEBUG ---

    weatherInfo.innerHTML = `
        <img src="${icon}" alt="${desc}" style="height: 48px;" class="mb-2">
        <div><strong>${temp}°C</strong> – ${desc}</div>
        <div class="mt-2">💧 <strong>Humedad:</strong> ${humidity}%</div>
        <div>☀️ <strong>UV:</strong> ${uv}</div>
    `;
})
.catch(error => {
    console.error('Error en el fetch o procesamiento:', error); // También útil para ver errores de red o JSON
    weatherInfo.innerText = "No se pudo cargar el clima (error en la solicitud).";
});
});
</script>

@endpush
