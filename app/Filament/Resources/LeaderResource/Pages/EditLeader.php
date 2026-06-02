<?php

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use App\Mail\LeaderInviteMail;
use App\Models\MagicLink;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditLeader extends EditRecord
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resend_invite')
                ->label('Reenviar Convite')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reenviar convite de acesso?')
                ->modalDescription('Um novo magic link será gerado e enviado para o e-mail do líder. O link anterior será invalidado pela expiração.')
                ->action(function () {
                    $leader = $this->record;
                    $leader->load('company');

                    $magicLinkUrl = MagicLink::generateUrlFor($leader, 'leader_login', expiresDays: 7);

                    try {
                        Mail::to($leader->email)->send(new LeaderInviteMail($leader, $magicLinkUrl));

                        Notification::make()
                            ->title('Convite reenviado!')
                            ->body("Novo link enviado para {$leader->email}.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Erro ao reenviar')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\DeleteAction::make()->label('Excluir'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
