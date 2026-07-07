<?php

namespace App\Filament\Resources\ScenarioResource\Pages;

use App\Filament\Resources\ScenarioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScenario extends CreateRecord
{
    protected static string $resource = ScenarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_top')
                ->label('Criar cenário')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('create'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['updated_by_admin_id'] = auth('admin')->id();
        return $data;
    }
}
