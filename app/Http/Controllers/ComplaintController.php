<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::included()->filter()->sort()->GetOrPaginate();
        return response()->json($complaints);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'estado'                  => 'required|boolean',
            'descripcion_adicional'   => 'required|string',
            'user_id'                 => 'nullable|exists:users,id',
            'publication_id'          => 'nullable|exists:publications,id',
            'reason_id'               => 'required|exists:reason_complaints,id',
        ]);

        // Mapear 'estado' del request a la columna 'Estado' de la BD (según tu migración)
        $data = $validated;
        $data['Estado'] = $data['estado'];
        unset($data['estado']);

        try {
            $complaint = Complaint::create($data);
        } catch (\Exception $e) {
            Log::error('Complaint store error: '.$e->getMessage());
            return response()->json([
                'error' => 'Error al crear la queja',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message'   => 'Queja enviada correctamente.',
            'complaint' => $complaint
        ], 201);
    }

    public function show(Complaint $complaint)
    {
        // ahora existe la relación 'reason' en el modelo
        $complaint->load(['user', 'publication', 'reason']);

        return response()->json([
            'complaint' => $complaint
        ]);
    }

    public function edit(Complaint $complaint)
    {
        //
    }

    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'estado'                  => 'required|boolean',
            'descripcion_adicional'   => 'required|string',
            'user_id'                 => 'nullable|exists:users,id',
            'publication_id'          => 'nullable|exists:publications,id',
            'reason_id'               => 'required|exists:reason_complaints,id',
        ]);

        $data = $validated;
        $data['Estado'] = $data['estado'];
        unset($data['estado']);

        try {
            $complaint->update($data);
        } catch (\Exception $e) {
            Log::error('Complaint update error: '.$e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar la queja',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message'   => 'Queja actualizada correctamente.',
            'complaint' => $complaint->fresh()
        ]);
    }

    public function destroy(Complaint $complaint)
    {
        try {
            $complaint->delete();
            // 200 con mensaje o 204 sin cuerpo; uso 200 para enviar mensaje.
            return response()->json(['message' => 'Queja eliminada correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Complaint delete error: '.$e->getMessage());
            return response()->json(['error' => 'No se pudo eliminar la queja.'], 400);
        }
    }
}
