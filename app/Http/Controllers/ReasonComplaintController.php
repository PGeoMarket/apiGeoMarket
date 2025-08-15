<?php

namespace App\Http\Controllers;

use App\Models\ReasonComplaint;
use Illuminate\Http\Request;

class ReasonComplaintController extends Controller
{
      //
    public function index()
    {

        $reason_complaint = ReasonComplaint::all();
        return response()->json($reason_complaint);
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
            'motivo'      => 'required|string|max:255',

        ]);

        $reason_complaint = ReasonComplaint::create($data);

        if (!$reason_complaint) {
            return response()->json([
                'message' => 'No se pudo añadir el reason_complaint.'
            ], 400);
        } else {
            return response()->json([
                'message'     => 'reason_complaint añadido correctamente.',
                'publication' => $reason_complaint,
            ], 201);
        }
    }

    public function show($id)
{
    $reasonComplaint = ReasonComplaint::find($id);

    if (!$reasonComplaint) {
        return response()->json([
            'error' => 'Motivo de queja no encontrado.'
        ], 404);
    }

    return response()->json([
        'message' => 'Motivo de queja encontrado.',
        'data'    => $reasonComplaint
    ], 200);
}



    public function edit()
    {
        //pantalla
    }

    public function update(Request $request, ReasonComplaint $reason_complaint)
    {
        $data = $request->validate([
            'motivo'      => 'required|string|max:255'
        ]);

        $reason_complaint->update($data);

        if (!$reason_complaint) {
            return response()->json([
                'message' => 'No se pudo editar reason_complaint.'
            ], 400);
        } else {
            return response()->json([
            'message'     => 'reason_complaint actualizado correctamente.',
            'reason_complaint' => $reason_complaint,
        ]);
        }

    }

    public function destroy($id)
    {
       // Eliminar reasoncomplaint
        $reason_complaint = ReasonComplaint::find($id);

    if (!$reason_complaint) {
        return response()->json([
            'error' => 'reasoncomplaint no encontrada.'
        ], 404);
    }

    if ($reason_complaint->delete()) {
        return response()->json([
            'message' => 'reasoncomplaint eliminada correctamente.'
        ], 200);
    }

    return response()->json([
        'error' => 'No se pudo eliminar la reasoncomplaint.'
    ], 400);
    }
}
