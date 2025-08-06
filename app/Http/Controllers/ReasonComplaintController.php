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

    public function show()
    {

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

    public function destroy(ReasonComplaint $reason_complaint)
    {

        if ($reason_complaint->delete()) {
            return response()->json([
                'message' => 'reason_complaint eliminado correctamente.'
            ], 204); // 204 No Content = éxito sin datos
        } else {
            return response()->json([
                'error' => 'No se pudo eliminar el reason_complaint.'
            ], 400); // 400 Bad Request = algo falló en la operación
        }

    }
}
