<?php

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use App\Mail\LeaderInviteMail;
use App\Models\MagicLink;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateLeader extends CreateRecord
{
    protected static string $resource = LeaderResource::class;

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
        $leader = $this->record;
        $leader->load('company');

        ['plain_token' => $plainToken] = MagicLink::generateFor(
            $leader,
            'leader_login',
            expiresDays: 7
        );

        $magicLinkUrl = url('/auth/acesso') . '?t=' . $plainToken;

        try {
            Mail::to($leader->email)->send(new LeaderInviteMail($leader, $magicLinkUrl));

            Notification::make()
                ->title('Convite enviado!')
                ->body("E-mail enviado para {$leader->email} com o link de acesso.")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Líder criado, mas e-mail falhou')
                ->body("Erro ao enviar e-mail: {$e->getMessage()}")
                ->warning()
                ->send();
        }
    }
}
