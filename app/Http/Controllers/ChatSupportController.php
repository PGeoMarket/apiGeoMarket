<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ChatSupportMail;
use App\Models\ChatSupport;
use App\Mail\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ChatSupportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:2000'
        ]);

        $supportRequest = ChatSupport::create([
            'mensaje' => $request->mensaje,
            'user_id' => auth::id(),
            'fecha_mensaje' => now()
        ]);
        $supportRequest->load('user');

        // Enviar correo al equipo de soporte
       Mail::to('geomarkethelp@gmail.com')->send(new ChatSupportMail($supportRequest));


        return response()->json([
            'message' => 'Solicitud enviada correctamente',
            'data' => $supportRequest
        ], 201);
    }

    public function index()
    {
        $requests = ChatSupport::with('user')
                              ->orderBy('fecha_mensaje', 'desc')
                              ->paginate(20);

        return response()->json($requests);
    }

}