<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollaboratorResource\Pages;
use App\Mail\CollaboratorInviteMail;
use App\Models\Collaborator;
use App\Models\Leader;
use App\Models\MagicLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class CollaboratorResource extends Resource
{
    protected static ?string $model = Collaborator::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Colaboradores';
    protected static ?string $modelLabel = 'Colaborador';
    protected static ?string $pluralModelLabel = 'Colaboradores';
    protected static ?int $navigationSort = 4;

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
                Forms\Components\Select::make('invited_by_leader_id')
                    ->label('Líder responsável')
                    ->options(fn (Forms\Get $get) => Leader::where('company_id', $get('company_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(180),
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->maxLength(120),
                Forms\Components\TextInput::make('department')
                    ->label('Departamento')
                    ->maxLength(80),
                Forms\Components\Select::make('profile')
                    ->label('Perfil')
                    ->options(['rh' => 'RH', 'financeiro' => 'Financeiro', 'operacao' => 'Operação', 'outro' => 'Outro'])
                    ->default('outro')
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
                    ->default('—'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invitedBy.name')
                    ->label('Líder')
                    ->default('—'),
                Tables\Columns\TextColumn::make('profile')
                    ->label('Perfil')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'rh'         => 'RH',
                        'financeiro' => 'Financeiro',
                        'operacao'   => 'Operação',
                        default      => 'Outro',
                    }),
                Tables\Columns\IconColumn::make('completed_at')
                    ->label('Treinou')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->completed_at !== null)
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Acertos')
                    ->formatStateUsing(fn ($record) => $record->score !== null
                        ? "{$record->score}/{$record->total_questions}"
                        : '—')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Concluído em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->label('Empresa')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('completed')
                    ->label('Status do treinamento')
                    ->placeholder('Todos')
                    ->trueLabel('Concluídos')
                    ->falseLabel('Pendentes')
                    ->queries(
                        true:  fn ($query) => $query->whereNotNull('completed_at'),
                        false: fn ($query) => $query->whereNull('completed_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('send_invite')
                    ->label('Enviar Convite')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->visible(fn (Collaborator $record) => $record->completed_at === null)
                    ->requiresConfirmation()
                    ->modalHeading('Enviar convite por e-mail?')
                    ->modalDescription(fn (Collaborator $record) => "Um magic link será gerado e enviado para {$record->email}.")
                    ->action(function (Collaborator $record) {
                        $record->load('invitedBy.company');
                        $leader = $record->invitedBy;

                        if (!$leader) {
                            Notification::make()->title('Sem líder vinculado')->warning()->send();
                            return;
                        }

                        ['plain_token' => $plainToken] = MagicLink::generateFor(
                            $record, 'collaborator_training', expiresDays: 30
                        );

                        $url = url('/auth/acesso') . '?t=' . $plainToken;

                        try {
                            Mail::to($record->email)->send(new CollaboratorInviteMail($record, $leader, $url));
                            Notification::make()->title('Convite enviado!')->body($record->email)->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Erro ao enviar')->body($e->getMessage())->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('copy_link')
                    ->label('Copiar Link')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->visible(fn (Collaborator $record) => $record->completed_at === null)
                    ->action(function (Collaborator $record, $livewire) {
                        ['plain_token' => $plainToken] = MagicLink::generateFor(
                            $record, 'collaborator_training', expiresDays: 30
                        );
                        $url = url('/auth/acesso') . '?t=' . $plainToken;

                        // Tenta copiar via Clipboard API; fallback pra textarea + execCommand
                        // (cobre HTTPS, HTTP local, e navegadores antigos)
                        $urlJson = json_encode($url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                        $livewire->js(<<<JS
                            (async function() {
                                const url = {$urlJson};
                                let ok = false;
                                try {
                                    if (navigator.clipboard && window.isSecureContext) {
                                        await navigator.clipboard.writeText(url);
                                        ok = true;
                                    }
                                } catch (e) { ok = false; }
                                if (!ok) {
                                    const ta = document.createElement('textarea');
                                    ta.value = url;
                                    ta.style.position = 'fixed';
                                    ta.style.opacity = '0';
                                    document.body.appendChild(ta);
                                    ta.select();
                                    try { document.execCommand('copy'); } catch (e) {}
                                    document.body.removeChild(ta);
                                }
                            })();
                        JS);

                        Notification::make()
                            ->title('✓ Link copiado!')
                            ->body($url)
                            ->success()
                            ->duration(8000)
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_send_invite')
                    ->label('Enviar Convite para Selecionados')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar convites em massa?')
                    ->modalDescription('Serão enviados e-mails com magic links para todos os colaboradores selecionados que ainda não concluíram.')
                    ->action(function ($records) {
                        $sent = 0;
                        $failed = 0;

                        foreach ($records as $record) {
                            if ($record->completed_at !== null) continue;

                            $record->load('invitedBy.company');
                            $leader = $record->invitedBy;
                            if (!$leader) { $failed++; continue; }

                            ['plain_token' => $plainToken] = MagicLink::generateFor(
                                $record, 'collaborator_training', expiresDays: 30
                            );
                            $url = url('/auth/acesso') . '?t=' . $plainToken;

                            try {
                                Mail::to($record->email)->send(new CollaboratorInviteMail($record, $leader, $url));
                                $sent++;
                            } catch (\Exception $e) {
                                $failed++;
                            }
                        }

                        Notification::make()
                            ->title("{$sent} convite(s) enviado(s)" . ($failed > 0 ? ", {$failed} falhou(ram)" : ''))
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('completed_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCollaborators::route('/'),
            'create' => Pages\CreateCollaborator::route('/create'),
        ];
    }
}
