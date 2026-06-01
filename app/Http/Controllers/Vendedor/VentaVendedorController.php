<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;

class VentaVendedorController extends Controller
{
    public function index()
    {
        $ventas = Venta::where('user_id_vendedor', Auth::id())
            ->with(['cliente', 'pedido.detalles.producto.cultivo'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalVendido = Venta::where('user_id_vendedor', Auth::id())->sum('total');
        $totalVentas = Venta::where('user_id_vendedor', Auth::id())->count();

        return view('vendedor.ventas.index', compact('ventas', 'totalVendido', 'totalVentas'));
    }

    public function detalle($id)
    {
        $venta = Venta::where('user_id_vendedor', Auth::id())
            ->with(['cliente', 'pedido.detalles.producto.cultivo', 'pedido.direccion', 'pedido.metodoPago'])
            ->findOrFail($id);

        return view('vendedor.ventas.detalle', compact('venta'));
    }
}
