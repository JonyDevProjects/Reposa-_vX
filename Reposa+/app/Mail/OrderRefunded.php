<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRefunded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $refund;

    public function __construct(Order $order, Refund $refund)
    {
        $this->order = $order;
        $this->refund = $refund;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reembolso procesado — Pedido #' . $this->order->id . ' — Reposa+',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.refunded',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
