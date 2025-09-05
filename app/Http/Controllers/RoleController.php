<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::included()->filter()->sort()->getOrPaginate();
        return response()->json($roles);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:50',
            'permisos'      => 'required|json',
        ]);

        $role = Role::create($data);

        if (!$role) {
            return response()->json([
                'message' => 'No se pudo crear el rol.'
            ], 400);
        } else {
            return response()->json([
                'message'     => 'Rol creado correctamente.',
                'role' => $role,
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
         return response()->json([
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
//
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
