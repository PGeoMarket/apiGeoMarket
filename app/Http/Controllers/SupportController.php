<?php

namespace App\Http\Controllers;

use App\Mail\SupportMail;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:2000'
        ]);

        $supportRequest = Support::create([
            'mensaje' => $request->mensaje,
            'user_id' => $request->user_id,
            'fecha_mensaje' => now()
        ]);
        $supportRequest->load('user');

        // Enviar correo al equipo de soporte
       Mail::to('geomarkethelp@gmail.com')->send(new SupportMail($supportRequest));


        return response()->json([
            'message' => 'Solicitud enviada correctamente',
            'data' => $supportRequest
        ], 201);
    }

    public function index()
    {
        $requests = Support::with('user')
                              ->orderBy('fecha_mensaje', 'desc')
                              ->paginate(20);

        return response()->json($requests);
    }
}
