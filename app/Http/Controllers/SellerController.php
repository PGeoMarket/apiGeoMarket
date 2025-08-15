<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    // Listar vendedores
    public function index()
    {
        $sellers = Seller::included()->filter()->sort()->GetOrPaginate();
        return response()->json($sellers);
    }

    // Crear vendedor
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'nombre_tienda'     => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'foto_portada'      => 'nullable|string',
            'latitud_tienda'    => 'nullable|numeric',
            'longitud_tienda'   => 'nullable|numeric',
            'direccion_tienda'  => 'nullable|string',
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
        $seller->load(['user', 'phones', 'publications']);
        return response()->json([
            'seller' => $seller
        ]);
    }

    // Actualizar vendedor
    public function update(Request $request, Seller $seller)
    {
        $data = $request->validate([
            'nombre_tienda'     => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'foto_portada'      => 'nullable|string',
            'latitud_tienda'    => 'nullable|numeric',
            'longitud_tienda'   => 'nullable|numeric',
            'direccion_tienda'  => 'nullable|string',
            'activo'            => 'boolean',
        ]);

        $seller->update($data);

        return response()->json([
            'message' => 'Vendedor actualizado correctamente.',
            'seller'  => $seller
        ]);
    }

    // Eliminar vendedor
    public function destroy($id)
    {
        $seller = Seller::find($id);

    if (!$seller) {
        return response()->json([
            'error' => 'seller no encontrado.'
        ], 404);
    }

    if ($seller->delete()) {
        return response()->json([
            'message' => 'seller eliminado correctamente.'
        ], 200);
    }

    return response()->json([
        'error' => 'No se pudo eliminar el seller.'
    ], 400);
    
    }
}
