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
use Illuminate\Support\HtmlString;
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
            ])
            ->actions([
                Tables\Actions\Action::make('results')
                    ->label('Ver Resultados')
                    ->icon('heroicon-o-chart-bar')
                    ->color('primary')
                    ->modalHeading(fn (Company $record) => 'Resultados — ' . $record->name)
                    ->modalContent(function (Company $record) {
                        $collaborators = $record->collaborators;
                        $completed     = $collaborators->whereNotNull('completed_at');
                        $pending       = $collaborators->whereNull('completed_at');
                        $total         = $collaborators->count();
                        $rate          = $total > 0 ? round($completed->count() / $total * 100) : 0;
                        $avgScore      = $completed
                            ->filter(fn($c) => $c->score !== null && $c->total_questions > 0)
                            ->map(fn($c) => round($c->score / $c->total_questions * 100))
                            ->avg() ?? 0;

                        $rows = $collaborators->map(function ($c) {
                            $pct = ($c->score !== null && $c->total_questions > 0)
                                ? round($c->score / $c->total_questions * 100) . '%'
                                : '—';
                            $status = $c->completed_at
                                ? '<span style="color:#16a34a;font-weight:700;">✔ Concluído</span>'
                                : '<span style="color:#d97706;font-weight:700;">⏳ Pendente</span>';
                            return "<tr style='border-bottom:1px solid #f0f0f0;'>
                                <td style='padding:8px 12px;'><strong>" . e($c->name ?? '—') . "</strong><br><small style='color:#888;'>" . e($c->email) . "</small></td>
                                <td style='padding:8px 12px;text-align:center;'>{$pct}</td>
                                <td style='padding:8px 12px;'>{$status}</td>
                                <td style='padding:8px 12px;color:#888;font-size:12px;'>" . ($c->completed_at?->format('d/m/Y H:i') ?? '—') . "</td>
                            </tr>";
                        })->implode('');

                        $html = "
                        <div style='font-family:Arial,sans-serif;'>
                            <div style='display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;'>
                                <div style='background:#f9f9f9;border-radius:8px;padding:14px;border-top:3px solid #2563eb;'>
                                    <div style='font-size:10px;color:#888;text-transform:uppercase;letter-spacing:1px;'>Total</div>
                                    <div style='font-size:28px;font-weight:900;'>{$total}</div>
                                </div>
                                <div style='background:#f9f9f9;border-radius:8px;padding:14px;border-top:3px solid #16a34a;'>
                                    <div style='font-size:10px;color:#888;text-transform:uppercase;letter-spacing:1px;'>Concluídos</div>
                                    <div style='font-size:28px;font-weight:900;color:#16a34a;'>{$completed->count()}</div>
                                </div>
                                <div style='background:#f9f9f9;border-radius:8px;padding:14px;border-top:3px solid #CC0000;'>
                                    <div style='font-size:10px;color:#888;text-transform:uppercase;letter-spacing:1px;'>Conclusão</div>
                                    <div style='font-size:28px;font-weight:900;color:#CC0000;'>{$rate}%</div>
                                </div>
                                <div style='background:#f9f9f9;border-radius:8px;padding:14px;border-top:3px solid #d97706;'>
                                    <div style='font-size:10px;color:#888;text-transform:uppercase;letter-spacing:1px;'>Média Acertos</div>
                                    <div style='font-size:28px;font-weight:900;color:#d97706;'>" . round($avgScore) . "%</div>
                                </div>
                            </div>
                            <table style='width:100%;border-collapse:collapse;font-size:13px;'>
                                <thead>
                                    <tr style='background:#f9f9f9;'>
                                        <th style='padding:8px 12px;text-align:left;font-size:11px;color:#888;text-transform:uppercase;'>Colaborador</th>
                                        <th style='padding:8px 12px;text-align:center;font-size:11px;color:#888;text-transform:uppercase;'>Pontuação</th>
                                        <th style='padding:8px 12px;font-size:11px;color:#888;text-transform:uppercase;'>Status</th>
                                        <th style='padding:8px 12px;font-size:11px;color:#888;text-transform:uppercase;'>Concluído em</th>
                                    </tr>
                                </thead>
                                <tbody>{$rows}</tbody>
                            </table>
                        </div>";

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

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
