<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Alimentación</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Reporte: Historial de Alimentación - iFish</h1>
    <p>Generado el: {{ $fechaReporte }}</p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Fecha y Hora</th><th>Estanque</th><th>Dispensador</th>
                <th>Tipo de Comida</th><th>Cantidad (gr)</th><th>Tipo</th><th>Iniciado Por</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $registro)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y h:i A') }}</td>
                    <td>{{ $registro->dispensador->estanque->nombre_estanque ?? 'N/A' }}</td>
                    <td>{{ $registro->dispensador->modelo ?? 'N/A' }}</td>
                    <td>{{ $registro->tipoComida->nombre_comida ?? 'N/A' }}</td>
                    <td>{{ $registro->cantidad_dispensada_gramos }}</td>
                    <td>{{ $registro->tipo_alimentacion }}</td>
                    <td>{{ $registro->iniciadoPor->name ?? 'Sistema' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No se encontraron registros.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>