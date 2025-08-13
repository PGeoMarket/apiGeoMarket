<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellerController extends Controller
{
    // Listar vendedores
    public function index()
    {
        // Si tienes scopes como included(), filter(), sort(), GetOrPaginate()
        $sellers = Seller::included()->filter()->sort()->GetOrPaginate();

        return response()->json($sellers);
    }

    // Crear vendedor (vista o preparación)
    public function create()
    {
        // Aquí podrías preparar datos si fuera una vista
        // Ejemplo: $user_ids = User::pluck('id');
    }

    // Guardar vendedor en BD
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'foto_portada'      => 'nullable|string',
            'latitud_tienda'    => 'nullable|numeric',
            'longitud_tienda'   => 'nullable|numeric',
            'direccion_tienda'  => 'nullable|string',
            'fecha_creacion'    => 'nullable|date',
            'activo'            => 'boolean',
        ]);

        $seller = Seller::create($data);

        if (!$seller) {
            return response()->json([
                'message' => 'No se pudo crear el vendedor.'
            ], 400);
        }

        return response()->json([
            'message' => 'Vendedor creado correctamente.',
            'seller'  => $seller
        ], 201);
    }

    // Mostrar un vendedor
    public function show(Seller $seller)
    {
        // Cargar relaciones si están en el modelo
        $seller->load(['user', 'phones', 'publications']);

        return response()->json([
            'seller' => $seller
        ]);
    }

    // Editar vendedor (vista o preparación)
    public function edit(Seller $seller)
    {
        // Aquí podrías enviar datos a una vista
    }

    // Actualizar vendedor
    public function update(Request $request, Seller $seller)
    {
        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'foto_portada'      => 'nullable|string',
            'latitud_tienda'    => 'nullable|numeric',
            'longitud_tienda'   => 'nullable|numeric',
            'direccion_tienda'  => 'nullable|string',
            'activo'            => 'boolean',
        ]);

        $data['fecha_creacion'] = $seller->fecha_creacion ?? now();

        $seller->update($data);

        if (!$seller) {
            return response()->json([
                'message' => 'No se pudo actualizar el vendedor.'
            ], 400);
        }

        return response()->json([
            'message' => 'Vendedor actualizado correctamente.',
            'seller'  => $seller
        ]);
    }

    // Eliminar vendedor
    public function destroy(Seller $seller)
    {
        if ($seller->delete()) {
            return response()->json([
                'message' => 'Vendedor eliminado correctamente.'
            ], 204);
        }

        return response()->json([
            'error' => 'No se pudo eliminar el vendedor.'
        ], 400);
    }
}
