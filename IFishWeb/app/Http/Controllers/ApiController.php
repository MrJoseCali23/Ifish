<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispensador;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * Endpoint para que un dispensador reporte su estado (nivel y temperatura).
     */
    public function reportarNivel(Request $request)
    {
        // 1. Validamos que el Arduino nos envíe los datos correctos.
        $datosValidados = $request->validate([
            'mac_address' => 'required|string|exists:Dispensadores,mac_address',
            'nivel_comida_actual_kg' => 'required|numeric|min:0',
            'temperatura_agua' => 'nullable|numeric',
        ]);

        // 2. Buscamos el dispensador por su MAC Address.
        // Usamos 'withoutGlobalScope' para que el dispositivo pueda ser encontrado
        // sin importar a qué criadero pertenezca.
        $dispensador = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                                ->where('mac_address', $datosValidados['mac_address'])
                                ->first();

        // 3. Si lo encontramos, actualizamos su información.
        if ($dispensador) {
            $dispensador->nivel_comida_actual_kg = $datosValidados['nivel_comida_actual_kg'];
            $dispensador->ultimo_reporte = Carbon::now();
            
            if (isset($datosValidados['temperatura_agua'])) {
                $dispensador->temperatura_agua = $datosValidados['temperatura_agua'];
            }
            
            $dispensador->save(); // Usamos save() ya que $timestamps está desactivado en el modelo

            return response()->json(['status' => 'ok', 'message' => 'Estado reportado exitosamente.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Dispensador no encontrado.'], 404);
    }

    /**
     * Endpoint para que un dispensador pregunte si tiene comandos pendientes.
     */
    public function obtenerComando($mac_address)
    {
        // 1. Buscamos el dispensador por su MAC Address.
        $dispensador = Dispensador::withoutGlobalScope(\App\Scopes\CriaderoScope::class)
                                ->where('mac_address', $mac_address)
                                ->first();

        // 2. Si lo encontramos, revisamos si tiene una orden pendiente.
        if ($dispensador && $dispensador->comando_pendiente) {
            $comando = [
                'comando' => $dispensador->comando_pendiente,
                'valor' => $dispensador->comando_valor,
            ];

            // 3. Limpiamos la orden para que no se repita.
            $dispensador->comando_pendiente = null;
            $dispensador->comando_valor = null;
            $dispensador->save();

            return response()->json($comando);
        }

        // 4. Si no hay nada, le respondemos "nada".
        return response()->json(['comando' => 'nada']);
    }
}