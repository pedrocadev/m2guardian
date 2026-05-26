<?php

namespace App\Filament\Resources\CollaboratorResource\Pages;

use App\Filament\Resources\CollaboratorResource;
use App\Mail\CollaboratorInviteMail;
use App\Models\MagicLink;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateCollaborator extends CreateRecord
{
    protected static string $resource = CollaboratorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invited_at'] = now();
        return $data;
    }

    protected function afterCreate(): void
    {
        $collaborator = $this->record->load('invitedBy.company');
        $leader = $collaborator->invitedBy;

        if (!$leader) return;

        ['plain_token' => $plainToken] = MagicLink::generateFor(
            $collaborator, 'collaborator_training', expiresDays: 30
        );

        $url = url('/auth/acesso') . '?t=' . $plainToken;

        try {
            Mail::to($collaborator->email)->send(new CollaboratorInviteMail($collaborator, $leader, $url));

            Notification::make()
                ->title('Colaborador criado e convite enviado!')
                ->body("E-mail enviado para {$collaborator->email}.")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Criado, mas e-mail falhou')
                ->body($e->getMessage())
                ->warning()
                ->send();
        }
    }
}
