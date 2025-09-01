<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Criaderos</title>
    <style>
        body { font-family: sans-serif; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Reporte de Criaderos - iFish</h1>
    <p>Generado el: {{ $fecha }}</p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Nombre del Criadero</th>
                <th>Dueño</th>
                <th>Ubicación</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($criaderos as $criadero)
                <tr>
                    <td>{{ $criadero->nombre }}</td>
                    <td>{{ $criadero->owner->name ?? 'N/A' }}</td>
                    <td>{{ $criadero->ubicacion }}</td>
                    <td>{{ $criadero->estado }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay criaderos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>