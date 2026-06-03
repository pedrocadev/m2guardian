<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\Release;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReleaseResource extends Resource
{
    protected static ?string $model = Release::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Atualizações';
    protected static ?string $modelLabel = 'Atualização';
    protected static ?string $pluralModelLabel = 'Atualizações';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Conteúdo da atualização')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(180)
                    ->placeholder('Ex: Cadastro de empresa com CNPJ + arquivamento'),

                Forms\Components\DatePicker::make('released_at')
                    ->label('Data da atualização')
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y')
                    ->native(false),

                Forms\Components\Textarea::make('content')
                    ->label('Conteúdo (Markdown)')
                    ->required()
                    ->rows(14)
                    ->columnSpanFull()
                    ->helperText(new \Illuminate\Support\HtmlString(
                        'Aceita <strong>Markdown</strong>: <code>**negrito**</code>, listas com <code>- </code>, títulos com <code>### </code>, <code>`código`</code>.<br>'
                        . 'Placeholders disponíveis: <code>{nome}</code> (primeiro nome), <code>{nome_completo}</code>, <code>{email}</code> do admin que vê o popup.'
                    )),

                Forms\Components\Toggle::make('published')
                    ->label('Publicada')
                    ->helperText('Quando ligada, aparece no popup pros admins na próxima visita ao /admin.')
                    ->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('released_at')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\IconColumn::make('published')
                    ->label('Publicada')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('published')->label('Publicada'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Excluir'),
            ])
            ->defaultSort('released_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReleases::route('/'),
            'create' => Pages\CreateRelease::route('/create'),
            'edit'   => Pages\EditRelease::route('/{record}/edit'),
        ];
    }
}
