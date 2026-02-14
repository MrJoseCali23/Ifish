<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña - IFish</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url('https://images.unsplash.com/photo-1616486090128-b33a10f5c996?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            filter: blur(10px);
            z-index: -1;
        }

        .form-container {
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 2rem;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
        }

        .submit-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.5);
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="form-container p-5" style="width: 100%; max-width: 480px;">
        <div class="text-center mb-4">
            <a href="/"><img src="{{ asset('images/logo.png') }}" alt="IFish Logo" style="width: 100%; max-width: 200px;" class="img-fluid"></a>
        </div>

        <h2 class="text-center text-primary fw-bold mb-3">
            <i class="bi bi-key me-2"></i>Restablecer Contraseña
        </h2>

        <p class="text-center text-muted mb-4">
            ¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo y te enviaremos un enlace para que elijas una nueva.
        </p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" name="email" id="email" class="form-control rounded-3 @error('email') is-invalid @enderror" required autofocus value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn submit-btn">
                    <i class="bi bi-envelope-check me-1"></i> Enviar Enlace de Restablecimiento
                </button>
            </div>
        </form>

        <hr class="my-4">

        <div class="text-center">
            <a class="text-decoration-none" href="{{ route('login') }}">
                Volver a inicio de sesión
            </a>
        </div>
    </div>
</div>

</body>
</html>