<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class KirimKalenderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sidang;
    public $gcalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($sidang, $gcalUrl)
    {
        $this->sidang = $sidang;
        $this->gcalUrl = $gcalUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Jadwal Sidang Kerja Praktik',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Provide a simple HTML email body for the student
        return new Content(
            htmlString: '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
                    <h2 style="color: #4285F4; text-align: center;">Jadwal Sidang Kerja Praktik</h2>
                    <p>Halo,</p>
                    <p>Jadwal sidang Anda telah dikirimkan! Silakan tambahkan jadwal ini ke <b>Google Calendar</b> Anda sebagai pengingat.</p>
                    
                    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 10px;"><strong>Tanggal:</strong> ' . \Carbon\Carbon::parse($this->sidang->tanggal_sidang)->locale('id')->isoFormat('dddd, D MMMM Y') . '</li>
                            <li style="margin-bottom: 10px;"><strong>Waktu:</strong> ' . \Carbon\Carbon::parse($this->sidang->waktu_mulai_sidang)->format('H:i') . ' - ' . \Carbon\Carbon::parse($this->sidang->waktu_selesai_sidang)->format('H:i') . ' WIB</li>
                            <li style="margin-bottom: 10px;"><strong>Ruangan:</strong> ' . ($this->sidang->ruang_sidang ?? '-') . '</li>
                        </ul>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <a href="' . $this->gcalUrl . '" style="background-color: #34A853; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 5px; display: inline-block;">
                            Tambahkan ke Google Calendar
                        </a>
                    </div>
                    
                    <p style="margin-top: 30px; font-size: 12px; color: #777;">Terima kasih,<br>Sistem Sidang KP UKRIDA</p>
                </div>
            '
        );
    }
}
