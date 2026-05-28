<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderResource\Pages;
use App\Mail\LeaderInviteMail;
use App\Models\Leader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeaderResource extends Resource
{
    protected static ?string $model = Leader::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Líderes';
    protected static ?string $modelLabel = 'Líder';
    protected static ?string $pluralModelLabel = 'Líderes';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('company_id')
                    ->label('Empresa')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(120),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail (usuário de acesso)')
                    ->email()
                    ->required()
                    ->maxLength(180)
                    ->helperText('Este será o login do líder no painel.')
                    ->unique(
                        table: 'leaders',
                        column: 'email',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule, Forms\Get $get) {
                            return $rule->where('company_id', $get('company_id'));
                        },
                    )
                    ->validationMessages([
                        'unique' => 'Já existe um líder com este e-mail para a empresa selecionada.',
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefone')
                    ->maxLength(20),
                Forms\Components\TextInput::make('role_label')
                    ->label('Cargo')
                    ->maxLength(60)
                    ->placeholder('Ex: Diretor de TI'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pendente (sem senha)',
                        'active'    => 'Ativo',
                        'suspended' => 'Suspenso',
                    ])
                    ->default('pending')
                    ->required(),
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
                    ->label('E-mail')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('password')
                    ->label('Senha')
                    ->alignCenter()
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->password !== null)
                    ->tooltip(fn ($record) => $record->password ? 'Senha definida' : 'Sem senha — clique em "Gerar Senha"'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger'  => 'suspended',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending'   => 'Pendente',
                        'active'    => 'Ativo',
                        'suspended' => 'Suspenso',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último acesso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Nunca acessou'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->label('Empresa')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pendente',
                        'active'    => 'Ativo',
                        'suspended' => 'Suspenso',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('generate_credentials')
                    ->label(fn (Leader $record) => $record->password ? 'Resetar Senha' : 'Gerar Senha')
                    ->icon('heroicon-o-key')
                    ->color(fn (Leader $record) => $record->password ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Leader $record) => $record->password
                        ? 'Resetar senha do líder?'
                        : 'Gerar primeira senha do líder?'
                    )
                    ->modalDescription(fn (Leader $record) => $record->password
                        ? 'Uma nova senha será gerada e a senha atual será invalidada. O líder não conseguirá mais acessar com a senha antiga.'
                        : 'Uma senha aleatória será gerada para que o líder possa acessar o painel.'
                    )
                    ->action(function (Leader $record) {
                        $newPassword = self::generatePassword();
                        $record->update([
                            'password'        => $newPassword,
                            'password_set_at' => now(),
                            'failed_attempts' => 0,
                            'locked_until'    => null,
                            'status'          => $record->status === 'pending' ? 'active' : $record->status,
                        ]);

                        // Store in session to show in next action (show_credentials)
                        session()->flash('leader_new_password_' . $record->id, $newPassword);

                        Notification::make()
                            ->title('Senha gerada!')
                            ->body('Clique em "Mostrar Credenciais" para copiar a senha (visível apenas uma vez).')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('show_credentials')
                    ->label('Mostrar Credenciais')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('primary')
                    ->modalHeading(fn (Leader $record) => 'Credenciais — ' . $record->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar')
                    ->modalContent(function (Leader $record) {
                        $password = session('leader_new_password_' . $record->id);

                        if (!$password) {
                            // Auto-generate if there's no stored password to show
                            $password = self::generatePassword();
                            $record->update([
                                'password'        => $password,
                                'password_set_at' => now(),
                                'failed_attempts' => 0,
                                'locked_until'    => null,
                                'status'          => $record->status === 'pending' ? 'active' : $record->status,
                            ]);
                        }

                        return view('filament.leader-credentials', [
                            'leader'   => $record,
                            'password' => $password,
                            'loginUrl' => route('leader.login'),
                        ]);
                    }),

                Tables\Actions\Action::make('send_credentials')
                    ->label('Enviar por E-mail')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar credenciais por e-mail?')
                    ->modalDescription(fn (Leader $record) => "Uma nova senha será gerada e enviada para {$record->email}.")
                    ->action(function (Leader $record) {
                        $record->load('company');

                        $newPassword = self::generatePassword();
                        $record->update([
                            'password'        => $newPassword,
                            'password_set_at' => now(),
                            'failed_attempts' => 0,
                            'locked_until'    => null,
                            'status'          => $record->status === 'pending' ? 'active' : $record->status,
                        ]);

                        try {
                            Mail::to($record->email)->send(new LeaderInviteMail($record, $newPassword));
                            Notification::make()
                                ->title('Credenciais enviadas!')
                                ->body("E-mail enviado para {$record->email}.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Erro ao enviar')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Excluir'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function generatePassword(): string
    {
        // 12 chars: alphanumeric, easy-to-read (no confusing 0/O/1/l/I)
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $pwd = '';
        for ($i = 0; $i < 12; $i++) {
            $pwd .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $pwd;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaders::route('/'),
            'create' => Pages\CreateLeader::route('/create'),
            'edit'   => Pages\EditLeader::route('/{record}/edit'),
        ];
    }
}
