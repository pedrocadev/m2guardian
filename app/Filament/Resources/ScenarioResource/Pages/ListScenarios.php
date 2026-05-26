<?php

namespace App\Filament\Resources\ScenarioResource\Pages;

use App\Filament\Resources\ScenarioResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListScenarios extends ListRecords
{
    protected static string $resource = ScenarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo Cenário'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-o-rectangle-stack'),

            'wapp' => Tab::make('WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->modifyQueryUsing(fn ($query) => $query->where('platform', 'wapp')),

            'teams' => Tab::make('Microsoft Teams')
                ->icon('heroicon-o-users')
                ->modifyQueryUsing(fn ($query) => $query->where('platform', 'teams')),

            'email' => Tab::make('E-mail')
                ->icon('heroicon-o-envelope')
                ->modifyQueryUsing(fn ($query) => $query->where('platform', 'email')),

            'outro' => Tab::make('Outras Plataformas')
                ->icon('heroicon-o-globe-alt')
                ->modifyQueryUsing(fn ($query) => $query->where('platform', 'outro')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}
