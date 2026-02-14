<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getColomiWeather(Request $request)
    {
        $apiKey = env('WEATHER_API_KEY'); // Acceso seguro en el backend
        $city = 'Colomi,BO';
        $lang = 'es';

        $response = Http::get("https://api.weatherapi.com/v1/current.json", [
            'key' => $apiKey,
            'q' => $city,
            'lang' => $lang
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        // Devolver un error si la llamada a la API falla
        return response()->json(['error' => 'No se pudo obtener el clima.'], $response->status());
    }
}