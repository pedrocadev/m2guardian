<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Equipe M2';
    protected static ?string $modelLabel = 'Admin';
    protected static ?string $pluralModelLabel = 'Equipe M2';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(120),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(180),
                Forms\Components\Select::make('role')
                    ->label('Perfil')
                    ->options(['super' => 'Super Admin', 'operator' => 'Operador'])
                    ->default('operator')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Ativo', 'suspended' => 'Suspenso', 'disabled' => 'Desabilitado'])
                    ->default('active')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->minLength(8)
                    ->helperText('Deixe em branco para manter a senha atual (ao editar)'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Perfil')
                    ->colors(['danger' => 'super', 'primary' => 'operator'])
                    ->formatStateUsing(fn ($state) => $state === 'super' ? 'Super Admin' : 'Operador'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors(['success' => 'active', 'warning' => 'suspended', 'danger' => 'disabled'])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'Ativo', 'suspended' => 'Suspenso', 'disabled' => 'Desabilitado', default => $state
                    }),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último acesso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth('admin')->user()?->isSuper() ?? false;
    }
}
