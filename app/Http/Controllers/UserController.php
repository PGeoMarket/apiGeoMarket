<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Image;
use App\Models\Coordinate;

class UserController extends Controller
{
    // Listar todos los usuarios
    public function index()
    {
        $users = User::included()->filter()->sort()->GetOrPaginate();
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
        // Este método ahora es solo para que el admin cree usuarios
        // El registro público lo maneja AuthController

        $data = $request->validate([
            'primer_nombre'    => 'required|string|max:100',
            'segundo_nombre'   => 'nullable|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'email'            => 'required|string|email|max:255|unique:users,email',
            'password'         => 'required|string|min:8',
            'role_id'           => 'required|exists:roles,id',
            'activo'           => 'boolean',

            // extras
            'imagen'      => 'nullable|image|max:10240', // archivo imagen, 10MB máx
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

        // Subir imagen a Cloudinary si viene
        if ($request->hasFile('imagen')) {
            $upload = cloudinary()->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'users'] // carpeta específica para usuarios
            );

            $user->image()->create([
                'url'       => $upload['secure_url'],
                'public_id' => $upload['public_id'],
            ]);
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
        $user = User::included()->findOrFail($user->id);


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
            'role_id'           => 'required|exists:roles,id',
            'activo'           => 'boolean',

            // extras
            'imagen'      => 'nullable|image|max:10240', // archivo imagen, 10MB máx
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

        // Si viene nueva imagen, reemplazar en Cloudinary
        if ($request->hasFile('imagen')) {
            // 1. Eliminar la anterior de Cloudinary si existe
            if ($user->image && $user->image->public_id) {
                cloudinary()->uploadApi()->destroy($user->image->public_id);
                $user->image->delete();
            }

            // 2. Subir la nueva imagen
            $upload = cloudinary()->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'users']
            );

            $user->image()->create([
                'url'       => $upload['secure_url'],
                'public_id' => $upload['public_id'],
            ]);
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
        // Eliminar imagen de Cloudinary si existe
        if ($user->image && $user->image->public_id) {
            cloudinary()->uploadApi()->destroy($user->image->public_id);
            $user->image->delete();
        }

        if ($user->delete()) {
            return response()->json(['message' => 'Usuario eliminado correctamente.'], 200);
        }

        return response()->json(['error' => 'No se pudo eliminar el usuario.'], 400);
    }

    // Favoritos de un usuario
    public function favoritos($id)
    {
        $usuario = User::with('favoritePublications.image')->findOrFail($id);

        return response()->json($usuario->favoritePublications);
    }


    public function toggleFavorito(Request $request, $userId)
    {
        $request->validate([
            'publication_id' => 'required|exists:publications,id'
        ]);

        $user = User::findOrFail($userId);

        // Laravel maneja automáticamente si agregar o quitar
        $result = $user->favoritePublications()->toggle($request->publication_id);

        $message = empty($result['attached']) ? 'Quitado de favoritos' : 'Agregado a favoritos';

        return response()->json(['message' => $message]);
    }
}
