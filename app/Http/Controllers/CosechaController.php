<?php

namespace App\Http\Controllers;

use App\Models\Cosecha;
use App\Models\Siembra;
use App\Models\Cultivo;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CosechaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // SOLO cosechas que ya ocurrieron (fecha <= hoy)
        $query = Cosecha::with(['siembra.cultivo', 'siembra.modulo'])
            ->where('user_id', $user->id)
            ->whereDate('fecha_cosecha', '<=', now());

        // ... filtros ...

        $cosechas = $query->orderBy('fecha_cosecha', 'desc')->paginate(10);

        // Total cosechado REAL (solo lo que ya se cosechó)
        $totalCosechadoReal = Cosecha::where('user_id', $user->id)
            ->whereDate('fecha_cosecha', '<=', now())
            ->sum('cantidad_kg');

        // Próximas cosechas (fecha > hoy)
        $proximasCosechas = Siembra::where('user_id', $user->id)
            ->where('estado', 'Activa')
            ->whereNotNull('fecha_estimada_cosecha')
            ->whereDate('fecha_estimada_cosecha', '>', now())
            ->count();

        $stats = [
            'peso_total' => $totalCosechadoReal,
            'pendientes' => $proximasCosechas,
        ];

        return view('Cosechas.cosechas', compact('cosechas', 'stats'));
    }

    // ===========================================
    // MÉTODO PARA PRÓXIMAS COSECHAS
    // ===========================================
    public function proximas()
    {
        $user = auth()->user();

        // Obtener siembras activas con fecha de cosecha en el futuro
        $proximasCosechas = Siembra::with(['cultivo', 'modulo'])
            ->where('user_id', $user->id)
            ->where('estado', 'Activa')
            ->whereNotNull('fecha_estimada_cosecha')
            ->where('fecha_estimada_cosecha', '>', now()->toDateString())
            ->orderBy('fecha_estimada_cosecha', 'asc')
            ->paginate(15);

        return view('Cosechas.proximas', compact('proximasCosechas'));
    }

    public function create()
    {
        $siembras = Siembra::with(['cultivo', 'modulo'])
            ->where('user_id', auth()->id())
            ->where('estado', 'Activa')
            ->doesntHave('cosecha')
            ->get();

        if ($siembras->isEmpty()) {
            return redirect()->route('cosechas.index')
                ->with('info', 'No hay siembras activas disponibles para cosechar.');
        }

        return view('Cosechas.create', compact('siembras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siembra_id' => 'required|exists:siembras,id',
            'fecha_cosecha' => 'required|date',
            'cantidad_kg' => 'required|numeric|min:0',
            'calidad' => 'required|in:Excelente,Buena,Regular,Mala',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $siembra = Siembra::where('user_id', auth()->id())
            ->where('id', $request->siembra_id)
            ->firstOrFail();

        if ($siembra->cosecha) {
            return redirect()->back()
                ->with('error', 'Esta siembra ya tiene una cosecha registrada.')
                ->withInput();
        }

        Cosecha::create([
            'siembra_id' => $request->siembra_id,
            'user_id' => auth()->id(),
            'fecha_cosecha' => $request->fecha_cosecha,
            'cantidad_kg' => $request->cantidad_kg,
            'calidad' => $request->calidad,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('cosechas.index')
            ->with('success', 'Cosecha registrada correctamente');
    }

    public function show(string $id)
    {
        $cosecha = Cosecha::with(['siembra.cultivo', 'siembra.modulo', 'siembra.evaluacion'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('Cosechas.show', compact('cosecha'));
    }

    public function edit(string $id)
    {
        $cosecha = Cosecha::with(['siembra.cultivo', 'siembra.modulo'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('Cosechas.edit', compact('cosecha'));
    }

    public function update(Request $request, string $id)
    {
        $cosecha = Cosecha::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'fecha_cosecha' => 'required|date',
            'cantidad_kg' => 'required|numeric|min:0',
            'calidad' => 'required|in:Excelente,Buena,Regular,Mala',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $cosecha->update($request->all());

        return redirect()->route('cosechas.index')
            ->with('success', 'Cosecha actualizada correctamente');
    }

    public function destroy(string $id)
    {
        try {
            $cosecha = Cosecha::where('user_id', auth()->id())->findOrFail($id);
            $cosecha->delete();

            return redirect()->route('cosechas.index')
                ->with('success', 'Cosecha eliminada correctamente');

        } catch (QueryException $e) {
            return redirect()->route('cosechas.index')
                ->with('error', 'Error al eliminar la cosecha.');
        }
    }
}
