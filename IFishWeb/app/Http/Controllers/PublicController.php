<?php
// app/Http/Controllers/PublicController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function dashboard()
    {
        // Datos simulados para empezar - luego conectaremos con BD real
        $totalCapturas = 15420;
        $especiesRegistradas = 45;
        $ubicacionesActivas = 12;
        $capturasMesActual = 1250;
        
        // Datos para gráficos básicos
        $capturasPorMes = [
            'Enero' => 980,
            'Febrero' => 1120,
            'Marzo' => 1350,
            'Abril' => 1180,
            'Mayo' => 1250
        ];
        
        $especiesPopulares = [
            ['nombre' => 'Atún', 'capturas' => 3200],
            ['nombre' => 'Sardina', 'capturas' => 2800],
            ['nombre' => 'Merluza', 'capturas' => 2100],
            ['nombre' => 'Anchoa', 'capturas' => 1900],
            ['nombre' => 'Caballa', 'capturas' => 1600]
        ];
        
        return view('public.dashboard', compact(
            'totalCapturas', 
            'especiesRegistradas', 
            'ubicacionesActivas', 
            'capturasMesActual',
            'capturasPorMes',
            'especiesPopulares'
        ));
    }
    
    public function statistics()
    {
        // Estadísticas más detalladas pero públicas
        $estadisticasGenerales = [
            'capturas_totales' => 15420,
            'promedio_mensual' => 1285,
            'mejor_mes' => 'Marzo',
            'especies_activas' => 45,
            'zonas_pesqueras' => 12
        ];
        
        return view('public.statistics', compact('estadisticasGenerales'));
    }
    
    public function species()
    {
        // Lista básica de especies sin datos sensibles
        $especies = [
            ['id' => 1, 'nombre' => 'Atún', 'nombre_cientifico' => 'Thunnus', 'tipo' => 'Pelágico'],
            ['id' => 2, 'nombre' => 'Sardina', 'nombre_cientifico' => 'Sardina pilchardus', 'tipo' => 'Pelágico'],
            ['id' => 3, 'nombre' => 'Merluza', 'nombre_cientifico' => 'Merluccius', 'tipo' => 'Demersal'],
            ['id' => 4, 'nombre' => 'Anchoa', 'nombre_cientifico' => 'Engraulis', 'tipo' => 'Pelágico'],
            ['id' => 5, 'nombre' => 'Caballa', 'nombre_cientifico' => 'Scomber', 'tipo' => 'Pelágico']
        ];
        
        return view('public.species', compact('especies'));
    }
    
    public function catches()
    {
        // Resumen público de capturas sin detalles específicos
        $resumenCapturas = [
            'total_mes_actual' => 1250,
            'variacion_mes_anterior' => '+8.5%',
            'zona_mas_activa' => 'Zona Norte',
            'especie_mas_capturada' => 'Atún'
        ];
        
        return view('public.catches', compact('resumenCapturas'));
    }
}
