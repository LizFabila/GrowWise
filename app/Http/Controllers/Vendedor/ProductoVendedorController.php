<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\ProductoVenta;
use App\Models\Cultivo;
use App\Models\Cosecha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoVendedorController extends Controller
{
    public function index()
    {
        $productosEnVenta = ProductoVenta::where('user_id', Auth::id())
            ->where('estado', 'disponible')
            ->where('stock', '>', 0)
            ->with('cultivo')
            ->orderBy('created_at', 'desc')
            ->get();

        $productosInventario = ProductoVenta::where('user_id', Auth::id())
            ->where('estado', 'agotado')
            ->with('cultivo')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendedor.productos.index', compact('productosEnVenta', 'productosInventario'));
    }

    public function crear()
    {
        $cultivos = Cultivo::where('activo', 1)->orderBy('nombre')->get();
        $cosechas = Cosecha::where('user_id', Auth::id())
            ->with('siembra.cultivo') // 🚀 Trae la siembra Y su cultivo al mismo tiempo
            ->orderBy('fecha_cosecha', 'desc')
            ->get();

        return view('vendedor.productos.crear', compact('cultivos', 'cosechas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cultivo_id' => 'required|exists:cultivos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'unidad' => 'required|string|in:kg,g,pieza',
            'precio_unitario' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:1',
        ]);

        ProductoVenta::create([
            'user_id' => Auth::id(),
            'cultivo_id' => $request->cultivo_id,
            'cosecha_id' => $request->cosecha_id,
            'cantidad' => $request->cantidad,
            'unidad' => $request->unidad,
            'precio_unitario' => $request->precio_unitario,
            'stock' => $request->stock,
            'estado' => 'disponible',
        ]);

        return redirect()->route('vendedor.productos.index')->with('success', 'Producto publicado exitosamente');
    }

    public function editar($id)
    {
        $producto = ProductoVenta::where('user_id', Auth::id())->findOrFail($id);
        $cultivos = Cultivo::where('activo', 1)->orderBy('nombre')->get();

        return view('vendedor.productos.editar', compact('producto', 'cultivos'));
    }

    public function update(Request $request, $id)
    {
        $producto = ProductoVenta::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'precio_unitario' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ]);

        $producto->update([
            'precio_unitario' => $request->precio_unitario,
            'stock' => $request->stock,
            'estado' => $request->stock > 0 ? 'disponible' : 'agotado',
        ]);

        return redirect()->route('vendedor.productos.index')->with('success', 'Producto actualizado');
    }

    public function destroy($id)
    {
        $producto = ProductoVenta::where('user_id', Auth::id())->findOrFail($id);
        $producto->update(['estado' => 'eliminado']);

        return redirect()->route('vendedor.productos.index')->with('success', 'Producto eliminado');
    }
}
