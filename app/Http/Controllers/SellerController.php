<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Seller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Hash;

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
            'imagen'      => 'nullable|image|max:10240', // archivo imagen, 10MB máx
            'latitud'       => 'nullable|numeric',
            'longitud'      => 'nullable|numeric',
            'direccion'     => 'nullable|string',
            'telefonos'     => 'nullable|array', //lista de telefonos
            'telefonos.*'   => 'required|numeric|digits_between:7,15', //reglas para cada item del array
        ]);

        // Crear el seller
        $seller = Seller::create($data);

        if (!$seller) {
            return response()->json([
                'message' => 'No se pudo crear el vendedor.'
            ], 400);
        }

        // Crear teléfonos si vienen
        if (!empty($data['telefonos'])) {
            foreach ($data['telefonos'] as $telefono) {
                Phone::create([
                    'numero_telefono' => $telefono,
                    'seller_id'       => $seller->id,
                ]);
            }
        }

        // Subir imagen a Cloudinary si viene
        if ($request->hasFile('imagen')) {
            $upload = cloudinary()->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'sellers'] // carpeta específica para sellers
            );

            $seller->image()->create([
                'url'       => $upload['secure_url'],
                'public_id' => $upload['public_id'],
            ]);
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
        $seller = Seller::included()->findOrFail($seller->id);

        return response()->json($seller);
    }

    // Actualizar vendedor
    public function update(Request $request, Seller $seller)
    {
        $data = $request->validate([
            'nombre_tienda' => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'activo'        => 'boolean',

            // extras
            'imagen'      => 'nullable|image|max:10240', // archivo imagen, 10MB máx
            'latitud'       => 'nullable|numeric',
            'longitud'      => 'nullable|numeric',
            'direccion'     => 'nullable|string',
            'telefonos'     => 'nullable|array', 
            'telefonos.*'   => 'required|numeric|digits_between:7,15',
        ]);

        $seller->update($data);

        if (isset($data['telefonos'])) {
            // Eliminar teléfonos anteriores
            $seller->phones()->delete();
            
            // Crear los nuevos
            foreach ($data['telefonos'] as $telefono) {
                Phone::create([
                    'numero_telefono' => $telefono,
                    'seller_id'       => $seller->id,
                ]);
            }
        }

        // Si viene nueva imagen, reemplazar en Cloudinary
        if ($request->hasFile('imagen')) {
            // 1. Eliminar la anterior de Cloudinary si existe
            if ($seller->image && $seller->image->public_id) {
                cloudinary()->uploadApi()->destroy($seller->image->public_id);
                $seller->image->delete();
            }

            // 2. Subir la nueva imagen
            $upload = cloudinary()->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'sellers']
            );

            $seller->image()->create([
                'url'       => $upload['secure_url'],
                'public_id' => $upload['public_id'],
            ]);
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
        $seller = Seller::with('image')->find($id);

        if (!$seller) {
            return response()->json([
                'error' => 'Seller no encontrado.'
            ], 404);
        }

        // Eliminar imagen de Cloudinary si existe
        if ($seller->image && $seller->image->public_id) {
            cloudinary()->uploadApi()->destroy($seller->image->public_id);
            $seller->image->delete();
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
