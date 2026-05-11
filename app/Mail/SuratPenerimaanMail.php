<?php

namespace App\Mail;

use App\Models\PesertaMagang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class SuratPenerimaanMail extends Mailable
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
            subject: 'Konfirmasi Penerimaan Magang/PKL - Pusat Data dan Teknologi Informasi, Kementerian PU',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.surat-penerimaan',
            with: [
                'peserta'        => $this->peserta,
                'captionBody'    => $this->captionBody, // pesan tambahan opsional dari admin
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        // Lampirkan surat penerimaan final (yang sudah di-TTE admin)
        if ($this->peserta->surat_penerimaan_final) {
            $path = storage_path('app/public/' . $this->peserta->surat_penerimaan_final);
            if (file_exists($path)) {
                $ext      = pathinfo($path, PATHINFO_EXTENSION);
                $mimeType = strtolower($ext) === 'pdf' ? 'application/pdf' : 'image/' . $ext;

                $attachments[] = Attachment::fromPath($path)
                    ->as('Surat_Penerimaan_' . str_replace(' ', '_', $this->peserta->nama) . '.' . $ext)
                    ->withMime($mimeType);
            }
        }

        return $attachments;
    }
}
