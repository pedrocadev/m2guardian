<?php

namespace App\Mail;

use App\Models\Collaborator;
use App\Models\Leader;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollaboratorInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collaborator $collaborator,
        public Leader $leader,
        public string $magicLinkUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Você recebeu uma missão no Guardião Digital | ' . $this->leader->company->name,
        );
    }

    public function content(): Content
    {
        $deadline = Carbon::now()
            ->addWeek()
            ->locale('pt_BR')
            ->isoFormat('D [de] MMMM [de] YYYY');

        return new Content(
            view: 'emails.collaborator-invite',
            with: ['deadline' => $deadline],
        );
    }
}
