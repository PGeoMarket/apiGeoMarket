<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Image;
use App\Models\Coordinate;
class UserController extends Controller
{
    // Listar todos los usuarios
    public function index()
    {
        $users = User::with('role')
            ->filter()
            ->sort()
            ->getOrPaginate();

        return response()->json($users);
    }

    // Crear (solo para vista si es necesario)
    public function create()
    {
        // Pantalla de formulario (no usada en API)
    }

    // Guardar usuario
    public function store(Request $request)
    {
        $data = $request->validate([
            'primer_nombre'    => 'required|string|max:100',
            'segundo_nombre'   => 'nullable|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'email'            => 'required|string|email|max:255|unique:users,email',
            'password'         => 'required|string|min:8',
            'rol_id'           => 'required|exists:roles,id',
            'activo'           => 'boolean',

            // extras
            'foto'      => 'nullable|string|url',
            'latitud'   => 'nullable|numeric',
            'longitud'  => 'nullable|numeric',
            'direccion' => 'nullable|string',
        ]);

        // Hashear la contraseña antes de guardar
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        // Crear usuario
        $user = User::create($data);

        if (!$user) {
            return response()->json(['message' => 'No se pudo crear el usuario.'], 400);
        }

        // Asociar imagen si viene
        if (!empty($data['foto'])) {
            $user->image()->create(['url' => $data['foto']]);
        }

        // Asociar coordenada si viene
        if (!empty($data['latitud']) && !empty($data['longitud'])) {
            $user->coordinate()->create([
                'latitud'   => $data['latitud'],
                'longitud'  => $data['longitud'],
                'direccion' => $data['direccion'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Usuario creado correctamente.',
            'user'    => $user->load('role', 'image', 'coordinate'),
        ], 201);
    }

    // Mostrar un usuario específico
    public function show(User $user)
    {
        $user->load(
            'role',
            'seller',
            'comments',
            'complaints',
            'favoritePublications',
            'image',
            'coordinate'
        );

        return response()->json(['user' => $user]);
    }

    // Editar (solo para vista si es necesario)
    public function edit(User $user)
    {
        // Pantalla de edición
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'primer_nombre'    => 'required|string|max:100',
            'segundo_nombre'   => 'nullable|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'         => 'nullable|string|min:8',
            'rol_id'           => 'required|exists:roles,id',
            'activo'           => 'boolean',

            // extras
            'foto'      => 'nullable|string|url',
            'latitud'   => 'nullable|numeric',
            'longitud'  => 'nullable|numeric',
            'direccion' => 'nullable|string',
        ]);

        // Hashear la contraseña si se envía
        if (!empty($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
            unset($data['password']);
        }

        $updated = $user->update($data);

        if (!$updated) {
            return response()->json(['message' => 'No se pudo actualizar el usuario.'], 400);
        }

        // Actualizar/crear imagen
        if (!empty($data['foto'])) {
            if ($user->image) {
                $user->image->update(['url' => $data['foto']]);
            } else {
                $user->image()->create(['url' => $data['foto']]);
            }
        }

        // Actualizar/crear coordenada
        if (!empty($data['latitud']) && !empty($data['longitud'])) {
            if ($user->coordinate) {
                $user->coordinate->update([
                    'latitud'   => $data['latitud'],
                    'longitud'  => $data['longitud'],
                    'direccion' => $data['direccion'] ?? null,
                ]);
            } else {
                $user->coordinate()->create([
                    'latitud'   => $data['latitud'],
                    'longitud'  => $data['longitud'],
                    'direccion' => $data['direccion'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'user'    => $user->load('role', 'image', 'coordinate'),
        ]);
    }

    // Eliminar usuario
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return response()->json(['message' => 'Usuario eliminado correctamente.'], 200);
        }

        return response()->json(['error' => 'No se pudo eliminar el usuario.'], 400);
    }

    // Favoritos de un usuario
    public function favoritos($id)
    {
        $usuario = User::with('favoritePublications')->findOrFail($id);

        return response()->json([
            'usuario_id' => $usuario->id,
            'favoritos'  => $usuario->favoritePublications
        ]);
    }
}