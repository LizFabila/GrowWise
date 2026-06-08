<?php

namespace App\Http\Controllers;

use App\Models\Siembra;
use App\Models\Cultivo;
use App\Models\Cosecha;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuscarController extends Controller
{
    public function index()
    {
        return view('buscar.index');
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'cultivo' => 'required|string|min:2',
        ]);

        $busqueda = $request->cultivo;
        $userId = Auth::id();

        // Buscar cultivos que coincidan con el nombre
        $cultivosEncontrados = Cultivo::where('nombre', 'LIKE', "%{$busqueda}%")
            ->where('activo', 1)
            ->get();

        $cultivosIds = $cultivosEncontrados->pluck('id');

        // Buscar siembras de esos cultivos
        $siembras = Siembra::with(['cultivo', 'modulo', 'cosecha', 'evaluacion'])
            ->where('user_id', $userId)
            ->whereIn('cultivo_id', $cultivosIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas del cultivo buscado
        $stats = [
            'total_siembras' => $siembras->count(),
            'activas' => $siembras->where('estado', 'Activa')->count(),
            'completadas' => $siembras->where('estado', 'Completada')->count(),
            'con_problemas' => $siembras->where('estado', 'Problema')->count(),
            'total_cosechado' => $siembras->sum(function($s) {
                return $s->cosecha->cantidad_kg ?? 0;
            }),
            'rendimiento_promedio' => round($siembras->avg(function($s) {
                return $s->evaluacion->rendimiento ?? 0;
            }), 1),
        ];

        return view('buscar.resultados', compact('siembras', 'busqueda', 'cultivosEncontrados', 'stats'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        $userId = Auth::id();

        $cultivos = Cultivo::where('nombre', 'LIKE', "%{$query}%")
            ->where('activo', 1)
            ->limit(10)
            ->get();

        return response()->json($cultivos);
    }

    public function getStats()
    {
        $userId = Auth::id();

        return response()->json([
            'total_cultivos' => Cultivo::where('activo', 1)->count(),
            'mis_cultivos' => Siembra::where('user_id', $userId)
                ->distinct('cultivo_id')
                ->count('cultivo_id'),
            'siembras_activas' => Siembra::where('user_id', $userId)
                ->where('estado', 'Activa')
                ->count(),
        ]);
    }
}
