<?php

namespace App\Mail;

use App\Models\Reclamacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Copia de la hoja de reclamación que se envía al consumidor al registrarla.
 */
class ConstanciaReclamacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reclamacion $reclamacion)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hoja de reclamación N° '.$this->reclamacion->numero.' - '.config('empresa.nombre_comercial'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.constancia');
    }
}
