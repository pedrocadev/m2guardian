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
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Wizard::make([

                // ── Passo 1: Identificação ──────────────────────────
                Forms\Components\Wizard\Step::make('Identificação')
                    ->icon('heroicon-o-identification')
                    ->description('Nome, plataforma e categoria')
                    ->schema([
                        Forms\Components\Select::make('platform')
                            ->label('Plataforma')
                            ->options(Scenario::PLATFORM_LABELS)
                            ->required()
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Plataforma onde o ataque é simulado. Define o visual do chat (cores, layout, ícones do WhatsApp/Teams/E-mail) que o colaborador vai ver durante o treinamento.'
                            ),
                        Forms\Components\Select::make('category')
                            ->label('Categoria comportamental')
                            ->options(\App\Models\Scenario::CATEGORIES)
                            ->required()
                            ->searchable()
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Habilidade comportamental que este cenário testa. Usada no relatório de "Pontos fortes" e "Pontos de evolução" do colaborador. Escolha a categoria que melhor representa o desafio principal do cenário (ex: cenário com link suspeito → Validação de links; cenário com pressão de tempo → Solicitações urgentes).'
                            ),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(60)
                            ->unique(ignoreRecord: true)
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Identificador único interno do cenário, em formato URL-friendly (ex: "ceo-wapp", "fatura-falsa"). Não é visível ao colaborador. Use letras minúsculas, números e hífens.'
                            ),
                        Forms\Components\TextInput::make('label')
                            ->label('Título do cenário')
                            ->required()
                            ->maxLength(120)
                            ->columnSpanFull()
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Título exibido ao colaborador na lista de missões e no topo do chat. Seja descritivo (ex: "Diretoria Executiva", "Fatura Suspeita do Fornecedor").'
                            ),
                        Forms\Components\TextInput::make('avatar')
                            ->label('Emoji')
                            ->maxLength(8)
                            ->placeholder('👨‍💼')
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Emoji que aparece como avatar do "remetente" no chat. Pode ser uma pessoa (👨‍💼, 👩‍💻), uma empresa (🏦, 📦) ou outro símbolo (📧, 💼).'
                            ),
                        Forms\Components\ColorPicker::make('bg_color')
                            ->label('Cor de fundo')
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Cor de fundo do avatar (atrás do emoji). Use cores corporativas relacionadas ao "remetente" simulado (ex: azul-marinho pra diretoria, verde pra banco).'
                            ),
                        Forms\Components\TextInput::make('preview')
                            ->label('Descrição prévia (lista)')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Texto curto exibido no card de "missão" antes do colaborador iniciar (ex: "Mensagem urgente do CEO pedindo transferência"). Aparece sob o título na lista de cenários.'
                            ),
                        Forms\Components\Textarea::make('intro')
                            ->label('Introdução (mostrada antes do chat)')
                            ->rows(2)
                            ->columnSpanFull()
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Texto opcional exibido em uma caixa no topo do chat antes das mensagens começarem. Use pra dar contexto extra ao colaborador (ex: "Você acabou de receber esta mensagem no celular corporativo às 14h32"). Deixe vazio se não precisar.'
                            ),
                    ])->columns(2),

                // ── Passo 2: Configuração ───────────────────────────
                Forms\Components\Wizard\Step::make('Configuração')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->description('Empresa, status e áreas-alvo')
                    ->schema([
                        Forms\Components\Select::make('companies')
                            ->label('Empresas vinculadas')
                            ->multiple()
                            ->relationship('companies', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->helperText('Uma empresa que tem QUALQUER cenário vinculado vê APENAS os vinculados a ela. Empresas sem vínculos veem só os cenários marcados como "Cenário padrão M2". Deixe vazio pra tratar como padrão M2.'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Scenario::STATUS_LABELS)
                            ->default('active')
                            ->required()
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Ativo = visível pros colaboradores no treinamento. Rascunho = só você vê no painel (em construção). Arquivado = oculto, mantido pra histórico/auditoria.'
                            ),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Cenário padrão M2')
                            ->helperText('Visível para todas as empresas')
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Ligue se este cenário deve fazer parte do catálogo padrão da M2 (disponível pra todas as empresas-cliente). Desligue pra cenários customizados de uma empresa específica.'
                            ),
                        Forms\Components\Toggle::make('demo_eligible')
                            ->label('Disponível no Demo')
                            ->helperText('Aparece nos 3 cenários do plano Demo')
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Ligue se este cenário pode ser oferecido a empresas com plano "Demo" (versão limitada com 3 cenários). Os planos pagos têm acesso a todos os cenários ativos.'
                            ),
                        CheckboxList::make('target_areas')
                            ->label('Áreas-alvo (para quais departamentos este cenário se aplica)')
                            ->options(Scenario::AREAS)
                            ->columns(3)
                            ->helperText('Marque "Todos" se aplicar a qualquer colaborador, ou selecione áreas específicas. Útil para escolher quais cenários enviar a cada colaborador.')
                            ->default(['todos'])
                            ->columnSpanFull()
                            ->hintIcon(
                                'heroicon-m-information-circle',
                                tooltip: 'Define quais perfis profissionais receberão esse cenário. Ex: "Financeiro" só vê golpes envolvendo pagamentos; "RH" só vê golpes envolvendo dados de funcionários. "Todos" expõe a qualquer perfil.'
                            ),
                    ])->columns(2),

                // ── Passo 3: Editor de Mensagens ────────────────────
                Forms\Components\Wizard\Step::make('Editor de Mensagens')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->description('Roteiro do cenário')
                    ->schema([
                        // Sub-section do cabeçalho de e-mail (só aparece se plataforma = e-mail)
                        Forms\Components\Section::make('Cabeçalho do e-mail')
                            ->description('Como o e-mail aparece pro colaborador (linha "De:", assunto).')
                            ->visible(fn (Get $get) => $get('platform') === 'email')
                            ->schema([
                                Forms\Components\TextInput::make('content.email_from_name')
                                    ->label('Nome do remetente (De:)')
                                    ->placeholder('Bradesco Empresas — E-mail')
                                    ->maxLength(120)
                                    ->hintIcon(
                                        'heroicon-m-information-circle',
                                        tooltip: 'Nome exibido como remetente do e-mail. Se deixar vazio, usa o "Título do cenário" como fallback.'
                                    ),
                                Forms\Components\TextInput::make('content.email_from_address')
                                    ->label('Endereço de e-mail')
                                    ->placeholder('bradesco.empresas.-.e-mail@bradescoempresasemail.com')
                                    ->maxLength(180)
                                    ->hintIcon(
                                        'heroicon-m-information-circle',
                                        tooltip: 'Endereço mostrado entre "< >" na linha do remetente. Deixe vazio pra o sistema gerar um endereço fake baseado no nome. Use pra simular domínios suspeitos (típico de phishing).'
                                    ),
                                Forms\Components\TextInput::make('content.email_subject')
                                    ->label('Assunto do e-mail')
                                    ->placeholder('[URGENTE] Token RSA desatualizado — Acesso será suspenso em 24h')
                                    ->maxLength(200)
                                    ->columnSpanFull()
                                    ->hintIcon(
                                        'heroicon-m-information-circle',
                                        tooltip: 'Título grande no topo do e-mail aberto. Se deixar vazio, usa a "Descrição prévia" da seção Identificação.'
                                    ),
                            ])->columns(2),

                        Forms\Components\Repeater::make('content.messages')
                            ->label('')
                            ->default([['type' => 'question']])
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipo de bloco')
                                    ->options([
                                        'text'     => '💬 Mensagem',
                                        'question' => '✍️ Resposta',
                                    ])
                                    ->required()
                                    ->live()
                                    ->columnSpanFull()
                                    ->hintIcon(
                                        'heroicon-m-information-circle',
                                        tooltip: '"Mensagem" = bolha do atacante (esquerda). "Resposta" = pausa o chat e oferece opções pro colaborador escolher.'
                                    ),

                                // Grava sempre 'them' — mensagens vêm da esquerda
                                Forms\Components\Hidden::make('from')
                                    ->default('them'),

                                // ── Bloco de MENSAGEM ──
                                Forms\Components\Group::make([
                                    Forms\Components\Textarea::make('body')
                                        ->label('Mensagem')
                                        ->rows(4)
                                        ->autosize()
                                        ->required()
                                        ->columnSpanFull()
                                        ->hintIcon(
                                            'heroicon-m-information-circle',
                                            tooltip: 'Texto exato que aparece na bolha do chat (vinda do atacante). Pode ter quebras de linha. Caracteres especiais e emojis são aceitos.'
                                        ),
                                ])
                                    ->visible(fn (Get $get) => $get('type') === 'text')
                                    ->extraAttributes(['class' => 'scenario-block-message'])
                                    ->columnSpanFull(),

                                // ── Bloco de RESPOSTA ──
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('prompt')
                                        ->label('Enunciado')
                                        ->required()
                                        ->columnSpanFull()
                                        ->hintIcon(
                                            'heroicon-m-information-circle',
                                            tooltip: 'Pergunta exibida pro colaborador depois das mensagens. Ex: "Como você responderia?".'
                                        ),

                                    Forms\Components\Repeater::make('options')
                                        ->label('Opções de resposta')
                                        ->default([
                                            ['key' => 'a', 'correct' => false],
                                            ['key' => 'b', 'correct' => false],
                                        ])
                                        ->schema([
                                            Forms\Components\Toggle::make('correct')
                                                ->label('Correta?')
                                                ->inline(false)
                                                ->hintIcon(
                                                    'heroicon-m-information-circle',
                                                    tooltip: 'Ligue se esta é a atitude SEGURA esperada. Pode ter mais de uma opção correta.'
                                                ),
                                            Forms\Components\Textarea::make('text')
                                                ->label('Texto da opção')
                                                ->rows(1)
                                                ->autosize()
                                                ->required()
                                                ->columnSpan(3)
                                                ->hintIcon(
                                                    'heroicon-m-information-circle',
                                                    tooltip: 'Texto exibido pro colaborador como opção de resposta. Cresce conforme você digita.'
                                                ),
                                            Forms\Components\Textarea::make('feedback')
                                                ->label('Feedback ao selecionar')
                                                ->rows(2)
                                                ->autosize()
                                                ->required()
                                                ->columnSpanFull()
                                                ->hintIcon(
                                                    'heroicon-m-information-circle',
                                                    tooltip: 'Mensagem educativa exibida APÓS o colaborador escolher esta opção. Explique por que está certa/errada.'
                                            ),
                                        ])
                                        ->columns(4)
                                        ->minItems(2)
                                        ->maxItems(4)
                                        ->addActionLabel('+ Adicionar opção')
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(function (array $state): ?string {
                                            $letter = strtoupper($state['key'] ?? '');
                                            $prefix = $letter !== '' ? $letter . ': ' : '';
                                            return $prefix . Str::limit($state['text'] ?? '(nova opção)', 60);
                                        })
                                        ->columnSpanFull(),
                                ])
                                    ->visible(fn (Get $get) => $get('type') === 'question')
                                    ->extraAttributes(['class' => 'scenario-block-response'])
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('+ Adicionar bloco')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => match ($state['type'] ?? null) {
                                'text'     => '💬 ' . Str::limit($state['body'] ?? '...', 60),
                                'question' => '✍️ ' . Str::limit($state['prompt'] ?? '...', 60),
                                default    => 'Novo bloco',
                            })
                            ->columnSpanFull(),
                    ]),

            ])
                ->skippable(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                ->columnSpanFull(),
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
                    ->color(fn ($state) => Scenario::PLATFORM_COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn ($state) => Scenario::PLATFORM_LABELS[$state] ?? $state),
                Tables\Columns\TextColumn::make('companies.name')
                    ->label('Empresas vinculadas')
                    ->badge()
                    ->separator(',')
                    ->color('gray')
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->placeholder('— Padrão M2 —'),
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
                    ->color(fn ($state) => Scenario::STATUS_COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn ($state) => Scenario::STATUS_LABELS[$state] ?? $state),
                Tables\Columns\TextColumn::make('version')
                    ->label('v.')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Plataforma')
                    ->options(Scenario::PLATFORM_LABELS),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Scenario::STATUS_LABELS),
                Tables\Filters\SelectFilter::make('companies')
                    ->label('Empresa vinculada')
                    ->relationship('companies', 'name')
                    ->searchable()
                    ->preload(),
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

    // Gera as chaves das opções (a, b, c, d) por índice — o admin não edita.
    public static function normalizeMessagesContent(array $content): array
    {
        $letters = ['a', 'b', 'c', 'd'];

        foreach ($content['messages'] ?? [] as $mi => $message) {
            if (($message['type'] ?? null) !== 'question') {
                continue;
            }
            foreach ($message['options'] ?? [] as $oi => $option) {
                $content['messages'][$mi]['options'][$oi]['key'] = $letters[$oi] ?? (string) $oi;
            }
        }

        return $content;
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
