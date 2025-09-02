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
            'user_id'       => 'required|exists:users,id',
            'nombre_tienda' => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'activo'        => 'boolean',

            // extras
            'foto'          => 'nullable|string|url',
            'latitud'       => 'nullable|numeric',
            'longitud'      => 'nullable|numeric',
            'direccion'     => 'nullable|string',
        ]);

        // Crear el seller
        $seller = Seller::create($data);

        if (!$seller) {
            return response()->json([
                'message' => 'No se pudo crear el vendedor.'
            ], 400);
        }

        // Asociar imagen si viene
        if (!empty($data['foto'])) {
            $seller->image()->create(['url' => $data['foto']]);
        }

        // Asociar coordenada si viene
        if (!empty($data['latitud']) && !empty($data['longitud'])) {
            $seller->coordinate()->create([
                'latitud'   => $data['latitud'],
                'longitud'  => $data['longitud'],
                'direccion' => $data['direccion'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Vendedor creado correctamente.',
            'seller'  => $seller->load('user', 'phones', 'publications', 'image', 'coordinate'),
        ], 201);
    }

    // Mostrar un vendedor
    public function show(Seller $seller)
    {
        $seller->load([
            'user',
            'phones',
            'publications',
            'image',
            'coordinate'
        ]);

        return response()->json([
            'seller' => $seller
        ]);
    }

    // Actualizar vendedor
    public function update(Request $request, Seller $seller)
    {
        $data = $request->validate([
            'nombre_tienda' => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'activo'        => 'boolean',

            // extras
            'foto'          => 'nullable|string|url',
            'latitud'       => 'nullable|numeric',
            'longitud'      => 'nullable|numeric',
            'direccion'     => 'nullable|string',
        ]);

        $seller->update($data);

        // Actualizar/crear imagen
        if (!empty($data['foto'])) {
            if ($seller->image) {
                $seller->image->update(['url' => $data['foto']]);
            } else {
                $seller->image()->create(['url' => $data['foto']]);
            }
        }

        // Actualizar/crear coordenada
        if (!empty($data['latitud']) && !empty($data['longitud'])) {
            if ($seller->coordinate) {
                $seller->coordinate->update([
                    'latitud'   => $data['latitud'],
                    'longitud'  => $data['longitud'],
                    'direccion' => $data['direccion'] ?? null,
                ]);
            } else {
                $seller->coordinate()->create([
                    'latitud'   => $data['latitud'],
                    'longitud'  => $data['longitud'],
                    'direccion' => $data['direccion'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Vendedor actualizado correctamente.',
            'seller'  => $seller->load('user', 'phones', 'publications', 'image', 'coordinate'),
        ]);
    }

    // Eliminar vendedor
    public function destroy($id)
    {
        $seller = Seller::find($id);

        if (!$seller) {
            return response()->json([
                'error' => 'Seller no encontrado.'
            ], 404);
        }

        if ($seller->delete()) {
            return response()->json([
                'message' => 'Seller eliminado correctamente.'
            ], 200);
        }

        return response()->json([
            'error' => 'No se pudo eliminar el seller.'
        ], 400);
    }
}