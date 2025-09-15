<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageController extends Controller
{
    public function index()
    {
        $images = Image::included()->filter()->sort()->GetOrPaginate();
        return response()->json($images);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'imageable_id'   => 'required|integer',
            'imageable_type' => 'required|string',
            'imagen'         => 'required|image|max:10240', // archivo de imagen
        ]);

        // Subir a Cloudinary
        $upload = cloudinary()->uploadApi()->upload(
            $request->file('imagen')->getRealPath(),
            ['folder' => 'general_images'] // carpeta opcional
        );

        $image = Image::create([
            'url'            => $upload['secure_url'],
            'public_id'      => $upload['public_id'],
            'imageable_id'   => $data['imageable_id'],
            'imageable_type' => $data['imageable_type'],
        ]);

        if (!$image) {
            return response()->json([
                'message' => 'No se pudo crear la imagen.'
            ], 400);
        }

        return response()->json([
            'message' => 'Imagen creada correctamente.',
            'image'   => $image,
        ], 201);
    }

    public function show(Image $image)
    {
        return response()->json($image);
    }

    public function update(Request $request, Image $image)
    {
        $data = $request->validate([
            'imagen'         => 'nullable|image|max:10240',
            'imageable_id'   => 'required|integer',
            'imageable_type' => 'required|string',
        ]);

        // Si hay nueva imagen, reemplazar en Cloudinary
        if ($request->hasFile('imagen')) {
            if ($image->public_id) {
                cloudinary()->uploadApi()->destroy($image->public_id);
            }

            $upload = cloudinary()->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'general_images']
            );

            $image->update([
                'url'            => $upload['secure_url'],
                'public_id'      => $upload['public_id'],
                'imageable_id'   => $data['imageable_id'],
                'imageable_type' => $data['imageable_type'],
            ]);
        } else {
            // Solo actualizar relaciones
            $image->update([
                'imageable_id'   => $data['imageable_id'],
                'imageable_type' => $data['imageable_type'],
            ]);
        }

        return response()->json([
            'message' => 'Imagen actualizada correctamente.',
            'image'   => $image,
        ], 200);
    }

    public function destroy($id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'error' => 'Imagen no encontrada.'
            ], 404);
        }

        // Eliminar de Cloudinary también
        if ($image->public_id) {
            cloudinary()->uploadApi()->destroy($image->public_id);
        }

        if ($image->delete()) {
            return response()->json([
                'message' => 'Imagen eliminada correctamente.'
            ], 200);
        }

        return response()->json([
            'error' => 'No se pudo eliminar la imagen.'
        ], 400);
    }
}
