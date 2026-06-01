<?php

namespace App\Http\Controllers;

use App\Models\DireccionEnvio;
use App\Models\MetodoPago;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index($producto_id = null)
    {
        $carrito = session()->get('carrito', []);
        $items = [];
        $total = 0;

        if ($producto_id && empty($carrito)) {
            $producto = ProductoVenta::disponible()
                ->with('cultivo', 'user')
                ->findOrFail($producto_id);

            $items[] = [
                'producto' => $producto,
                'cantidad' => 1,
                'subtotal' => $producto->precio_unitario,
            ];
            $total = $producto->precio_unitario;
        } else {
            foreach ($carrito as $item) {
                $producto = ProductoVenta::find($item['id']);
                if ($producto && $producto->stock >= $item['cantidad']) {
                    $items[] = [
                        'producto' => $producto,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $producto->precio_unitario * $item['cantidad'],
                    ];
                    $total += $producto->precio_unitario * $item['cantidad'];
                }
            }
        }

        if (empty($items)) {
            return redirect()->route('tienda.index')->with('error', 'No hay productos válidos para comprar');
        }

        $direcciones = DireccionEnvio::where('user_id', Auth::id())
            ->orderBy('principal', 'desc')
            ->get();

        $metodosPago = MetodoPago::where('activo', 1)->get();

        return view('tienda.checkout', compact('items', 'total', 'direcciones', 'metodosPago', 'producto_id'));
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos_venta,id',
            'cantidad' => 'required|integer|min:1',
            'direccion_id' => 'required|exists:direcciones_envio,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
        ]);

        $producto = ProductoVenta::findOrFail($request->producto_id);

        if ($producto->stock < $request->cantidad) {
            return response()->json(['success' => false, 'error' => 'Stock insuficiente'], 400);
        }

        DB::beginTransaction();

        try {
            $subtotal = $producto->precio_unitario * $request->cantidad;
            $impuesto = $subtotal * 0.16;
            $totalFinal = $subtotal + $impuesto;

            $pedido = Pedido::create([
                'user_id_cliente' => Auth::id(),
                'user_id_vendedor' => $producto->user_id,
                'id_direccion_envio' => $request->direccion_id,
                'id_metodo_pago' => $request->metodo_pago_id,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total_final' => $totalFinal,
                'estado' => 'confirmado',
                'fecha_pedido' => now(),
            ]);

            PedidoDetalle::create([
                'pedido_id' => $pedido->id,
                'producto_venta_id' => $producto->id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $producto->precio_unitario,
                'subtotal' => $subtotal,
            ]);

            // Actualizar stock
            $producto->stock -= $request->cantidad;
            if ($producto->stock <= 0) {
                $producto->estado = 'agotado';
            }
            $producto->save();

            Venta::create([
                'user_id_vendedor' => $producto->user_id,
                'user_id_cliente' => Auth::id(),
                'pedido_id' => $pedido->id,
                'total' => $totalFinal,
                'fecha_venta' => now(),
                'estado' => 'completada',
            ]);

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
