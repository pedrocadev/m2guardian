<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScenarioResource\Pages;
use App\Models\Scenario;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                    ->options(['wapp' => 'WhatsApp', 'teams' => 'Microsoft Teams', 'email' => 'E-mail', 'outro' => 'Outra Plataforma'])
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(60)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('label')
                    ->label('Título do cenário')
                    ->required()
                    ->maxLength(120)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('avatar')
                    ->label('Emoji')
                    ->maxLength(8)
                    ->placeholder('👨‍💼'),
                Forms\Components\ColorPicker::make('bg_color')
                    ->label('Cor de fundo'),
                Forms\Components\TextInput::make('preview')
                    ->label('Descrição prévia (lista)')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('intro')
                    ->label('Introdução (mostrada antes do chat)')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Configuração')->schema([
                Forms\Components\Select::make('company_id')
                    ->label('Empresa (vazio = padrão M2)')
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
                    ->helperText('Visível para todas as empresas'),
                Forms\Components\Toggle::make('demo_eligible')
                    ->label('Disponível no Demo')
                    ->helperText('Aparece nos 3 cenários do plano Demo'),
                CheckboxList::make('target_areas')
                    ->label('Áreas-alvo (para quais departamentos este cenário se aplica)')
                    ->options(Scenario::AREAS)
                    ->columns(3)
                    ->helperText('Marque "Todos" se aplicar a qualquer colaborador, ou selecione áreas específicas. Útil para escolher quais cenários enviar a cada colaborador.')
                    ->default(['todos'])
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Editor de Mensagens')
                ->description('Monte o roteiro do cenário. Alterne entre mensagens de texto e perguntas.')
                ->schema([
                    Forms\Components\Repeater::make('content.messages')
                        ->label('')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Tipo de bloco')
                                ->options([
                                    'text'     => '💬 Mensagem de texto',
                                    'question' => '❓ Pergunta',
                                ])
                                ->required()
                                ->live()
                                ->columnSpanFull(),

                            // ── Bloco de texto ──────────────────────────
                            Forms\Components\Select::make('from')
                                ->label('Quem envia')
                                ->options(['them' => '← Deles (esquerda)', 'me' => '→ Eu (direita)'])
                                ->default('them')
                                ->required()
                                ->visible(fn (Get $get) => $get('type') === 'text'),

                            Forms\Components\Textarea::make('body')
                                ->label('Texto da mensagem')
                                ->rows(2)
                                ->required()
                                ->visible(fn (Get $get) => $get('type') === 'text')
                                ->columnSpanFull(),

                            // ── Bloco de pergunta ────────────────────────
                            Forms\Components\TextInput::make('prompt')
                                ->label('Enunciado da pergunta')
                                ->required()
                                ->visible(fn (Get $get) => $get('type') === 'question')
                                ->columnSpanFull(),

                            Forms\Components\Repeater::make('options')
                                ->label('Opções de resposta')
                                ->schema([
                                    Forms\Components\TextInput::make('key')
                                        ->label('Chave (a, b, c...)')
                                        ->maxLength(4)
                                        ->required()
                                        ->placeholder('a'),
                                    Forms\Components\Toggle::make('correct')
                                        ->label('Resposta correta?')
                                        ->inline(false),
                                    Forms\Components\TextInput::make('text')
                                        ->label('Texto da opção')
                                        ->required()
                                        ->columnSpan(2),
                                    Forms\Components\Textarea::make('feedback')
                                        ->label('Feedback ao selecionar')
                                        ->rows(2)
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                                ->columns(4)
                                ->minItems(2)
                                ->maxItems(4)
                                ->addActionLabel('+ Adicionar opção')
                                ->visible(fn (Get $get) => $get('type') === 'question')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->addActionLabel('+ Adicionar bloco')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => match ($state['type'] ?? null) {
                            'text'     => '💬 ' . Str::limit($state['body'] ?? '...', 60),
                            'question' => '❓ ' . Str::limit($state['prompt'] ?? '...', 60),
                            default    => 'Novo bloco',
                        })
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
                Tables\Columns\TextColumn::make('platform')
                    ->label('Plataforma')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'wapp'  => 'success',
                        'teams' => 'primary',
                        'email' => 'warning',
                        'outro' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'wapp'  => 'WhatsApp',
                        'teams' => 'Teams',
                        'email' => 'E-mail',
                        'outro' => 'Outra',
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
                Tables\Columns\TextColumn::make('target_areas')
                    ->label('Áreas-alvo')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn ($state) => Scenario::AREAS[$state] ?? $state)
                    ->color('info')
                    ->limit(40),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'draft'    => 'warning',
                        'archived' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'   => 'Ativo',
                        'draft'    => 'Rascunho',
                        'archived' => 'Arquivado',
                        default    => $state,
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
                // (Filtro de área-alvo removido temporariamente — usar filtros de plataforma e demo)
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicar')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Scenario $record) {
                        $new = $record->replicate();
                        $new->slug = $record->slug . '-copia-' . now()->format('YmdHis');
                        $new->label = $record->label . ' (cópia)';
                        $new->status = 'draft';
                        $new->version = 1;
                        $new->save();
                    })
                    ->successNotificationTitle('Cenário duplicado como rascunho'),
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
            'index'  => Pages\ListScenarios::route('/'),
            'create' => Pages\CreateScenario::route('/create'),
            'edit'   => Pages\EditScenario::route('/{record}/edit'),
        ];
    }
}
