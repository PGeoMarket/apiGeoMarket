<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Image;

class PublicationController extends Controller
{
    public function index()
    {
       $publications = Publication::where('visibilidad', true)->included()->filter()->sort()->getOrPaginate();
        return response()->json($publications);
    }


    public function store(Request $request)
{
    $data = $request->validate([
        'titulo'      => 'required|string|max:255',
        'precio'      => 'required|numeric|min:0',
        'descripcion' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
        'seller_id'   => 'required|exists:sellers,id',
        'imagen'      => 'nullable|image|max:10240', // archivo imagen, 5MB máx
    ]);

    // Crear publicación
    $publication = Publication::create($data);

    if (!$publication) {
        return response()->json([
            'message' => 'No se pudo crear la publicación.'
        ], 400);
    }

    // Subir imagen si viene
    if ($request->hasFile('imagen')) {
        $upload = cloudinary()->uploadApi()->upload(
            $request->file('imagen')->getRealPath(),
            ['folder' => 'publications'] // opcional: carpeta en Cloudinary
        );

        $publication->image()->create([
            'url'       => $upload['secure_url'],
            'public_id' => $upload['public_id'],
        ]);
    }

    return response()->json([
        'message'     => 'Publicación creada correctamente.',
        'publication' => $publication->load('image'),
    ], 201);
}


    public function show(Publication $publication)
    {
        $publication = Publication::included()->findOrFail($publication->id);

        return response()->json($publication);
    }

    public function update(Request $request, Publication $publication)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'seller_id'   => 'required|exists:sellers,id',
            'imagen'      => 'nullable|image|max:10240', // 👈 ahora acepta archivo
            'visibilidad' => 'nullable'
        ]);

        $data['fecha_actualizacion'] = now();
        $publication->update($data);

        // Si viene nueva imagen, reemplazar en Cloudinary
        if ($request->hasFile('imagen')) {
            // 1. Eliminar la anterior de Cloudinary
            if ($publication->image && $publication->image->public_id) {
                cloudinary()->uploadApi()->destroy($publication->image->public_id);
                $publication->image->delete();
            }

            // 2. Subir la nueva
            $upload = cloudinary()->uploadApi()->upload(
                $request->file('imagen')->getRealPath(),
                ['folder' => 'publications']
            );

            $publication->image()->create([
                'url'       => $upload['secure_url'],
                'public_id' => $upload['public_id'],
            ]);
        }

        return response()->json([
            'message'     => 'Publicación actualizada correctamente.',
            'publication' => $publication->load('image'),
        ], 200);
    }

    public function destroy($id)
    {
        $publication = Publication::with('image')->find($id);

        if (!$publication) {
            return response()->json([
                'error' => 'Publicación no encontrada.'
            ], 404);
        }

        // Eliminar imagen de Cloudinary si existe
        if ($publication->image && $publication->image->public_id) {
            cloudinary()->uploadApi()->destroy($publication->image->public_id);
            $publication->image->delete();
        }

        if ($publication->delete()) {
            return response()->json([
                'message' => 'Publicación eliminada correctamente.'
            ], 200);
        }

        return response()->json([
            'error' => 'No se pudo eliminar la publicación.'
        ], 400);
    }


}
