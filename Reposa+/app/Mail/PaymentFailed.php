<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $errorMessage;

    public function __construct(Order $order, string $errorMessage)
    {
        $this->order = $order;
        $this->errorMessage = $errorMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago no procesado — Reposa+',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.payment_failed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
