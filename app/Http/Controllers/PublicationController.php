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
        $publications = Publication::included()->filter()->sort()->GetOrPaginate();
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
            'visibilidad' => 'boolean',
            'seller_id'   => 'required|exists:sellers,id',
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
        // Carga explícita de relaciones si están definidas en el modelo
        $publication->load(['seller', 'category']);

        return response()->json([
            'publication' => $publication
        ]);
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
            'visibilidad' =>  'boolean',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data['fecha_actualizacion'] = now();

        $Publication->update($data);

        if (!$Publication) {
            return response()->json([
                'message' => 'No se pudo editar la publicación.'
            ], 400);
        } else {
            return response()->json([
                'message'     => 'Publicación actualizada correctamente.',
                'publication' => $Publication,
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
