<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
       //
    public function index()
    {   
        //$publications=Publication::included()->get();
        //$publications=Publication::included()->filter()->get();
        //$publications=Publication::included()->filter()->sort()->get();
        $publications=Publication::included()->filter()->sort()->GetOrPaginate();
        return response()->json($publications);


  /*       $publications = Publication::all(); */
    }

    public function create()
    {
        //pantalla
        //$seller_ids = Seller::pluck('id'); //Lista de todos los 'id'
        //$category_ids = Category::pluck('id');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen'      => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $publication = Publication::create($data);
        
        if (!$publication) {
            return response()->json([
                'message' => 'No se pudo crear la publicación.'
            ], 400);
        } else {
            return response()->json([
                'message'     => 'Publicación creada correctamente.',
                'publication' => $publication,
            ], 201);
        }
    }

    public function show(Publication $publication)
    {
        //Relaciones
        $publication = Publication::with(
                [
                    'seller','seller.user', 'category','comments.user', 
                    'usersWhoFavorited', 'complaints', 'complaints.reasoncomplaint'
                ]
            )->findOrFail($publication->id);
        return response()->json($publication);

    }


    public function edit(Publication $Publication)
    {
        //pantalla
    }

    public function update(Request $request, Publication $Publication)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen'      => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data['fecha_actualizacion'] = now();

        $publication->update($data);

        if (!$publication) {
            return response()->json([
                'message' => 'No se pudo editar la publicación.'
            ], 400);
        } else {
            return response()->json([
            'message'     => 'Publicación actualizada correctamente.',
            'publication' => $publication,
        ]);
        }

    }

    public function destroy(Publication $publication)
    {

        if ($publication->delete()) {
            return response()->json([
                'message' => 'Publicación eliminada correctamente.'
            ], 204); // 204 No Content = éxito sin datos
        } else {
            return response()->json([
                'error' => 'No se pudo eliminar la publicación.'
            ], 400); // 400 Bad Request = algo falló en la operación
        }

    }

}
