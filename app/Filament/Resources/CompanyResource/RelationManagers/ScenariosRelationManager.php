<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Filament\Resources\ScenarioResource;
use App\Models\Scenario;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Lista os cenarios vinculados a uma empresa via pivot company_scenario.
 *
 * - Aparece como aba "Cenarios vinculados" na tela de edicao da empresa.
 * - Attach: vincular um cenario existente (nao cria novo aqui).
 * - Detach: desvincular (nao apaga o cenario, so remove o vinculo).
 * - "Abrir" leva pro form do ScenarioResource (nova aba).
 * - Filtrado por plataforma pra facilitar navegacao.
 */
class ScenariosRelationManager extends RelationManager
{
    protected static string $relationship = 'scenarios';
    protected static ?string $recordTitleAttribute = 'label';
    protected static ?string $title = 'Cenários vinculados';
    protected static ?string $modelLabel = 'cenário';
    protected static ?string $pluralModelLabel = 'cenários';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Cenário')
                    ->description(fn (Scenario $r) => $r->slug)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('platform')
                    ->label('Plataforma')
                    ->badge()
                    ->color(fn (string $state) => Scenario::PLATFORM_COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state) => Scenario::PLATFORM_LABELS[$state] ?? $state),
                Tables\Columns\TextColumn::make('question_count')
                    ->label('Perguntas')
                    ->alignCenter()
                    ->state(fn (Scenario $r) => collect($r->content['messages'] ?? [])
                        ->where('type', 'question')
                        ->count()),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Padrão M2')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => Scenario::STATUS_COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state) => Scenario::STATUS_LABELS[$state] ?? $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Plataforma')
                    ->options(Scenario::PLATFORM_LABELS),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Vincular cenário')
                    ->preloadRecordSelect()
                    ->multiple()
                    ->recordSelectSearchColumns(['label', 'slug']),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Abrir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Scenario $r) => ScenarioResource::getUrl('edit', ['record' => $r]))
                    ->openUrlInNewTab(),
                Tables\Actions\DetachAction::make()
                    ->label('Desvincular')
                    ->modalHeading('Desvincular cenário')
                    ->modalDescription('O cenário NÃO será apagado — apenas o vínculo com esta empresa será removido.')
                    ->modalSubmitActionLabel('Desvincular'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()->label('Desvincular selecionados'),
                ]),
            ])
            ->defaultSort('platform')
            ->emptyStateHeading('Nenhum cenário vinculado')
            ->emptyStateDescription('Esta empresa está usando o catálogo padrão M2 como fallback. Vincule cenários específicos aqui para isolá-los.')
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }
}
