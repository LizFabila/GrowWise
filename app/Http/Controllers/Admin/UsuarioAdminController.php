<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('rol')) {
            $query->where('role', $request->rol);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);

        $totales = [
            'admin'    => User::where('role', 'admin')->count(),
            'vendedor' => User::where('role', 'vendedor')->count(),
            'cliente'  => User::where('role', 'cliente')->count(),
        ];

        return view('Admin.usuarios.index', compact('usuarios', 'totales'));
    }

    public function create()
    {
        return view('Admin.usuarios.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,vendedor,cliente',
            'telefono' => 'nullable|string|max:20',
        ]);

        $initials = urlencode(trim($request->nombre) . '+' . trim($request->apellido));
        $avatar   = "https://ui-avatars.com/api/?name={$initials}&background=2E7D32&color=fff&size=40";

        User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'telefono' => $request->telefono,
            'avatar'   => $avatar,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('Admin.usuarios.editar', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'role'     => 'required|in:admin,vendedor,cliente',
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = $request->only(['nombre', 'apellido', 'email', 'role', 'telefono']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        // No permitir eliminar al propio admin que está logueado
        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function cambiarRol(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:admin,vendedor,cliente']);

        $usuario = User::findOrFail($id);
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $usuario->update(['role' => $request->role]);

        return back()->with('success', "Rol actualizado a '{$request->role}'.");
    }
}
