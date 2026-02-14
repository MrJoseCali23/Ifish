@extends('layouts.app')

@section('title', 'iFish - Optimiza tu Criadero de Peces')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.9) 0%, rgba(226, 232, 240, 0.9) 100%), url('https://images.unsplash.com/photo-1524704796725-9fc3044a5a24?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzOTRecDB8MHwxfGFsbHx8fHx8fHx8fDE3MjYzMDUyODV8&ixlib=rb-4.0.3&q=80&w=1080') no-repeat center center;
        background-size: cover;
    }
    .feature-card {
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15) !important;
    }
</style>
@endpush


@section('content')
    <div class="hero-section text-center py-5 rounded-3">
        <div class="container col-xl-10 col-xxl-8 px-4 py-5">
            <div class="row align-items-center g-lg-5 py-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <h1 class="display-4 fw-bold lh-1 text-primary mb-3">Optimiza tu criadero. Aumenta tu producción.</h1>
                    <p class="col-lg-10 fs-4 text-muted">
                        iFish te ofrece la tecnología para automatizar la alimentación y monitorear tus dispensadores de forma remota, permitiéndote tomar decisiones inteligentes basadas en datos reales.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4">
                        <a href="https://wa.me/59167408921?text=Hola,%20estoy%20interesado%20en%20el%20servicio%20de%20iFish." class="btn btn-success btn-lg px-4 me-md-2" target="_blank">
                            <i class="bi bi-whatsapp me-2"></i>Solicitar Servicio
                        </a>
                    </div>
                </div>
                <div class="col-md-10 mx-auto col-lg-5">
                    <div class="card shadow-lg glass-effect">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title text-muted mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Clima en Colomi</h5>
                            <div id="weather-info" class="fs-4">
                                Cargando... <div class="spinner-border spinner-border-sm" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container px-4 py-5">
        <h2 class="pb-2 border-bottom text-center">Beneficios para tu Negocio</h2>
        <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
            <div class="col">
                <div class="card feature-card h-100 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-primary fs-1 mb-3"><i class="bi bi-clock-history"></i></div>
                        <h3 class="fs-2">Ahorra Tiempo</h3>
                        <p>Automatiza la tarea más repetitiva. Nuestro sistema alimenta a tus peces en los horarios y cantidades exactas, liberando tu tiempo para otras gestiones.</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card feature-card h-100 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-primary fs-1 mb-3"><i class="bi bi-graph-up-arrow"></i></div>
                        <h3 class="fs-2">Maximiza el Crecimiento</h3>
                        <p>Aplica planes de alimentación basados en la biomasa. Asegura la dosis óptima en cada etapa de crecimiento para una conversión de alimento más eficiente.</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card feature-card h-100 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-primary fs-1 mb-3"><i class="bi bi-cloud-check-fill"></i></div>
                        <h3 class="fs-2">Control Remoto</h3>
                        <p>Supervisa el estado de tus dispensadores y el consumo de alimento desde cualquier lugar. Recibe alertas y toma el control total de tu operación.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const weatherInfo = document.getElementById('weather-info');

    fetch("{{ route('weather.colomi') }}")
        .then(response => {
            if (!response.ok) { throw new Error('La respuesta de la red no fue exitosa'); }
            return response.json();
        })
        .then(data => {
            const temp = Math.round(data.current.temp_c);
            const desc = data.current.condition.text;
            const iconUrl = "https:" + data.current.condition.icon;

            weatherInfo.innerHTML = `
                <div class="display-4 fw-bold">
                    <img src="${iconUrl}" alt="${desc}" style="height: 64px; vertical-align: middle;">
                    <span style="vertical-align: middle;">${temp}°C</span>
                </div>
                <div class="fs-5 text-secondary mt-2">${desc}</div>
            `;
        })
        .catch(error => {
            console.error('Error al obtener el clima:', error);
            weatherInfo.innerText = "No se pudo cargar el clima.";
        });

    // Lógica para notificaciones de navegador
    @if(isset($dispensadoresCriticos) && $dispensadoresCriticos->isNotEmpty())
        if (Notification.permission === "granted") {
            @foreach($dispensadoresCriticos as $disp)
                new Notification("⚠️ Nivel bajo en {{ $disp->modelo }}", {
                    body: "Nivel actual: {{ number_format($disp->nivel_comida_actual_kg, 1) }} Kg",
                    icon: "{{ asset('images/logo2.png') }}"
                });
            @endforeach
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    new Notification("🔔 Notificaciones activadas para iFish");
                }
            });
        }
    @endif
});
</script>
@endpush