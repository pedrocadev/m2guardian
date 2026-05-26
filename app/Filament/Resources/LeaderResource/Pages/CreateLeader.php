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
        $leader = $this->record;

        // Gera senha automaticamente
        $newPassword = LeaderResource::generatePassword();
        $leader->update([
            'password'        => $newPassword,
            'password_set_at' => now(),
            'status'          => 'active',
        ]);

        // Armazena na sessão para o admin ver via "Mostrar Credenciais"
        session()->flash('leader_new_password_' . $leader->id, $newPassword);

        Notification::make()
            ->title('Líder criado e senha gerada!')
            ->body('Clique em "Mostrar Credenciais" na linha do líder para copiar a senha (visível apenas uma vez).')
            ->success()
            ->persistent()
            ->send();
    }
}
