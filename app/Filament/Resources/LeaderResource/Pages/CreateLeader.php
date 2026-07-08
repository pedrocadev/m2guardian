<?php

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLeader extends CreateRecord
{
    protected static string $resource = LeaderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        LeaderResource::resetLeaderPassword($this->record);

        Notification::make()
            ->title('Líder criado e senha gerada!')
            ->body('Clique em "Mostrar Credenciais" na linha do líder para copiar a senha. O líder será obrigado a trocá-la no primeiro acesso.')
            ->success()
            ->persistent()
            ->send();
    }
}
