<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            'url'             => 'required|string|url',
            'imageable_id'    => 'required|integer',
            'imageable_type'  => 'required|string',
        ]);

        $image = Image::create($data);

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
        //
    }

    public function update(Request $request, Image $image)
    {
        $data = $request->validate([
            'url'             => 'required|string|url',
            'imageable_id'    => 'required|integer',
            'imageable_type'  => 'required|string',
        ]);

        $image->update($data);

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
