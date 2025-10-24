<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña - iFish</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <!-- Encabezado -->
    <div style="background-color: #f8fafc; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <img src="{{ asset('images/logo2.png') }}" alt="iFish Logo" style="height: 45px;" class="img-fluid">
        <h1 style="color: #1e40af; font-size: 24px; margin: 0; font-weight: bold;">
            Hola, {{ $user->name }}
        </h1>
    </div>

    <!-- Cuerpo -->
    <div style="padding: 20px; background-color: #ffffff; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #4b5563;">
            Recibimos una solicitud para restablecer la contraseña de tu cuenta. Si no has sido tú, puedes ignorar este correo.
        </p>

        <p style="text-align: center; margin: 20px 0;">
            <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}" 
               style="background-color: #1e40af; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Restablecer mi Contraseña
            </a>
        </p>

        <p style="font-size: 14px; color: #6b7280;">
            Este enlace de restablecimiento de contraseña expirará en {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.
        </p>

        <p style="font-size: 16px; color: #4b5563;">
            ¿Dudas? Escríbenos a <a href="mailto:soporte@ifish.com" style="color: #1e40af;">soporte@ifish.com</a>
        </p>
    </div>

    <!-- Pie -->
    <div style="background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 8px;">
        <p>¡Gracias por ser parte de la familia iFish!</p>
        <p style="font-weight: bold;">El equipo de {{ config('app.name', 'iFish') }}</p>
        <p>
            <a href="{{ config('app.url', 'https://ifish.com') }}" style="color: #1e40af;">{{ config('app.url', 'https://ifish.com') }}</a> | 
            <a href="{{ config('app.url', 'https://ifish.com') }}/politica-privacidad" style="color: #1e40af;">Política de Privacidad</a>
        </p>
        <p>© {{ date('Y') }} {{ config('app.name', 'iFish') }}. Todos los derechos reservados.</p>
    </div>

</body>
</html>
