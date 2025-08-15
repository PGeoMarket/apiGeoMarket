<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function index()
{
    $phones = Phone::with('seller')->get();

    return response()->json($phones);
}

    /* public function create()  {
        $areas = Area::all();
        $trainingCenters = TrainingCenter::all();

        return view('teacher.create',compact('areas', 'trainingCenters'));
    } */

    public function store(Request $request)
    {
        $phone = Phone::create($request->all());

        return response()->json([
            'message' => 'Teléfono creado correctamente.',
            'data' => $phone
        ], 201);
    }

    public function show($id)
    {
        $phone = Phone::with('seller')->find($id);

        if (!$phone) {
            return response()->json([
                'error' => 'Teléfono no encontrado.'
            ], 404);
        }

        return response()->json([
            'message' => 'Teléfono encontrado.',
            'data' => $phone
        ], 200);
    }

    public function update(Request $request, Phone $phone)
{
    $request->validate([
        'numero_telefono' => 'required|integer', // ajusta min según tu formato
        'seller_id'       => 'required|exists:sellers,id',
    ]);

    $phone->numero_telefono = $request->numero_telefono;
    $phone->seller_id       = $request->seller_id;
    $phone->save();

    return response()->json([
        'message' => 'Teléfono actualizado correctamente.',
        'data'    => $phone->load('seller')
    ], 200);
}




    /* public function edit(Teacher $teacher) {
        return view('teacher.edit',compact('teacher'));
    } */

    public function destroy($id)
    {
        $phone = Phone::find($id);

    if (!$phone) {
        return response()->json([
            'error' => 'telefono no encontrado.'
        ], 404);
    }

    if ($phone->delete()) {
        return response()->json([
            'message' => 'telefono eliminado correctamente.'
        ], 200);
    }

    return response()->json([
        'error' => 'No se pudo eliminar el telefono.'
    ], 400);
    
    }
}
