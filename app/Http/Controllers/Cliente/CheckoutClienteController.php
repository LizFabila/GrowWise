<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\DireccionEnvio;
use App\Models\MetodoPago;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutClienteController extends Controller
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
            return redirect()->route('cliente.tienda.index')->with('error', 'No hay productos válidos para comprar');
        }

        $direcciones = DireccionEnvio::where('user_id', Auth::id())
            ->orderBy('principal', 'desc')
            ->get();

        $metodosPago = MetodoPago::where('activo', 1)->get();

        return view('cliente.checkout.index', compact('items', 'total', 'direcciones', 'metodosPago', 'producto_id'));
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'direccion_id' => 'required|exists:direcciones_envio,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
        ]);

        $carrito = session()->get('carrito', []);
        $items = [];

        if ($request->producto_id && empty($carrito)) {
            $producto = ProductoVenta::disponible()->find($request->producto_id);
            if (!$producto) {
                return back()->with('error', 'Producto no disponible');
            }
            $items[] = [
                'producto' => $producto,
                'cantidad' => 1,
            ];
        } else {
            foreach ($carrito as $item) {
                $producto = ProductoVenta::find($item['id']);
                if ($producto && $producto->stock >= $item['cantidad']) {
                    $items[] = [
                        'producto' => $producto,
                        'cantidad' => $item['cantidad'],
                    ];
                }
            }
        }

        if (empty($items)) {
            return back()->with('error', 'No hay productos válidos');
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['producto']->precio_unitario * $item['cantidad'];
            }

            $impuesto = $subtotal * 0.16;
            $totalFinal = $subtotal + $impuesto;
            $vendedorId = $items[0]['producto']->user_id;

            $pedido = Pedido::create([
                'user_id_cliente' => Auth::id(),
                'user_id_vendedor' => $vendedorId,
                'id_direccion_envio' => $request->direccion_id,
                'id_metodo_pago' => $request->metodo_pago_id,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total_final' => $totalFinal,
                'estado' => 'confirmado',
                'fecha_pedido' => now(),
                'notas' => $request->notas,
            ]);

            foreach ($items as $item) {
                PedidoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'producto_venta_id' => $item['producto']->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['producto']->precio_unitario,
                    'subtotal' => $item['producto']->precio_unitario * $item['cantidad'],
                ]);

                $item['producto']->stock -= $item['cantidad'];
                if ($item['producto']->stock <= 0) {
                    $item['producto']->estado = 'agotado';
                }
                $item['producto']->save();
            }

            Venta::create([
                'user_id_vendedor' => $vendedorId,
                'user_id_cliente' => Auth::id(),
                'pedido_id' => $pedido->id,
                'total' => $totalFinal,
                'fecha_venta' => now(),
                'estado' => 'completada',
            ]);

            session()->forget('carrito');
            DB::commit();

            return redirect()->route('cliente.pedidos.index')->with('success', '¡Compra realizada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }
}
