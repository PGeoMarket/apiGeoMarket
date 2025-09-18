<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatSupport;

class ChatSupportMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $supportRequest;

    public function __construct(ChatSupport $supportRequest)
    {
        $this->supportRequest = $supportRequest;
    }

    public function build()
    {
        // CAMBIO: Usar los campos correctos del modelo User
        $nombreCompleto = trim(
            ($this->supportRequest->user->primer_nombre ?? '') . ' ' . 
            ($this->supportRequest->user->segundo_nombre ?? '') . ' ' .
            ($this->supportRequest->user->primer_apellido ?? '') . ' ' .
            ($this->supportRequest->user->segundo_apellido ?? '')
        ) ?: 'Usuario desconocido';
        
        $email = $this->supportRequest->user->email ?? 'Sin email';
        $mensaje = $this->supportRequest->mensaje;
        $fecha = $this->supportRequest->fecha_mensaje->format('d/m/Y H:i');

        return $this->subject('Nueva solicitud de soporte')
            ->html("
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: #175e7a; color: white; padding: 20px; text-align: center;'>
                        <h2>Nueva Solicitud de Soporte</h2>
                    </div>
                    <div style='padding: 20px; background: #f9f9f9;'>
                        <p><strong>Usuario:</strong> {$nombreCompleto}</p>
                        <p><strong>Email:</strong> {$email}</p>
                        <p><strong>Fecha:</strong> {$fecha}</p>
                        <p><strong>Mensaje:</strong></p>
                        <div style='background: white; padding: 15px; border-left: 4px solid #175e7a;'>
                            {$mensaje}
                        </div>
                    </div>
                </div>
            ");
    }
}