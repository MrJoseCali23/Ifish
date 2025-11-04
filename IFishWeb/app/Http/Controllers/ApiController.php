<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispensador;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * 📡 Reporte de nivel y temperatura desde el dispensador (ESP8266)
     */
    public function reportarNivel(Request $request)
    {
        $datosValidados = $request->validate([
            'mac_address' => 'required|string|exists:Dispensadores,mac_address',
            'nivel_comida_actual_kg' => 'required|numeric|min:0',
            'temperatura_agua' => 'nullable|numeric',
        ]);

        $dispensador = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
            ->where('mac_address', $datosValidados['mac_address'])
            ->first();

        if (!$dispensador) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dispensador no encontrado.',
            ], 404);
        }

        // FORZAR CASTEO A FLOAT
        $nivel = (float) $datosValidados['nivel_comida_actual_kg'];
        $temperatura = $datosValidados['temperatura_agua'] !== null
            ? (float) $datosValidados['temperatura_agua']
            : $dispensador->temperatura_agua;

        $dispensador->update([
            'nivel_comida_actual_kg' => $nivel,
            'temperatura_agua' => $temperatura,
            'ultimo_reporte' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Estado actualizado correctamente.',
            'data' => [
                'nivel_kg' => $dispensador->nivel_comida_actual_kg,
                'temperatura' => $dispensador->temperatura_agua,
                'hora' => $dispensador->ultimo_reporte->toDateTimeString(),
            ],
        ]);
    }

    /**
     * 💬 El dispensador consulta si hay un comando pendiente (dispensar X gramos)
     */
    public function obtenerComando($mac_address)
    {
        $dispensador = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
            ->where('mac_address', $mac_address)
            ->first();

        if (!$dispensador) {
            return response()->json([
                'status' => 'error',
                'message' => 'MAC address no registrada.',
                'comando' => 'none',
            ]);
        }

        // Si hay un comando pendiente
        if ($dispensador->comando_pendiente) {
            $valor = $dispensador->comando_valor ?? 0;

            // Lo limpiamos después de enviar
            $dispensador->update([
                'comando_pendiente' => false,
                'comando_valor' => null,
            ]);

            return response()->json([
                'status' => 'ok',
                'message' => "Comando enviado al dispensador {$mac_address}",
                'comando' => 'dispensar',
                'valor' => (int) $valor,
            ]);
        }

        // Si no hay comandos pendientes
        return response()->json([
            'status' => 'ok',
            'message' => 'No hay comandos pendientes.',
            'comando' => 'none',
        ]);
    }
}
