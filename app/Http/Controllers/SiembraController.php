<?php

namespace App\Http\Controllers;

use App\Models\Siembra;
use App\Models\Cultivo;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiembraController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $siembras = Siembra::with(['cultivo', 'modulo'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('Siembras.siembras', compact('siembras'));
    }

    public function create()
    {
        $cultivos = Cultivo::where('activo', 1)->get();
        $modulos = Modulo::where('user_id', auth()->id())->get();

        return view('Siembras.create', compact('cultivos', 'modulos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cultivo_id' => 'required|exists:cultivos,id',
            'modulo_id' => 'required|exists:modulos,id',
            'charola' => 'required|integer|min:1',
            'fecha_siembra' => 'required|date',
            'fecha_estimada_cosecha' => 'nullable|date|after:fecha_siembra',
        ]);

        $user = auth()->user();

        // Verificar charola ocupada
        $existe = Siembra::where('modulo_id', $request->modulo_id)
            ->where('charola', $request->charola)
            ->where('estado', 'Activa')
            ->exists();

        if ($existe) {
            return back()->with('error', 'La charola ya está ocupada en este módulo.');
        }

        // Calcular fecha estimada si no se proporciona
        $fechaEstimada = $request->fecha_estimada_cosecha;
        if (!$fechaEstimada) {
            $cultivo = Cultivo::find($request->cultivo_id);
            $dias = $cultivo->dias_cosecha ?? 30;
            $fechaEstimada = now()->addDays($dias)->toDateString();
        }

        Siembra::create([
            'user_id' => $user->id,
            'cultivo_id' => $request->cultivo_id,
            'modulo_id' => $request->modulo_id,
            'charola' => $request->charola,
            'fecha_siembra' => $request->fecha_siembra,
            'fecha_estimada_cosecha' => $fechaEstimada,
            'estado' => 'Activa',
        ]);

        return redirect()->route('siembras.index')->with('success', 'Siembra creada correctamente.');
    }

    public function edit($id)
    {
        $siembra = Siembra::where('user_id', auth()->id())->findOrFail($id);
        $cultivos = Cultivo::where('activo', 1)->get();
        $modulos = Modulo::where('user_id', auth()->id())->get();

        return view('Siembras.edit', compact('siembra', 'cultivos', 'modulos'));
    }

    public function update(Request $request, $id)
    {
        $siembra = Siembra::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'cultivo_id' => 'required|exists:cultivos,id',
            'modulo_id' => 'required|exists:modulos,id',
            'charola' => 'required|integer|min:1',
            'fecha_siembra' => 'required|date',
            'fecha_estimada_cosecha' => 'nullable|date|after:fecha_siembra',
        ]);

        // Verificar charola ocupada excepto la misma siembra
        $existe = Siembra::where('modulo_id', $request->modulo_id)
            ->where('charola', $request->charola)
            ->where('estado', 'Activa')
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'La charola ya está ocupada en este módulo.');
        }

        $siembra->update($request->all());

        return redirect()->route('siembras.index')->with('success', 'Siembra actualizada correctamente.');
    }

    public function destroy($id)
    {
        $siembra = Siembra::where('user_id', auth()->id())->findOrFail($id);
        $siembra->delete();

        return redirect()->route('siembras.index')->with('success', 'Siembra eliminada correctamente.');
    }
}
