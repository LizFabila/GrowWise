<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoVendedorController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::where('user_id_vendedor', Auth::id())
            ->with(['cliente', 'detalles.producto.cultivo'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('vendedor.pedidos.index', compact('pedidos'));
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
