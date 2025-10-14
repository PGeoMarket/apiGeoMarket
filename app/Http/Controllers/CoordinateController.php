<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinate;

class CoordinateController extends Controller
{
    public function index()
    {
        $coordinates = Coordinate::included()->filter()->sort()->GetOrPaginate();
        return response()->json($coordinates);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'latitud'             => 'required|numeric',
            'longitud'            => 'required|numeric',
            'direccion'           => 'required|string',
            'coordinateable_id'   => 'required|integer',
            'coordinateable_type' => 'required|string',
        ]);

        $coordinate = Coordinate::create($data);

        if (!$coordinate) {
            return response()->json([
                'message' => 'No se pudo crear la coordenada.'
            ], 400);
        }

        return response()->json([
            'message'    => 'Coordenada creada correctamente.',
            'coordinate' => $coordinate,
        ], 201);
    }

    public function show(Coordinate $coordinate)
    {
        return response()->json(data: $coordinate);
    }

    public function update(Request $request, Coordinate $coordinate)
    {
        $data = $request->validate([
            'latitud'   => 'nullable|numeric',
            'longitud'  => 'nullable|numeric',
            'direccion' => 'nullable|string',
        ]);

        $data = array_filter($data, fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'message' => 'No se proporcionaron campos para actualizar.'
            ], 400);
        }

        $coordinate->update($data);

        return response()->json([
            'message'    => 'Coordenada actualizada correctamente.',
            'coordinate' => $coordinate,
        ], 200);
    }

    public function destroy($id)
    {
        $coordinate = Coordinate::find($id);

        if (!$coordinate) {
            return response()->json([
                'error' => 'Coordenada no encontrada.'
            ], 404);
        }

        if ($coordinate->delete()) {
            return response()->json([
                'message' => 'Coordenada eliminada correctamente.'
            ], 200);
        }

        return response()->json([
            'error' => 'No se pudo eliminar la coordenada.'
        ], 400);
    }
}
