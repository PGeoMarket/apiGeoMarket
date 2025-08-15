<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Listar todos los usuarios
    public function index()
    {
        $users = User::with('rol') 
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
            'primer_nombre'      => 'required|string|max:100',
            'segundo_nombre'     => 'nullable|string|max:100',
            'primer_apellido'    => 'required|string|max:100',
            'segundo_apellido'   => 'nullable|string|max:100',
            'foto'               => 'nullable|string',
            'email'              => 'required|string|email|max:255|unique:users,email',
            'password'           => 'required|string|min:8',
            'rol_id'             => 'required|exists:roles,id',
            'latitud'            => 'nullable|numeric',
            'longitud'           => 'nullable|numeric',
            'direccion_completa' => 'nullable|string',
            'activo'             => 'boolean',
        ]);

        // Hashear la contraseña antes de guardar
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        $user = User::create($data);

        if (!$user) {
            return response()->json(['message' => 'No se pudo crear el usuario.'], 400);
        }

        return response()->json([
            'message' => 'Usuario creado correctamente.',
            'user'    => $user,
        ], 201);
    }

    // Mostrar un usuario específico
    public function show(User $user)
    {
        $user->load('rol'); // Relación con roles

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
            'primer_nombre'      => 'required|string|max:100',
            'segundo_nombre'     => 'nullable|string|max:100',
            'primer_apellido'    => 'required|string|max:100',
            'segundo_apellido'   => 'nullable|string|max:100',
            'foto'               => 'nullable|string',
            'email'              => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'           => 'nullable|string|min:8',
            'rol_id'             => 'required|exists:roles,id',
            'latitud'            => 'nullable|numeric',
            'longitud'           => 'nullable|numeric',
            'direccion_completa' => 'nullable|string',
            'activo'             => 'boolean',
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

        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'user'    => $user,
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
      public function favoritos($id){
        $usuario = User::with('favoritePublications')->findOrFail($id);

        return response()->json([
            'usuario_id' => $usuario->id,
            'favoritos' => $usuario->favoritePublications
        ]);
    }

    
}


  

