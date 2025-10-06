<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Consumo de Comida</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <h1>Reporte: Consumo de Comida por Tipo</h1>
    <p>
        <strong>Periodo del Reporte:</strong> 
        del {{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}
    </p>
    <p><strong>Generado el:</strong> {{ $fechaReporte }}</p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Tipo de Comida</th>
                <th class="text-end">Total Consumido</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consumoPorTipo as $consumo)
                <tr>
                    <td>{{ $consumo->nombre_comida }}</td>
                    <td class="text-end">{{ number_format($consumo->total_consumido / 1000, 2) }} Kg</td>
                </tr>
            @empty
                <tr><td colspan="2">No hay datos de consumo en el periodo seleccionado.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>