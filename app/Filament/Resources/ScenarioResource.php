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

            Forms\Components\Section::make('Identificação')->schema([
                Forms\Components\Select::make('platform')
                    ->label('Plataforma')
                    ->options(['wapp' => 'WhatsApp', 'teams' => 'Microsoft Teams', 'email' => 'E-mail', 'outro' => 'Outra Plataforma'])
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

            Forms\Components\Section::make('Configuração')->schema([
                Forms\Components\Select::make('company_id')
                    ->label('Empresa (vazio = padrão M2)')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->hintIcon(
                        'heroicon-m-information-circle',
                        tooltip: 'Se selecionar uma empresa, o cenário fica disponível APENAS para colaboradores dela. Se deixar vazio, vira "cenário padrão M2" disponível pra todas as empresas que tiverem licença para esse tipo de cenário.'
                    ),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Ativo', 'draft' => 'Rascunho', 'archived' => 'Arquivado'])
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

            Forms\Components\Section::make('Cabeçalho do e-mail')
                ->description('Como o e-mail aparece pro colaborador (linha "De:", assunto). Só aparece quando a plataforma é E-mail.')
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

            Forms\Components\Section::make('Editor de Mensagens')
                ->description('Monte o roteiro do cenário. Alterne entre mensagens de texto e perguntas.')
                ->schema([
                    Forms\Components\Repeater::make('content.messages')
                        ->label('')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Tipo de bloco')
                                ->options([
                                    'text'     => '💬 Mensagem de texto',
                                    'question' => '❓ Pergunta',
                                ])
                                ->required()
                                ->live()
                                ->columnSpanFull()
                                ->hintIcon(
                                    'heroicon-m-information-circle',
                                    tooltip: '"Mensagem de texto" = bolha simulando uma mensagem real (do atacante ou do colaborador). "Pergunta" = pausa o chat e oferece opções pro colaborador escolher como reagir.'
                                ),

                            // ── Bloco de texto ──────────────────────────
                            Forms\Components\Select::make('from')
                                ->label('Quem envia')
                                ->options(['them' => '← Deles (esquerda)', 'me' => '→ Eu (direita)'])
                                ->default('them')
                                ->required()
                                ->visible(fn (Get $get) => $get('type') === 'text')
                                ->hintIcon(
                                    'heroicon-m-information-circle',
                                    tooltip: '"Deles" = mensagem do atacante (bolha à esquerda, fundo claro). "Eu" = mensagem do colaborador (bolha à direita, fundo vermelho). Use "Eu" pra simular respostas que o colaborador "já mandou" antes da pergunta.'
                                ),

                            Forms\Components\Textarea::make('body')
                                ->label('Texto da mensagem')
                                ->rows(12)
                                ->autosize()
                                ->required()
                                ->visible(fn (Get $get) => $get('type') === 'text')
                                ->columnSpanFull()
                                ->hintIcon(
                                    'heroicon-m-information-circle',
                                    tooltip: 'Texto exato que aparece na bolha do chat. Pode ter quebras de linha. Caracteres especiais e emojis são aceitos. Mantenha realista — escreva como um atacante real escreveria (com urgência, pressão, etc.).'
                                ),

                            // ── Bloco de pergunta ────────────────────────
                            Forms\Components\TextInput::make('prompt')
                                ->label('Enunciado da pergunta')
                                ->required()
                                ->visible(fn (Get $get) => $get('type') === 'question')
                                ->columnSpanFull()
                                ->hintIcon(
                                    'heroicon-m-information-circle',
                                    tooltip: 'Pergunta exibida pro colaborador depois das mensagens. Geralmente "Como você responderia?" ou "O que você faz agora?". Aparece num card destacado abaixo do chat.'
                                ),

                            Forms\Components\Repeater::make('options')
                                ->label('Opções de resposta')
                                ->schema([
                                    Forms\Components\TextInput::make('key')
                                        ->label('Chave (a, b, c...)')
                                        ->maxLength(4)
                                        ->required()
                                        ->placeholder('a')
                                        ->hintIcon(
                                            'heroicon-m-information-circle',
                                            tooltip: 'Letra identificadora interna (a, b, c, d). Não aparece pro colaborador, é usada pra registrar qual opção ele escolheu no banco de dados (relatórios e estatísticas).'
                                        ),
                                    Forms\Components\Toggle::make('correct')
                                        ->label('Resposta correta?')
                                        ->inline(false)
                                        ->hintIcon(
                                            'heroicon-m-information-circle',
                                            tooltip: 'Ligue se esta é a atitude SEGURA esperada. Pode ter mais de uma opção correta. Acertos vão somar pontos no resultado final do colaborador.'
                                        ),
                                    Forms\Components\TextInput::make('text')
                                        ->label('Texto da opção')
                                        ->required()
                                        ->columnSpan(2)
                                        ->hintIcon(
                                            'heroicon-m-information-circle',
                                            tooltip: 'Texto exibido pro colaborador como opção de resposta (ex: "Pedir confirmação pelo Teams antes de transferir", "Transferir imediatamente como pedido"). Escreva de forma natural.'
                                        ),
                                    Forms\Components\Textarea::make('feedback')
                                        ->label('Feedback ao selecionar')
                                        ->rows(2)
                                        ->required()
                                        ->columnSpanFull()
                                        ->hintIcon(
                                            'heroicon-m-information-circle',
                                            tooltip: 'Mensagem educativa exibida APÓS o colaborador escolher esta opção. Explique por que está certa/errada e o que ele deveria observar. É a parte mais importante do treinamento — é onde ele realmente aprende.'
                                        ),
                                ])
                                ->columns(4)
                                ->minItems(2)
                                ->maxItems(4)
                                ->addActionLabel('+ Adicionar opção')
                                ->visible(fn (Get $get) => $get('type') === 'question')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->addActionLabel('+ Adicionar bloco')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => match ($state['type'] ?? null) {
                            'text'     => '💬 ' . Str::limit($state['body'] ?? '...', 60),
                            'question' => '❓ ' . Str::limit($state['prompt'] ?? '...', 60),
                            default    => 'Novo bloco',
                        })
                        ->columnSpanFull(),
                ]),
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
                    ->color(fn ($state) => match ($state) {
                        'wapp'  => 'success',
                        'teams' => 'primary',
                        'email' => 'warning',
                        'outro' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'wapp'  => 'WhatsApp',
                        'teams' => 'Teams',
                        'email' => 'E-mail',
                        'outro' => 'Outra',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->default('— Padrão M2 —')
                    ->sortable(),
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
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'draft'    => 'warning',
                        'archived' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'   => 'Ativo',
                        'draft'    => 'Rascunho',
                        'archived' => 'Arquivado',
                        default    => $state,
                    }),
                Tables\Columns\TextColumn::make('version')
                    ->label('v.')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Plataforma')
                    ->options(['wapp' => 'WhatsApp', 'teams' => 'Teams', 'email' => 'E-mail']),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(['active' => 'Ativo', 'draft' => 'Rascunho', 'archived' => 'Arquivado']),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListScenarios::route('/'),
            'create' => Pages\CreateScenario::route('/create'),
            'edit'   => Pages\EditScenario::route('/{record}/edit'),
        ];
    }
}
