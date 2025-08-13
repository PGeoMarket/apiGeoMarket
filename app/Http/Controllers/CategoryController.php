<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
      // Listar categorías
    public function index()
    {
        // Si tienes scopes como included(), filter(), sort(), GetOrPaginate()
        $categories = Category::included()->filter()->sort()->GetOrPaginate();

        return response()->json($categories);
    }

    // Crear categoría (vista o preparación)
    public function create()
    {
        // Aquí podrías preparar datos si fuera una vista
    }

    // Guardar categoría
    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria' => 'required|string|max:100',
        ]);

        $category = Category::create($data);

        if (!$category) {
            return response()->json([
                'message' => 'No se pudo crear la categoría.'
            ], 400);
        }

        return response()->json([
            'message'  => 'Categoría creada correctamente.',
            'category' => $category
        ], 201);
    }

    // Mostrar una categoría
    public function show(Category $category)
    {
        // Cargar relaciones si están en el modelo
        $category->load(['publications']);

        return response()->json([
            'category' => $category
        ]);
    }

    // Editar categoría (vista o preparación)
    public function edit(Category $category)
    {
        // Aquí podrías enviar datos a una vista
    }

    // Actualizar categoría
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'categoria' => 'required|string|max:100',
        ]);

        $category->update($data);

        if (!$category) {
            return response()->json([
                'message' => 'No se pudo actualizar la categoría.'
            ], 400);
        }

        return response()->json([
            'message'  => 'Categoría actualizada correctamente.',
            'category' => $category
        ]);
    }

    // Eliminar categoría
    public function destroy(Category $category)
    {
        if ($category->delete()) {
            return response()->json([
                'message' => 'Categoría eliminada correctamente.'
            ], 204);
        }

        return response()->json([
            'error' => 'No se pudo eliminar la categoría.'
        ], 400);
    }
}
