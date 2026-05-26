<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Empresas';
    protected static ?string $modelLabel = 'Empresa';
    protected static ?string $pluralModelLabel = 'Empresas';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dados da Empresa')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome da Empresa')
                    ->required()
                    ->maxLength(180)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('slug', Str::slug($state ?? ''));
                    }),
                Forms\Components\TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->mask('99.999.999/9999-99')
                    ->maxLength(14),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(60)
                    ->helperText('Gerado automaticamente a partir do nome. Pode editar se necessário.')
                    ->prefix('m2guardian.com.br/'),
            ])->columns(2),

            Forms\Components\Section::make('Licença')->schema([
                Forms\Components\Select::make('license')
                    ->label('Tipo de Licença')
                    ->options(['demo' => 'Demo', 'pro' => 'Pro'])
                    ->required()
                    ->live()
                    ->default('demo'),
                Forms\Components\TextInput::make('max_collaborators')
                    ->label('Máx. Colaboradores')
                    ->numeric()
                    ->required()
                    ->default(3)
                    ->minValue(1),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Ativo',
                        'suspended' => 'Suspenso',
                        'expired' => 'Expirado',
                    ])
                    ->required()
                    ->default('active'),
                Forms\Components\DateTimePicker::make('license_expires_at')
                    ->label('Expiração da Licença')
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Contato')->schema([
                Forms\Components\TextInput::make('contact_email')
                    ->label('Email de Contato')
                    ->email()
                    ->maxLength(180),
                Forms\Components\TextInput::make('contact_phone')
                    ->label('Telefone')
                    ->maxLength(20),
                Forms\Components\Textarea::make('notes')
                    ->label('Anotações Internas')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('license')
                    ->label('Licença')
                    ->colors([
                        'warning' => 'demo',
                        'success' => 'pro',
                    ])
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'expired',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'Ativo',
                        'suspended' => 'Suspenso',
                        'expired' => 'Expirado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('max_collaborators')
                    ->label('Máx. Colab.')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('leaders_count')
                    ->label('Líderes')
                    ->counts('leaders')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('collaborators_count')
                    ->label('Colaboradores')
                    ->counts('collaborators')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('license')
                    ->label('Licença')
                    ->options(['demo' => 'Demo', 'pro' => 'Pro']),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Ativo',
                        'suspended' => 'Suspenso',
                        'expired' => 'Expirado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_admin_id'] = auth('admin')->id();
        return $data;
    }
}
