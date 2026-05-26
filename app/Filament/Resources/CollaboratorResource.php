<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollaboratorResource\Pages;
use App\Models\Collaborator;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CollaboratorResource extends Resource
{
    protected static ?string $model = Collaborator::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Colaboradores';
    protected static ?string $modelLabel = 'Colaborador';
    protected static ?string $pluralModelLabel = 'Colaboradores';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invitedBy.name')
                    ->label('Convidado por')
                    ->default('—'),
                Tables\Columns\BadgeColumn::make('profile')
                    ->label('Perfil')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'rh' => 'RH',
                        'financeiro' => 'Financeiro',
                        'operacao' => 'Operação',
                        default => 'Outro',
                    }),
                Tables\Columns\IconColumn::make('completed_at')
                    ->label('Treinou')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->completed_at !== null)
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Acertos')
                    ->formatStateUsing(fn ($record) => $record->score !== null
                        ? "{$record->score}/{$record->total_questions}"
                        : '—')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Concluído em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->label('Empresa')
                    ->relationship('company', 'name'),
                Tables\Filters\TernaryFilter::make('completed')
                    ->label('Status do treinamento')
                    ->placeholder('Todos')
                    ->trueLabel('Concluídos')
                    ->falseLabel('Pendentes')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('completed_at'),
                        false: fn ($query) => $query->whereNull('completed_at'),
                    ),
            ])
            ->actions([])
            ->defaultSort('completed_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollaborators::route('/'),
        ];
    }
}
