<?php

namespace App\Http\Controllers;

use App\Models\ProductoVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiendaController extends Controller
{
    public function index()
    {
        $productos = ProductoVenta::disponible()
            ->with(['user', 'cultivo'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('tienda.index', compact('productos'));
    }

    public function agregarCarrito(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos_venta,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $producto = ProductoVenta::findOrFail($request->producto_id);

        if ($producto->stock < $request->cantidad) {
            return back()->with('error', 'No hay suficiente stock disponible');
        }

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$producto->id])) {
            $carrito[$producto->id]['cantidad'] += $request->cantidad;
        } else {
            $carrito[$producto->id] = [
                'id' => $producto->id,
                'nombre' => $producto->cultivo->nombre,
                'precio' => $producto->precio_unitario,
                'cantidad' => $request->cantidad,
                'stock' => $producto->stock,
                'unidad' => $producto->unidad,
                'vendedor_id' => $producto->user_id,
                'vendedor_nombre' => $producto->user->nombre . ' ' . $producto->user->apellido,
                'imagen' => $producto->cultivo->imagen,
            ];
        }

        session()->put('carrito', $carrito);

        return redirect()->route('carrito.ver')->with('success', 'Producto agregado al carrito');
    }

    public function verCarrito()
    {
        $carrito = session()->get('carrito', []);
        $total = 0;

        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return view('tienda.carrito', compact('carrito', 'total'));
    }

    public function eliminarDelCarrito($id)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.ver')->with('success', 'Producto eliminado del carrito');
    }

    public function actualizarCarrito(Request $request)
    {
        $carrito = session()->get('carrito', []);

        foreach ($request->cantidades as $id => $cantidad) {
            if (isset($carrito[$id]) && $cantidad > 0 && $cantidad <= $carrito[$id]['stock']) {
                $carrito[$id]['cantidad'] = $cantidad;
            }
        }

        session()->put('carrito', $carrito);

        return redirect()->route('carrito.ver')->with('success', 'Carrito actualizado');
    }

    public function vaciarCarrito()
    {
        session()->forget('carrito');
        return response()->json(['success' => true]);
    }

    public function productoDetalle($id)
    {
        $producto = ProductoVenta::disponible()
            ->with(['user', 'cultivo'])
            ->findOrFail($id);

        return view('tienda.detalle', compact('producto'));
    }
}
