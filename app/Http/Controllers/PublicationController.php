<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function index()
    {   
        $publications = Publication::included()->filter()->sort()->GetOrPaginate();
        return response()->json($publications);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen'      => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'seller_id'   => 'required|exists:sellers,id', // ✅ agregado
        ]);

        $publication = Publication::create($data);

        if (!$publication) {
            return response()->json([
                'message' => 'No se pudo crear la publicación.'
            ], 400);
        }

        return response()->json([
            'message'     => 'Publicación creada correctamente.',
            'publication' => $publication,
        ], 201);
    }

    public function show(Publication $publication)
    {
        $publication = Publication::with([
            'seller','seller.user', 'category','comments.user', 
            'usersWhoFavorited', 'complaints', 'complaints.reasoncomplaint'
        ])->findOrFail($publication->id);

        return response()->json($publication);
    }

    public function update(Request $request, Publication $publication)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen'      => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'seller_id'   => 'required|exists:sellers,id', // ✅ agregado
        ]);

        $data['fecha_actualizacion'] = now();

        $publication->update($data);

        return response()->json([
            'message'     => 'Publicación actualizada correctamente.',
            'publication' => $publication,
        ], 200);
    }

    public function destroy($id)
    {
        $publication = Publication::find($id);

    if (!$publication) {
        return response()->json([
            'error' => 'publicacion no encontrado.'
        ], 404);
    }

    if ($publication->delete()) {
        return response()->json([
            'message' => 'publicacion eliminado correctamente.'
        ], 200);
    }

    return response()->json([
        'error' => 'No se pudo eliminar la publicacion.'
    ], 400);
    
    }
}
