<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidosComoCliente = Pedido::where('user_id_cliente', Auth::id())
            ->with(['vendedor', 'detalles.producto.cultivo', 'direccion', 'metodoPago'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pedidosComoVendedor = Pedido::where('user_id_vendedor', Auth::id())
            ->with(['cliente', 'detalles.producto.cultivo', 'direccion', 'metodoPago'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'compras_pendientes' => Pedido::where('user_id_cliente', Auth::id())->where('estado', 'pendiente')->count(),
            'ventas_pendientes' => Pedido::where('user_id_vendedor', Auth::id())->where('estado', 'pendiente')->count(),
            'total_compras' => Pedido::where('user_id_cliente', Auth::id())->count(),
            'total_ventas' => Pedido::where('user_id_vendedor', Auth::id())->count(),
        ];

        return view('pedidos.index', compact('pedidosComoCliente', 'pedidosComoVendedor', 'stats'));
    }

    public function show($id)
    {
        $pedido = Pedido::where(function($query) {
            $query->where('user_id_cliente', Auth::id())
                ->orWhere('user_id_vendedor', Auth::id());
        })
            ->with(['cliente', 'vendedor', 'detalles.producto.cultivo', 'direccion', 'metodoPago'])
            ->findOrFail($id);

        return view('pedidos.show', compact('pedido'));
    }

    public function cancelar($id)
    {
        $pedido = Pedido::where('user_id_cliente', Auth::id())->findOrFail($id);

        if ($pedido->estado !== 'pendiente' && $pedido->estado !== 'confirmado') {
            return back()->with('error', 'No se puede cancelar este pedido');
        }

        $pedido->update(['estado' => 'cancelado']);

        return back()->with('success', 'Pedido cancelado');
    }

    public function actualizarEstado(Request $request, $id)
    {
        $pedido = Pedido::where('user_id_vendedor', Auth::id())->findOrFail($id);

        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,enviado,entregado,cancelado',
        ]);

        $pedido->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado del pedido actualizado');
    }
}
