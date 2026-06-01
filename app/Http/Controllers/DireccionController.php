<?php

namespace App\Http\Controllers;

use App\Models\DireccionEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DireccionController extends Controller
{
    public function index()
    {
        $direcciones = DireccionEnvio::where('user_id', Auth::id())
            ->orderBy('principal', 'desc')
            ->get();

        return view('direcciones.index', compact('direcciones'));
    }

    public function create()
    {
        return view('direcciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'calle' => 'required|string|max:150',
            'numero' => 'required|string|max:20',
            'colonia' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:10',
            'referencias' => 'nullable|string',
            'principal' => 'nullable|boolean',
        ]);

        // Si es principal, quitar principal de otras direcciones
        if ($request->principal) {
            DireccionEnvio::where('user_id', Auth::id())->update(['principal' => false]);
        }

        DireccionEnvio::create([
            'user_id' => Auth::id(),
            'calle' => $request->calle,
            'numero' => $request->numero,
            'colonia' => $request->colonia,
            'ciudad' => $request->ciudad,
            'estado' => $request->estado,
            'codigo_postal' => $request->codigo_postal,
            'referencias' => $request->referencias,
            'principal' => $request->principal ?? false,
        ]);

        return redirect()->route('direcciones.index')->with('success', 'Dirección agregada correctamente');
    }

    public function edit($id)
    {
        $direccion = DireccionEnvio::where('user_id', Auth::id())->findOrFail($id);
        return view('direcciones.edit', compact('direccion'));
    }

    public function update(Request $request, $id)
    {
        $direccion = DireccionEnvio::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'calle' => 'required|string|max:150',
            'numero' => 'required|string|max:20',
            'colonia' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:10',
            'referencias' => 'nullable|string',
            'principal' => 'nullable|boolean',
        ]);

        if ($request->principal && !$direccion->principal) {
            DireccionEnvio::where('user_id', Auth::id())->update(['principal' => false]);
        }

        $direccion->update([
            'calle' => $request->calle,
            'numero' => $request->numero,
            'colonia' => $request->colonia,
            'ciudad' => $request->ciudad,
            'estado' => $request->estado,
            'codigo_postal' => $request->codigo_postal,
            'referencias' => $request->referencias,
            'principal' => $request->principal ?? $direccion->principal,
        ]);

        return redirect()->route('direcciones.index')->with('success', 'Dirección actualizada correctamente');
    }

    public function destroy($id)
    {
        $direccion = DireccionEnvio::where('user_id', Auth::id())->findOrFail($id);
        $direccion->delete();

        return redirect()->route('direcciones.index')->with('success', 'Dirección eliminada correctamente');
    }

    public function setPrincipal($id)
    {
        DireccionEnvio::where('user_id', Auth::id())->update(['principal' => false]);
        DireccionEnvio::where('user_id', Auth::id())->where('id', $id)->update(['principal' => true]);

        return back()->with('success', 'Dirección principal actualizada');
    }
}
