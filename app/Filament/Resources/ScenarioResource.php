<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScenarioResource\Pages;
use App\Models\Scenario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScenarioResource extends Resource
{
    protected static ?string $model = Scenario::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Cenários';
    protected static ?string $modelLabel = 'Cenário';
    protected static ?string $pluralModelLabel = 'Cenários';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificação')->schema([
                Forms\Components\Select::make('platform')
                    ->label('Plataforma')
                    ->options(['wapp' => 'WhatsApp', 'teams' => 'Microsoft Teams', 'email' => 'E-mail'])
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('label')
                    ->label('Título')
                    ->required()
                    ->maxLength(120)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('avatar')
                    ->label('Emoji / Avatar')
                    ->maxLength(8)
                    ->placeholder('👨‍💼'),
                Forms\Components\ColorPicker::make('bg_color')
                    ->label('Cor de fundo'),
                Forms\Components\TextInput::make('preview')
                    ->label('Descrição prévia')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Configuração')->schema([
                Forms\Components\Select::make('company_id')
                    ->label('Empresa (deixe vazio para padrão M2)')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Ativo', 'draft' => 'Rascunho', 'archived' => 'Arquivado'])
                    ->default('active')
                    ->required(),
                Forms\Components\Toggle::make('is_default')
                    ->label('Cenário padrão M2')
                    ->helperText('Disponível para todas as empresas'),
                Forms\Components\Toggle::make('demo_eligible')
                    ->label('Disponível no Demo')
                    ->helperText('Aparece para empresas com licença Demo'),
            ])->columns(2),

            Forms\Components\Section::make('Conteúdo')->schema([
                Forms\Components\Textarea::make('intro')
                    ->label('Introdução')
                    ->rows(3)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Cenário')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('platform')
                    ->label('Plataforma')
                    ->colors([
                        'success' => 'wapp',
                        'primary' => 'teams',
                        'warning' => 'email',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'wapp' => 'WhatsApp',
                        'teams' => 'Teams',
                        'email' => 'E-mail',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->default('— Padrão M2 —')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Padrão')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('demo_eligible')
                    ->label('Demo')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'draft',
                        'danger' => 'archived',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'Ativo',
                        'draft' => 'Rascunho',
                        'archived' => 'Arquivado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('version')
                    ->label('v.')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Plataforma')
                    ->options(['wapp' => 'WhatsApp', 'teams' => 'Teams', 'email' => 'E-mail']),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(['active' => 'Ativo', 'draft' => 'Rascunho', 'archived' => 'Arquivado']),
                Tables\Filters\TernaryFilter::make('is_default')->label('Apenas padrão M2'),
                Tables\Filters\TernaryFilter::make('demo_eligible')->label('Apenas Demo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->defaultSort('platform');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScenarios::route('/'),
            'create' => Pages\CreateScenario::route('/create'),
            'edit' => Pages\EditScenario::route('/{record}/edit'),
        ];
    }
}
