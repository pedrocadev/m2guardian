<?php

namespace App\Mail;

use App\Models\Leader;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaderInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Leader $leader,
        public string $magicLinkUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu acesso ao Guardião Digital — ' . $this->leader->company->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leader-invite',
        );
    }
}
