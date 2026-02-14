<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Criaderos</title>
    <style>
        /* Page box */
        @page { margin: 120px 40px 80px 40px; }

        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }

        /* Header */
        header { position: fixed; top: -100px; left: 0; right: 0; height: 100px; }
        .header-left { float: left; }
        .header-right { float: right; text-align: right; }
        .report-title { font-size: 18px; font-weight: 700; color: #0d6efd; margin-bottom: 4px; }
        .report-meta { font-size: 11px; color: #666; }

        /* Footer */
        footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 50px; font-size: 11px; color: #666; }
        .footer .pagenum:before { content: counter(page); }

        /* Table styles */
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #e0e0e0; padding: 6px 8px; text-align: left; }
        th { background: #f7f7f7; font-weight: 700; }
        tr.group-header td { background: #eef6ff; font-weight: 700; border-top: 2px solid #d0e7ff; }
        tr.owner-subtotal td { background: #f8f9fa; font-weight: 700; }

        /* Avoid breaking rows across pages */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr, td, th { page-break-inside: avoid; }

        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <div style="margin-bottom:12px;">
        <h1 style="color:#333; font-size:18px; margin:0 0 6px 0;">Reporte de Criaderos - iFish</h1>
        <p style="margin:0 0 6px 0; font-size:12px; color:#666;">Generado el: {{ $fecha }}</p>
        <hr style="border:none; border-top:1px solid #e0e0e0; margin-top:8px; margin-bottom:12px;">
    </div>

    <footer>
        <div style="width:100%; display:flex; justify-content:space-between;">
            <div class="muted">iFish - Lista de Criaderos</div>
            <div class="muted">Página <span class="pagenum"></span></div>
        </div>
    </footer>

    <main>
        @php
            $grouped = $criaderos->groupBy(function($item) {
                return optional($item->owner)->name ?: 'Sin dueño';
            });
            $totalGeneral = $criaderos->count();
            $counter = 1;
        @endphp

        @if($criaderos->isEmpty())
            <div class="muted">No hay criaderos registrados.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:6%;">#</th>
                        <th style="width:44%;">Nombre del Criadero</th>
                        <th style="width:30%;">Ubicación</th>
                        <th style="width:20%;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grouped as $ownerName => $group)
                        <tr class="group-header">
                            <td colspan="4">Dueño: {{ $ownerName }} <span class="muted">({{ count($group) }} criadero(s))</span></td>
                        </tr>

                        @foreach($group as $criadero)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $criadero->nombre }}</td>
                                <td>{{ $criadero->ubicacion }}</td>
                                <td>{{ $criadero->estado }}</td>
                            </tr>
                        @endforeach

                        <tr class="owner-subtotal">
                            <td colspan="3">Subtotal ({{ $ownerName }})</td>
                            <td>{{ count($group) }}</td>
                        </tr>
                    @endforeach

                    <tr class="owner-subtotal">
                        <td colspan="3">Total General</td>
                        <td>{{ $totalGeneral }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>