<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $complaints = Complaint::included()->filter()->sort()->GetOrPaginate();
        return response()->json($complaints);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $data = $request->validate([
            'estado'      => 'required|boolean',
            'descripcion_adicional'      => 'required|string',
            'user_id'   => 'required|exists:users,id',
            'publication_id' => 'required|exists:publications,id',
            'reason_id' => 'required|exists:reasons,id',
        ]);


        $complaint = Complaint::create($data);

        if (!$complaint) {
            return response()->json([
                'message' => 'No se pudo enviar la queja.'
            ], 400);
        } else {
            return response()->json([
                'message'     => 'Queja enviada correctamente.',
                'complaint' => $complaint,
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'publication', 'reason']);

        return response()->json([
            'complaint' => $complaint
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'estado'      => 'required|boolean',
            'descripcion_adicional'      => 'required|string',
            'user_id'   => 'required|exists:users,id',
            'publication_id' => 'required|exists:publications,id',
            'reason_id' => 'required|exists:reasons,id',
        ]);

        $data['fecha_actualizacion'] = now();

        $complaint->update($data);

        if (!$complaint) {
            return response()->json([
                'message' => 'No se pudo editar la queja.'
            ], 400);
        } else {
            return response()->json([
                'message'     => 'Queja actualizada correctamente.',
                'complaint' => $complaint,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        if ($complaint->delete()) {
            return response()->json([
                'message' => 'Queja eliminada correctamente.'
            ], 204); // 204 No Content = éxito sin datos
        } else {
            return response()->json([
                'error' => 'No se pudo eliminar la queja.'
            ], 400); // 400 Bad Request = algo falló en la operación
        }
    }
}
