<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Seller;
use App\Models\Coordinate;
use App\Models\Phone;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de usuario
     */
    public function register(Request $request)
    {
        // Validación base para todos los usuarios
        $baseRules = [
            'primer_nombre'    => 'required|string|max:100',
            'segundo_nombre'   => 'nullable|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'email'            => 'required|string|email|max:255|unique:users,email',
            'password'         => 'required|string|min:8|confirmed',
            'role_id'          => 'required|exists:roles,id',
        ];

        // Validación adicional si es vendedor (role_id = 2)
        if ($request->role_id == 2) {
            $baseRules['nombre_tienda'] = 'required|string|max:255';
            $baseRules['descripcion'] = 'nullable|string';
            $baseRules['latitud'] = 'nullable|numeric';
            $baseRules['longitud'] = 'nullable|numeric';
            $baseRules['direccion'] = 'nullable|string';
            $baseRules['telefonos']     = 'nullable|array';
            $baseRules['telefonos.*']   = 'required|numeric|digits_between:7,15';
        }

        $data = $request->validate($baseRules);

        // 1. Crear el usuario base
        $user = User::create([
            'primer_nombre'    => $data['primer_nombre'],
            'segundo_nombre'   => $data['segundo_nombre'],
            'primer_apellido'  => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'],
            'email'            => $data['email'],
            'password_hash'    => Hash::make($data['password']),
            'role_id'          => $data['role_id'],
            'activo'           => true,
        ]);

        // 2. Si es vendedor, crear registro en tabla sellers
        if ($data['role_id'] == 2) {
            $seller = Seller::create([
                'user_id'        => $user->id,
                'nombre_tienda'  => $data['nombre_tienda'],
                'descripcion'    => $data['descripcion'] ?? null,
                'activo'         => true,
            ]);

            if (!empty($data['telefonos'])) {
                foreach ($data['telefonos'] as $telefono) {
                    Phone::create([
                        'numero_telefono' => $telefono,
                        'seller_id'       => $seller->id,
                    ]);
                }
            }

            // 3. Crear coordenadas para el vendedor
            Coordinate::create([
                'latitud'             => $data['latitud'],
                'longitud'            => $data['longitud'],
                'direccion'           => $data['direccion'],
                'coordinateable_id'   => $seller->id,
                'coordinateable_type' => 'App\Models\Seller',
            ]);
        }

        // 4. Crear token de acceso
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user'    => $user->load('role', 'seller', 'seller.coordinate'),
            'token'   => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Buscar usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Verificar que el usuario esté activo
        if ($user->isSuspended()) {
        // Si está suspendido temporalmente
        if ($user->suspended_until && $user->suspended_until > now()) {
            $days_remaining = now()->diffInHours($user->suspended_until);
            throw ValidationException::withMessages([
                'email' => ["Tu cuenta está suspendida temporalmente. Disponible en {$days_remaining} horas."],
            ]);
        }
        
        // Si está suspendido permanentemente
        throw ValidationException::withMessages([
            'email' => ['Tu cuenta está suspendida permanentemente.'],
        ]);
    }

        // Crear token de acceso
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('role', 'seller','image','coordinate','seller.coordinate','seller.image','seller.phones'),
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Logout del usuario
     */
    public function logout(Request $request)
    {
        // Eliminar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('role', 'seller','image','coordinate','seller.coordinate','seller.image','seller.phones')
        ]);
    }

    /**
     * Logout de todas las sesiones
     */
    public function logoutAll(Request $request)
    {
        // Eliminar todos los tokens del usuario
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Se han cerrado todas las sesiones'
        ]);
    }
}