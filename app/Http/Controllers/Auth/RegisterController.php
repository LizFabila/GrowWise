<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('Register.registro');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        event(new Registered($user));

        auth()->login($user);

        // Redirigir según el rol
        if ($user->isVendedor()) {
            return redirect()->route('vendedor.dashboard');
        }

        return redirect()->route('cliente.tienda.index');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'in:cliente,vendedor'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'cliente',
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($data['nombre'] . '+' . $data['apellido']) . '&background=2E7D32&color=fff&size=40',
        ]);
    }
}
