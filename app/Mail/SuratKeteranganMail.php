<?php

namespace App\Mail;

use App\Models\PesertaMagang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class SuratKeteranganMail extends Mailable
{
    use Queueable, SerializesModels;

    public PesertaMagang $peserta;
    public string $captionBody;

    public function __construct(PesertaMagang $peserta, string $captionBody)
    {
        $this->peserta     = $peserta;
        $this->captionBody = $captionBody;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Surat Keterangan Selesai Magang – Pusat Data dan Teknologi Informasi Kementerian PU',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.surat-keterangan',
            with: [
                'peserta'     => $this->peserta,
                'captionBody' => $this->captionBody,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->peserta->surat_keterangan) {
            $path = storage_path('app/public/' . $this->peserta->surat_keterangan);
            if (file_exists($path)) {
                $attachments[] = Attachment::fromPath($path)
                    ->as('Surat_Keterangan_Magang_' . str_replace(' ', '_', $this->peserta->nama) . '.pdf')
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}
