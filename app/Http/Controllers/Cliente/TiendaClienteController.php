<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ProductoVenta;
use App\Models\Pedido;
use App\Models\DireccionEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiendaClienteController extends Controller
{
    public function index()
    {
        $productos = ProductoVenta::disponible()
            ->with(['user', 'cultivo'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('cliente.tienda.index', compact('productos'));
    }

    public function categorias()
    {
        $categorias = \App\Models\Cultivo::select('tipo')->distinct()->get();
        return view('cliente.tienda.categorias', compact('categorias'));
    }

    public function ofertas()
    {
        $ofertas = ProductoVenta::disponible()
            ->with(['user', 'cultivo'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('cliente.tienda.ofertas', compact('ofertas'));
    }

    public function contacto()
    {
        return view('cliente.tienda.contacto');
    }

    public function productoDetalle($id)
    {
        $producto = ProductoVenta::disponible()
            ->with(['user', 'cultivo'])
            ->findOrFail($id);

        return view('cliente.tienda.detalle', compact('producto'));
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

        return redirect()->route('cliente.carrito.ver')->with('success', 'Producto agregado al carrito');
    }

    public function verCarrito()
    {
        $carrito = session()->get('carrito', []);
        $total = 0;

        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return view('cliente.tienda.carrito', compact('carrito', 'total'));
    }

    public function actualizarCarrito(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$request->id])) {
            $producto = ProductoVenta::find($request->id);
            if ($producto && $producto->stock >= $request->cantidad) {
                $carrito[$request->id]['cantidad'] = $request->cantidad;
                session()->put('carrito', $carrito);
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Stock insuficiente'], 400);
            }
        }

        return response()->json(['success' => false, 'error' => 'Producto no encontrado'], 404);
    }

    public function eliminarDelCarrito($id)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }

        return redirect()->route('cliente.carrito.ver')->with('success', 'Producto eliminado del carrito');
    }

    public function vaciarCarrito(Request $request)
    {
        session()->forget('carrito');

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cliente.tienda.index')->with('success', 'Carrito vaciado');
    }

    public function misPedidos()
    {
        $pedidos = Pedido::where('user_id_cliente', Auth::id())
            ->with(['vendedor', 'detalles.producto.cultivo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.pedidos.index', compact('pedidos'));
    }

    public function verPedido($id)
    {
        $pedido = Pedido::where('user_id_cliente', Auth::id())
            ->with(['vendedor', 'detalles.producto.cultivo', 'direccion', 'metodoPago'])
            ->findOrFail($id);

        return view('cliente.pedidos.show', compact('pedido'));
    }
}
