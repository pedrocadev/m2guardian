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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

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
                    ->required()
                    ->disabled(fn (?Leader $record) => $record?->is_primary === true)
                    ->helperText(fn (?Leader $record) => $record?->is_primary
                        ? 'Líder principal — vínculo com a empresa é permanente.'
                        : null),
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(120)
                    ->disabled(fn (?Leader $record) => $record?->is_primary === true)
                    ->helperText(fn (?Leader $record) => $record?->is_primary
                        ? 'Nome do líder principal não pode ser alterado.'
                        : null),
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
                    ->sortable()
                    ->description(fn (Leader $record) => $record->is_primary ? '★ Líder Principal' : null)
                    ->weight(fn (Leader $record) => $record->is_primary ? 'bold' : 'normal'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('password_state')
                    ->label('Senha')
                    ->alignCenter()
                    ->getStateUsing(function (Leader $record): string {
                        if ($record->password === null) {
                            return 'none';
                        }
                        return $record->must_change_password ? 'temp' : 'personal';
                    })
                    ->colors([
                        'gray'    => 'none',
                        'warning' => 'temp',
                        'success' => 'personal',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'none'     => 'Sem senha',
                        'temp'     => 'Temporária',
                        'personal' => 'Pessoal',
                        default    => $state,
                    })
                    ->tooltip(fn ($state) => match ($state) {
                        'none'     => 'Sem senha — clique em "Gerar Senha"',
                        'temp'     => 'Senha gerada pelo admin — o líder ainda não trocou',
                        'personal' => 'Senha foi trocada pelo líder — não é mais visível',
                        default    => null,
                    }),
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
                Tables\Filters\TrashedFilter::make()->label('Arquivados'),
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
                        self::resetLeaderPassword($record);

                        Notification::make()
                            ->title('Senha gerada!')
                            ->body('Clique em "Mostrar Credenciais" para copiar a senha. O líder será obrigado a trocá-la no primeiro acesso.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('show_credentials')
                    ->label('Mostrar Credenciais')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('primary')
                    ->visible(fn (Leader $record) => $record->must_change_password && session()->has('leader_new_password_' . $record->id))
                    ->modalHeading(fn (Leader $record) => 'Credenciais — ' . $record->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar')
                    ->modalContent(function (Leader $record) {
                        $encrypted = session('leader_new_password_' . $record->id);
                        return view('filament.leader-credentials', [
                            'leader'   => $record,
                            'password' => $encrypted ? decrypt($encrypted) : null,
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
                        // Gera em memória e tenta enviar ANTES de persistir. Se o envio falhar,
                        // a senha antiga do líder permanece intacta — nada é rotacionado à toa.
                        $newPassword = self::generatePassword();

                        try {
                            Mail::to($record->email)->send(new LeaderInviteMail($record, $newPassword));
                        } catch (\Exception $e) {
                            \Log::error('Falha ao enviar credenciais do líder', [
                                'leader_id' => $record->id,
                                'error'     => $e->getMessage(),
                            ]);
                            Notification::make()
                                ->title('Falha no envio')
                                ->body('Não foi possível enviar o e-mail. A senha atual do líder foi mantida — use "Resetar Senha" + "Mostrar Credenciais" para enviar manualmente.')
                                ->danger()
                                ->persistent()
                                ->send();
                            return;
                        }

                        self::persistNewPassword($record, $newPassword);

                        Notification::make()
                            ->title('Credenciais enviadas!')
                            ->body("E-mail enviado para {$record->email}.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->label('Editar'),

                Tables\Actions\DeleteAction::make()
                    ->label('Arquivar')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->modalHeading('Arquivar líder?')
                    ->modalDescription('O líder será arquivado (soft delete). Os dados continuam preservados e podem ser restaurados depois.')
                    ->modalSubmitActionLabel('Arquivar')
                    ->visible(fn (Leader $record) => !$record->trashed())
                    ->before(function (Leader $record, Tables\Actions\DeleteAction $action) {
                        if (!$record->canBeArchived()) {
                            Notification::make()
                                ->title('Bloqueado: último líder da empresa')
                                ->body('Esta empresa precisa ter pelo menos um líder. Cadastre outro líder antes de arquivar este.')
                                ->danger()
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),

                Tables\Actions\RestoreAction::make()
                    ->label('Desarquivar')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function generatePassword(): string
    {
        // Sem 0/O/1/l/I para evitar confusão ao ditar/copiar por telefone.
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $pwd = '';
        for ($i = 0; $i < 12; $i++) {
            $pwd .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $pwd;
    }

    /**
     * Gera nova senha aleatória para o líder, marca troca obrigatória no próximo login,
     * zera lockout, ativa a conta se estava pendente e guarda a senha na sessão do admin
     * para exibição via "Mostrar Credenciais". Retorna a senha em texto puro.
     */
    public static function resetLeaderPassword(Leader $record): string
    {
        $newPassword = self::generatePassword();
        self::persistNewPassword($record, $newPassword);
        return $newPassword;
    }

    /**
     * Persiste uma senha já gerada. Separado do resetLeaderPassword para permitir
     * o fluxo "envia primeiro, persiste depois" no envio de credenciais por e-mail:
     * se o Mail::send falhar, a senha antiga do líder permanece válida.
     */
    private static function persistNewPassword(Leader $record, string $newPassword): void
    {
        $record->update([
            'password'             => $newPassword,
            'password_set_at'      => now(),
            'must_change_password' => true,
            'failed_attempts'      => 0,
            'locked_until'         => null,
            'status'               => $record->status === 'pending' ? 'active' : $record->status,
        ]);

        // Encrypt evita expor a senha em plaintext se o session driver for file/database
        // (com Redis efêmero o ganho é menor, mas mantém defesa em profundidade).
        session()->put('leader_new_password_' . $record->id, encrypt($newPassword));
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
