<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformFeedbackResource\Pages;
use App\Models\PlatformFeedback;
use App\Models\Scenario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlatformFeedbackResource extends Resource
{
    protected static ?string $model = PlatformFeedback::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Feedbacks';
    protected static ?string $modelLabel = 'Feedback de Plataforma';
    protected static ?string $pluralModelLabel = 'Feedbacks de Plataforma';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('platform')
                ->label('Plataforma')
                ->options(Scenario::PLATFORM_LABELS)
                ->disabled()
                ->hintIcon(
                    'heroicon-m-information-circle',
                    tooltip: 'Cada plataforma tem um único feedback. Não é possível trocar depois de criado.'
                ),
            Forms\Components\TextInput::make('title')
                ->label('Título principal (aparece no cabeçalho do modal)')
                ->required()
                ->maxLength(120)
                ->columnSpanFull()
                ->hintIcon(
                    'heroicon-m-information-circle',
                    tooltip: 'Título fixo que aparece no cabeçalho do modal em todos os slides.'
                ),
            Forms\Components\FileUpload::make('guardian_image')
                ->label('Imagem do Guardião (exibida à esquerda do balão)')
                ->image()
                ->imageEditor()
                ->imageResizeMode('contain')
                ->imageResizeTargetWidth(600)
                ->disk('public')
                ->directory('platform-feedbacks/guardians')
                ->maxSize(2048)
                ->columnSpanFull()
                ->hintIcon(
                    'heroicon-m-information-circle',
                    tooltip: 'Upload da imagem do Guardião que aparece à esquerda do balão de fala. Se vazio, usa o mascote padrão do sistema. Recomenda-se PNG com fundo transparente, corpo inteiro, retrato (mais alto que largo).'
                ),
            Forms\Components\Repeater::make('slides')
                ->label('Slides do feedback (aparecem em sequência com setas de navegação)')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Título do slide')
                        ->maxLength(120)
                        ->hintIcon(
                            'heroicon-m-information-circle',
                            tooltip: 'Aparece em destaque no topo do slide. Ex: "Sinais de alerta", "Regra de ouro", "Resumo".'
                        ),
                    Forms\Components\RichEditor::make('body')
                        ->label('Conteúdo do slide')
                        ->required()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'strike',
                            'h2',
                            'h3',
                            'bulletList',
                            'orderedList',
                            'blockquote',
                            'link',
                            'undo',
                            'redo',
                        ])
                        ->hintIcon(
                            'heroicon-m-information-circle',
                            tooltip: 'Texto do slide com formatação rica (negrito, itálico, listas, links, títulos). Use listas para tópicos e blockquote para destacar regras.'
                        ),
                ])
                ->columnSpanFull()
                ->reorderable()
                ->reorderableWithButtons()
                ->cloneable()
                ->collapsible()
                ->collapsed()
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Slide sem título')
                ->addActionLabel('Adicionar slide')
                ->minItems(1)
                ->defaultItems(1)
                ->hintIcon(
                    'heroicon-m-information-circle',
                    tooltip: 'Cada slide é uma "telinha" que o colaborador navega com setas. Recomenda-se de 3 a 5 slides por plataforma pra não cansar.'
                ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform')
                    ->label('Plataforma')
                    ->formatStateUsing(fn ($state) => Scenario::PLATFORM_LABELS[$state] ?? $state)
                    ->badge()
                    ->color(fn ($state) => Scenario::PLATFORM_COLORS[$state] ?? 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(60)
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Editado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('platform')
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformFeedbacks::route('/'),
            'edit'  => Pages\EditPlatformFeedback::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
