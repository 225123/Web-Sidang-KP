<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupervisorPenilaianMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sidang;
    public $url_penilaian;

    /**
     * Create a new message instance.
     */
    public function __construct($sidang, $url_penilaian)
    {
        $this->sidang = $sidang;
        $this->url_penilaian = $url_penilaian;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permohonan Penilaian Kerja Praktek Mahasiswa UKRIDA',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supervisor_penilaian',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
