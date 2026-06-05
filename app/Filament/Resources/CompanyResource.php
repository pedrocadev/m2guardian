<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Services\CnpjService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->required()
                    ->mask('99.999.999/9999-99')
                    ->stripCharacters(['.', '/', '-'])
                    ->extraInputAttributes(['maxlength' => 18])
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Este CNPJ já está cadastrado no sistema (pode estar arquivado). Verifique o filtro "Arquivadas" antes de tentar criar novamente.',
                    ])
                    ->live(onBlur: true)
                    ->disabledOn('edit')
                    ->dehydrated()
                    ->helperText('Após salvar, o CNPJ não pode mais ser alterado.')
                    ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                        $digits = preg_replace('/\D/', '', $value ?? '');
                        if (strlen($digits) !== 14) {
                            $fail('CNPJ deve ter 14 dígitos.');
                            return;
                        }
                        if (!CnpjService::validate($digits)) {
                            $fail('CNPJ inválido (dígitos verificadores não conferem).');
                        }
                    })
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $digits = preg_replace('/\D/', '', $state ?? '');
                        if (strlen($digits) !== 14 || !CnpjService::validate($digits)) {
                            return;
                        }
                        $data = CnpjService::lookup($digits);
                        if ($data && !empty($data['razao_social'])) {
                            $set('name', $data['razao_social']);
                            Notification::make()
                                ->title('Razão social carregada do CNPJ')
                                ->body($data['razao_social'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('CNPJ válido, mas não encontrado na base pública')
                                ->body('Preencha a razão social manualmente.')
                                ->warning()
                                ->send();
                        }
                    }),
                Forms\Components\TextInput::make('name')
                    ->label('Razão Social')
                    ->required()
                    ->maxLength(180)
                    ->helperText('Preenchida automaticamente ao informar um CNPJ válido.'),
                Forms\Components\TextInput::make('nickname')
                    ->label('Apelido')
                    ->required()
                    ->maxLength(80)
                    ->placeholder('Ex: M2, ACME, Banco X')
                    ->helperText('Nome curto/informal pra referência interna.'),
            ])->columns(2),

            Forms\Components\Section::make('Líder Principal')
                ->description('Toda empresa precisa de pelo menos um líder responsável.')
                ->schema([
                    Forms\Components\TextInput::make('leader_name')
                        ->label('Nome do líder')
                        ->required()
                        ->maxLength(120),
                    Forms\Components\TextInput::make('leader_email')
                        ->label('E-mail do líder')
                        ->required()
                        ->email()
                        ->maxLength(180)
                        ->helperText('Será o login do líder no painel.'),
                    Forms\Components\TextInput::make('leader_phone')
                        ->label('Telefone')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('leader_role')
                        ->label('Cargo')
                        ->maxLength(60)
                        ->placeholder('Ex: Diretor de TI'),
                ])
                ->columns(2)
                ->visibleOn('create'),

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
                Tables\Columns\TextColumn::make('completion')
                    ->label('Conclusão')
                    ->getStateUsing(function (Company $record) {
                        $total = $record->collaborators()->count();
                        if ($total === 0) return '—';
                        $done = $record->collaborators()->whereNotNull('completed_at')->count();
                        $pct  = round($done / $total * 100);
                        return "{$done}/{$total} ({$pct}%)";
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('avg_score')
                    ->label('Média')
                    ->getStateUsing(function (Company $record) {
                        $avg = $record->collaborators()
                            ->whereNotNull('completed_at')
                            ->whereNotNull('score')
                            ->where('total_questions', '>', 0)
                            ->selectRaw('AVG(score / total_questions * 100) as avg')
                            ->value('avg');
                        return $avg !== null ? round($avg) . '%' : '—';
                    })
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
                Tables\Filters\TrashedFilter::make()
                    ->label('Arquivadas'),
            ])
            ->actions([
                Tables\Actions\Action::make('results')
                    ->label('Ver Resultados')
                    ->icon('heroicon-o-chart-bar')
                    ->color('primary')
                    ->modalHeading(fn (Company $record) => 'Resultados — ' . $record->name)
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar')
                    ->modalContent(fn (Company $record) => view('filament.modals.company-results', [
                        'company'      => $record,
                        'companyScore' => app(\App\Services\ScoreService::class)->forCompany($record),
                    ])),

                Tables\Actions\EditAction::make()->label('Editar'),

                Tables\Actions\DeleteAction::make()
                    ->label('Arquivar')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->modalHeading('Arquivar empresa?')
                    ->modalDescription('A empresa será arquivada (soft delete). Os dados continuam preservados e podem ser restaurados depois. Nenhum registro é apagado.')
                    ->modalSubmitActionLabel('Arquivar')
                    ->visible(fn (Company $record) => !$record->trashed()),

                Tables\Actions\RestoreAction::make()
                    ->label('Desarquivar')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
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
}
