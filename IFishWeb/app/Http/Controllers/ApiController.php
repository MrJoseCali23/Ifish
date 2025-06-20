<?php

namespace App\Http\Controllers;

use App\Models\Dispensador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * Recibe y procesa un reporte de nivel de comida desde un dispensador.
     */
    public function reportarNivel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required|string|mac_address',
            'nivel_comida_kg' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Datos inválidos.', 'errors' => $validator->errors()], 400);
        }

        $dispensador = Dispensador::where('mac_address', $request->input('mac_address'))->first();

        if (!$dispensador) {
            return response()->json(['status' => 'error', 'message' => 'Dispositivo no registrado.'], 404);
        }

        $dispensador->nivel_comida_actual_kg = $request->input('nivel_comida_kg');
        $dispensador->ultimo_reporte = Carbon::now();
        $dispensador->save();

        return response()->json(['status' => 'ok', 'message' => 'Nivel actualizado exitosamente.']);
    }

    /**
     * Revisa si hay un comando pendiente para un dispensador y se lo devuelve.
     */
    public function getComando($mac_address)
    {
        $dispensador = Dispensador::where('mac_address', $mac_address)->first();

        if (!$dispensador) {
            return response()->json(['comando' => 'error', 'mensaje' => 'Dispositivo no encontrado'], 404);
        }

        if ($dispensador->comando_pendiente) {
            $respuesta = [
                'comando' => $dispensador->comando_pendiente,
                'gramos' => (int)$dispensador->comando_valor,
            ];

            $dispensador->comando_pendiente = null;
            $dispensador->comando_valor = null;
            $dispensador->save();

            return response()->json($respuesta);
        } else {
            return response()->json(['comando' => 'nada']);
        }
    }
}