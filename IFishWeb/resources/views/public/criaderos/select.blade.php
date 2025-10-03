@extends('layouts.app')

@section('title', 'Seleccionar Criadero - iFish')

@push('styles')
<style>
    /* Ocultamos el sidebar y navbar */
    #sidebar, .navbar {
        display: none !important;
    }

    /* Evitamos barras de desplazamiento innecesarias */
    body {
        overflow: hidden;
    }

    /* Fondo principal con imagen desenfocada */
    .main-content {
        padding: 0;
        margin: 0;
        min-height: 100vh;
        background: linear-gradient(rgba(219, 234, 254, 0.8), rgba(226, 232, 240, 0.8)), url('{{ asset('images/background.jpg') }}') no-repeat center center/cover;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px); /* Para compatibilidad con Safari */
        position: relative;
        overflow: hidden;
    }

    .selection-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        color: var(--dark-blue);
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 2rem;
        animation: fadeIn 1s ease-in-out;
    }

    .logo-container img {
        height: 60px;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .selection-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 3rem;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        text-align: center;
        animation: fadeIn 1.2s ease-in-out;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 2.5rem;
        justify-items: center;
        max-width: 1200px;
        width: 100%;
    }

    .profile-card {
        text-decoration: none;
        color: var(--dark-blue);
        transition: all 0.3s ease-in-out;
        position: relative;
        border-radius: 12px;
        padding: 1rem;
        background: white;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.5s ease-in-out forwards;
        animation-delay: calc(var(--index) * 0.1s);
    }

    .profile-card:hover {
        transform: translateY(-8px) scale(1.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        color: var(--secondary-blue);
    }

    .profile-icon {
        width: 160px;
        height: 160px;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        border-radius: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 5rem;
        color: white;
        border: 3px solid transparent;
        transition: all 0.3s ease-in-out;
    }

    .profile-card:hover .profile-icon {
        border-color: var(--accent-blue);
        box-shadow: 0 0 15px rgba(96, 165, 250, 0.5);
    }

    .profile-name {
        margin-top: 1rem;
        font-size: 1.3rem;
        font-weight: 600;
        text-align: center;
        text-transform: capitalize;
    }

    /* Estilo para el caso vacío */
    .empty-state {
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-in-out;
    }

    .empty-state p {
        font-size: 1.5rem;
        color: var(--dark-blue);
        margin-bottom: 1rem;
    }

    .empty-state .text-muted {
        font-size: 1.2rem;
        color: #6b7280;
    }

    .logout-btn {
        display: inline-block;
        padding: 0.8rem 2rem;
        background: linear-gradient(135deg, var(--danger-red), #b91c1c);
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease-in-out;
    }

    .logout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        background: linear-gradient(135deg, #b91c1c, #991b1b);
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Media queries para responsividad */
    @media (max-width: 768px) {
        .selection-title {
            font-size: 2rem;
        }
        .logo-container img {
            height: 50px;
        }
        .profile-icon {
            width: 140px;
            height: 140px;
            font-size: 4rem;
        }
        .profile-name {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .profile-grid {
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        }
        .profile-icon {
            width: 120px;
            height: 120px;
            font-size: 3.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Asignar un índice a cada tarjeta para animaciones escalonadas
    document.querySelectorAll('.profile-card').forEach((card, index) => {
        card.style.setProperty('--index', index);
    });
</script>
@endpush

@section('content')
    <div class="selection-container">
        <div class="logo-container">
            <img src="{{ asset('images/logo2.png') }}" alt="iFish Logo" class="img-fluid">
        </div>
        <h1 class="selection-title">¿En qué criadero quieres trabajar?</h1>

            <div class="profile-grid">
                @forelse ($criaderos as $criadero)

                    {{-- ▼▼▼ LÓGICA INTELIGENTE EN LA VISTA ▼▼▼ --}}
                    @if ($criadero->estado === 'Activo')
                        {{-- Si está activo, es un enlace clickeable --}}
                        <a href="{{ route('criaderos.set-active', $criadero) }}" class="profile-card">
                            <div class="profile-icon"><i class="bi bi-building"></i></div>
                            <p class="profile-name">{{ $criadero->nombre }}</p>
                        </a>
                    @else
                        {{-- Si NO está activo, es un div no clickeable --}}
                        <div class="profile-card disabled">
                            <span class="status-badge">{{ $criadero->estado }}</span>
                            <div class="profile-icon"><i class="bi bi-building"></i></div>
                            <p class="profile-name">{{ $criadero->nombre }}</p>
                        </div>
                    @endif

                @empty
                    <div class="text-center">
                        <p class="fs-4">Aún no tienes ningún criadero asignado.</p>
                        <p class="text-muted">Por favor, contacta al Super Administrador.</p>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-light mt-3">Cerrar Sesión</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"> @csrf </form>
                    </div>
                @endforelse
            </div>
    </div>
@endsection