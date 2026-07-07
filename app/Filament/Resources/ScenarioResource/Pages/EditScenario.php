<?php

namespace App\Filament\Resources\ScenarioResource\Pages;

use App\Filament\Resources\ScenarioResource;
use App\Models\ScenarioVersion;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScenario extends EditRecord
{
    protected static string $resource = ScenarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save_top')
                ->label('Salvar')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),

            Actions\Action::make('view_versions')
                ->label('Histórico de versões')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->url(fn () => null)
                ->modalContent(function () {
                    $versions = $this->record->versions()->latest()->take(10)->get();
                    return view('filament.scenario-versions', ['versions' => $versions]);
                })
                ->modalHeading('Histórico de versões')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar'),

            Actions\DeleteAction::make()->label('Arquivar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_admin_id'] = auth('admin')->id();
        $data['version'] = $this->record->version + 1;
        return $data;
    }

    protected function afterSave(): void
    {
        ScenarioVersion::create([
            'scenario_id'       => $this->record->id,
            'version'           => $this->record->version,
            'content_snapshot'  => $this->record->content,
            'edited_by_admin_id' => auth('admin')->id(),
            'edit_summary'      => 'Editado via painel admin',
            'created_at'        => now(),
        ]);
    }
}
