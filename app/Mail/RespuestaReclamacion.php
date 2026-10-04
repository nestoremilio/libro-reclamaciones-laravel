<?php

namespace App\Mail;

use App\Models\Reclamacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Respuesta del proveedor a la reclamación.
 */
class RespuestaReclamacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reclamacion $reclamacion)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respuesta a su hoja de reclamación N° '.$this->reclamacion->numero,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.respuesta');
    }
}
