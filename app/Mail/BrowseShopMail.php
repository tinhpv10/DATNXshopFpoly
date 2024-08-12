<?php

namespace App\Mail;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrowseShopMail extends Mailable
{
    use Queueable, SerializesModels;

    public Shop $shop;
    /**
     * Create a new message instance.
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->view('emails.browse_shop')
            ->with(['shop' => $this->shop])
            ->subject('Shop của bạn đã được duyệt');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Shop của bạn đã được duyệt',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.browse_shop',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
