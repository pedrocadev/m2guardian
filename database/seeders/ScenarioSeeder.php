<?php

namespace Database\Seeders;

use App\Models\Scenario;
use Illuminate\Database\Seeder;

class ScenarioSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->scenarios() as $scenario) {
            Scenario::updateOrCreate(
                ['company_id' => null, 'slug' => $scenario['slug']],
                $scenario
            );
        }
    }

    private function scenarios(): array
    {
        return [
            // ─── DEMO (3 cenários) ───────────────────────────────────────────
            [
                'slug'          => 'ceo-wapp',
                'platform'      => 'wapp',
                'label'         => 'Fraude do CEO',
                'avatar'        => '👨‍💼',
                'bg_color'      => '#1e3a8a',
                'preview'       => 'CEO solicita transferência urgente via WhatsApp',
                'intro'         => 'Você recebe uma mensagem no WhatsApp de um número desconhecido alegando ser o CEO da empresa.',
                'is_default'    => true,
                'demo_eligible' => true,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Oi, sou o Carlos, CEO. Preciso da sua ajuda em uma operação sigilosa.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Estou em reunião e não posso falar agora. Preciso que você faça uma transferência de R$ 87.450 para um fornecedor estratégico.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Vou te enviar os dados bancários. É urgente — precisa sair hoje. Não comente com ninguém por ora.'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Como você responderia a essa solicitação?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Confirmar pessoalmente com o CEO por outro canal (Teams/telefone interno) antes de qualquer ação', 'correct' => true,  'feedback' => 'Correto! Pedidos financeiros urgentes por canais não-oficiais são o padrão de fraude BEC. A urgência e o sigilo são alertas clássicos — sempre confirme por canal alternativo conhecido.'],
                                ['key' => 'b', 'text' => 'Pedir os dados bancários e executar a transferência para não atrasar', 'correct' => false, 'feedback' => 'Erro grave. Você acabou de cair em uma fraude BEC (Business Email Compromise). A urgência e o pedido de sigilo são sinais de alerta que devem travar qualquer ação imediata.'],
                                ['key' => 'c', 'text' => 'Responder pedindo o CPF do CEO para confirmar a identidade', 'correct' => false, 'feedback' => 'Insuficiente. Dados pessoais podem ser obtidos com engenharia social. Verifique sempre por canal oficial que você mesmo inicia — não o fornecido pelo suspeito.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou confirmar pelo canal interno antes de qualquer ação. Aguarde.'],
                    ],
                ],
            ],

            [
                'slug'          => 'it-teams',
                'platform'      => 'teams',
                'label'         => 'Falso Suporte de TI',
                'avatar'        => '💻',
                'bg_color'      => '#0078d4',
                'preview'       => 'Falso suporte de TI pede senha para "verificação urgente"',
                'intro'         => 'Você recebe uma mensagem no Microsoft Teams de alguém se identificando como suporte de TI.',
                'is_default'    => true,
                'demo_eligible' => true,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá! Sou o Rafael, da equipe de TI. Detectamos atividade suspeita na sua conta.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Para evitar o bloqueio automático nas próximas 2 horas, preciso confirmar sua senha atual para validar o acesso.'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Qual é a melhor resposta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Compartilhar a senha para evitar o bloqueio', 'correct' => false, 'feedback' => 'Erro crítico. Nenhum suporte de TI legítimo jamais solicita senha. Esse é um ataque de phishing interno — possivelmente uma conta comprometida sendo usada para movimentação lateral.'],
                                ['key' => 'b', 'text' => 'Recusar e reportar ao canal oficial de TI/Segurança', 'correct' => true,  'feedback' => 'Correto! TI nunca pede senha, ponto final. Reporte imediatamente pelo canal oficial — o próprio solicitante pode ser uma vítima com conta comprometida.'],
                                ['key' => 'c', 'text' => 'Trocar a senha primeiro e então enviar a nova para ele', 'correct' => false, 'feedback' => 'Ainda errado. O problema não é qual senha, é o pedido em si. Senhas jamais devem ser compartilhadas por nenhum canal, independentemente de quem pede.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou reportar isso ao canal oficial de segurança. Por favor, abra um chamado formal.'],
                    ],
                ],
            ],

            [
                'slug'          => 'invoice-fraud',
                'platform'      => 'email',
                'label'         => 'Fatura Fraudulenta',
                'avatar'        => '📧',
                'bg_color'      => '#dc2626',
                'preview'       => 'Email com fatura falsa de fornecedor conhecido',
                'intro'         => 'Você recebe um email de um fornecedor regular com uma fatura e novos dados bancários.',
                'is_default'    => true,
                'demo_eligible' => true,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Prezado(a), segue em anexo a fatura referente ao mês corrente.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Atenção: alteramos nossos dados bancários. Por favor, atualize seu cadastro e efetue o pagamento na nova conta indicada no boleto.'],
                        ['type' => 'text', 'from' => 'them', 'body' => '📎 fatura_novembro_2024.pdf'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Como você deve proceder antes de qualquer pagamento?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Pagar nos novos dados para não atrasar e evitar juros', 'correct' => false, 'feedback' => 'Erro grave. Mudança de dados bancários enviada por email é o vetor #1 de fraude a fornecedores. Nunca pague sem confirmar por canal independente.'],
                                ['key' => 'b', 'text' => 'Ligar para o fornecedor usando o número já cadastrado no sistema (não o do email) para confirmar', 'correct' => true,  'feedback' => 'Correto! Validação out-of-band (canal independente do email recebido) é o padrão para confirmar mudanças bancárias. Nunca use contatos do próprio email suspeito.'],
                                ['key' => 'c', 'text' => 'Responder o email pedindo uma confirmação por escrito dos novos dados', 'correct' => false, 'feedback' => 'Insuficiente. Se o email foi comprometido, o atacante vai confirmar por escrito sem problema. Use sempre um canal que você mesmo conhece e controlou anteriormente.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou ligar no número cadastrado para confirmar antes de qualquer pagamento.'],
                    ],
                ],
            ],

            // ─── PRO (10 cenários adicionais) ────────────────────────────────
            [
                'slug'          => 'phishing-link-email',
                'platform'      => 'email',
                'label'         => 'Link de Phishing',
                'avatar'        => '🎣',
                'bg_color'      => '#7c3aed',
                'preview'       => 'Email corporativo com link urgente para "confirmar acesso"',
                'intro'         => 'Você recebe um email aparentemente da Microsoft pedindo que confirme seu acesso.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'De: Microsoft Support <no-reply@microsofit-security.com>'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Assunto: [AÇÃO NECESSÁRIA] Sua conta Microsoft 365 será desativada em 24h'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Detectamos acesso não autorizado à sua conta. Clique no link abaixo para confirmar sua identidade e evitar o bloqueio permanente: [Confirmar Conta Agora]'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'O que você deve fazer?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Clicar no link e inserir as credenciais para evitar o bloqueio', 'correct' => false, 'feedback' => 'Você acabou de entregar suas credenciais ao atacante. O domínio "microsofit-security.com" é falso — note o erro ortográfico. Urgência + ameaça = padrão clássico de phishing.'],
                                ['key' => 'b', 'text' => 'Verificar o remetente, identificar o domínio falso e reportar como phishing sem clicar', 'correct' => true,  'feedback' => 'Correto! "microsofit-security.com" não é um domínio legítimo da Microsoft. Domínio falso + urgência + ameaça de perda = phishing. Reporte sem clicar.'],
                                ['key' => 'c', 'text' => 'Encaminhar o email para um colega verificar se é legítimo', 'correct' => false, 'feedback' => 'Risco desnecessário. Encaminhar propaga o link perigoso. Analise você mesmo os sinais (domínio do remetente, urgência, ameaça) e reporte ao canal de segurança.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Domínio suspeito — vou reportar como phishing sem clicar.'],
                    ],
                ],
            ],

            [
                'slug'          => 'qrcode-wapp',
                'platform'      => 'wapp',
                'label'         => 'QR Code Malicioso',
                'avatar'        => '📱',
                'bg_color'      => '#059669',
                'preview'       => 'QR code enviado por WhatsApp promete bônus de R$ 500',
                'intro'         => 'Um colega (número salvo) te envia um QR code via WhatsApp com uma promoção da empresa.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Ei! Vi que estão dando R$ 500 de bônus pra quem cadastrar no novo portal de benefícios da empresa.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Me mandaram esse QR code. Você já fez? Escaneie antes de acabar, tem prazo hoje.'],
                        ['type' => 'text', 'from' => 'them', 'body' => '[QR Code]'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Qual a atitude correta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Escanear o QR code imediatamente para não perder o prazo', 'correct' => false, 'feedback' => 'Erro. O WhatsApp do colega pode ter sido comprometido. QR codes podem direcionar para sites de phishing ou executar ações no dispositivo. Nunca escaneie sem verificar a origem oficial.'],
                                ['key' => 'b', 'text' => 'Verificar com RH/TI se esse benefício existe antes de escanear qualquer código', 'correct' => true,  'feedback' => 'Correto! Contas do WhatsApp podem ser clonadas ou comprometidas. Verifique benefícios sempre com RH ou pela intranet oficial — nunca por QR codes recebidos em mensagens, mesmo de colegas.'],
                                ['key' => 'c', 'text' => 'Ligar para o colega para confirmar se ele mesmo enviou', 'correct' => false, 'feedback' => 'Melhor que escanear, mas incompleto. Mesmo que o colega confirme, o QR code pode ter sido enviado sem ele saber (conta comprometida). Confirme com a fonte oficial (RH/intranet).'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou verificar com o RH se esse benefício existe antes de qualquer ação.'],
                    ],
                ],
            ],

            [
                'slug'          => 'cfo-teams',
                'platform'      => 'teams',
                'label'         => 'Urgência Financeira',
                'avatar'        => '💰',
                'bg_color'      => '#b45309',
                'preview'       => 'CFO pede no Teams para antecipar pagamento fora do processo normal',
                'intro'         => 'Você trabalha no financeiro e recebe uma mensagem no Teams do perfil do CFO da empresa.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá. Preciso que você processe um pagamento urgente fora do fluxo normal de aprovação.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Estamos fechando uma parceria estratégica e o contrato exige depósito de R$ 210.000 ainda hoje. O jurídico já autorizou verbalmente.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Não acione o processo de dupla aprovação agora — o outro aprovador está viajando sem acesso. Vou te enviar os dados.'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Como você procede?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Processar o pagamento — o CFO tem autoridade e a situação parece legítima', 'correct' => false, 'feedback' => 'Erro crítico. Pedidos para "pular" controles internos são red flags independentemente de quem solicita. Contas de executivos são alvo prioritário de comprometimento.'],
                                ['key' => 'b', 'text' => 'Seguir o processo padrão, contatar o CFO por telefone diretamente, e não processar sem dupla aprovação', 'correct' => true,  'feedback' => 'Correto! Controles internos (dupla aprovação) existem exatamente para situações assim. Nenhuma autoridade legítima pede para burlar controles de segurança financeira.'],
                                ['key' => 'c', 'text' => 'Pedir autorização por escrito no próprio Teams antes de processar', 'correct' => false, 'feedback' => 'Insuficiente. Se o Teams foi comprometido, qualquer autorização escrita por ali é do atacante. Confirme por telefone direto ao número pessoal conhecido do CFO.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou seguir o processo padrão e contatar o CFO diretamente por telefone.'],
                    ],
                ],
            ],

            [
                'slug'          => 'credential-reset-email',
                'platform'      => 'email',
                'label'         => 'Coleta de Credenciais',
                'avatar'        => '🔑',
                'bg_color'      => '#dc2626',
                'preview'       => 'Email falso de "redefinição de senha" do sistema interno',
                'intro'         => 'Você recebe um email pedindo que redefina sua senha do sistema ERP da empresa.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'De: TI-Corporativo <ti@empresa-sistemas.net>'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Assunto: Sua senha do ERP expirará em 2 horas'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá, sua senha do sistema ERP expirará em breve. Para manter o acesso, clique em "Redefinir Senha" e informe: senha atual + nova senha. [Redefinir Agora]'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'O que fazer com esse email?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Clicar e informar as credenciais para não perder acesso ao ERP', 'correct' => false, 'feedback' => 'Erro. Sistemas legítimos nunca pedem a senha ATUAL num processo de redefinição — eles geram um token único. Pedir senha atual + nova é coleta de credenciais.'],
                                ['key' => 'b', 'text' => 'Ignorar o email e acessar o ERP diretamente pela intranet para verificar se há aviso de expiração', 'correct' => true,  'feedback' => 'Correto! Acesse sistemas sempre pela URL que você já conhece, nunca por links de email. Sistemas legítimos de redefinição não pedem a senha atual.'],
                                ['key' => 'c', 'text' => 'Responder ao email pedindo mais informações antes de clicar', 'correct' => false, 'feedback' => 'Risco desnecessário. Interagir com o email pode confirmar que seu endereço é ativo. Acesse o sistema diretamente e reporte o email suspeito ao TI.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou acessar o ERP diretamente pela intranet e reportar esse email ao TI.'],
                    ],
                ],
            ],

            [
                'slug'          => 'ransomware-attachment',
                'platform'      => 'email',
                'label'         => 'Anexo Malicioso',
                'avatar'        => '☠️',
                'bg_color'      => '#111827',
                'preview'       => 'Email com proposta comercial em Word habilitando macros',
                'intro'         => 'Você recebe um email com uma proposta comercial de um parceiro, pedindo para habilitar conteúdo no Word.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá! Conforme conversamos, segue nossa proposta comercial atualizada em anexo.'],
                        ['type' => 'text', 'from' => 'them', 'body' => '📎 Proposta_Comercial_2024_v3.docm'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Obs: o documento utiliza formatação especial — ao abrir, clique em "Habilitar Conteúdo" para visualizar corretamente. Aguardo retorno!'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Como você deve agir com esse documento?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Abrir e habilitar o conteúdo como pedido para ver a proposta', 'correct' => false, 'feedback' => 'Erro grave. Habilitar macros em documentos Office de fontes externas é o vetor de infecção mais comum de ransomware. A extensão .docm (macro-enabled) é um sinal de alerta imediato.'],
                                ['key' => 'b', 'text' => 'Não habilitar macros, escanear o arquivo com antivírus e confirmar com o parceiro por telefone se enviou esse documento', 'correct' => true,  'feedback' => 'Correto! Documentos com macros de fontes externas devem ser tratados com suspeita máxima. Escaneie, confirme por telefone, e nunca habilite macros sem orientação do TI.'],
                                ['key' => 'c', 'text' => 'Abrir o documento mas não habilitar as macros', 'correct' => false, 'feedback' => 'Melhor que habilitar, mas ainda arriscado. Algumas vulnerabilidades podem ser exploradas apenas ao abrir o arquivo. Escaneie antes e confirme a autenticidade com o remetente por outro canal.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou escanear e confirmar com o parceiro por telefone antes de abrir qualquer coisa.'],
                    ],
                ],
            ],

            [
                'slug'          => 'vishing-wapp',
                'platform'      => 'wapp',
                'label'         => 'Preparação para Vishing',
                'avatar'        => '📞',
                'bg_color'      => '#0f172a',
                'preview'       => 'WhatsApp preparando terreno para ligação de "banco" ou "TI"',
                'intro'         => 'Você recebe uma mensagem no WhatsApp de um número desconhecido se apresentando como do banco da empresa.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Boa tarde! Sou a Ana, do setor antifraude do Banco Itaú. Identificamos uma tentativa de acesso suspeito à conta corporativa da sua empresa.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Para bloquear preventivamente, precisamos que você nos informe o código que chegará por SMS no número cadastrado. Aguardarei sua ligação no 0800-XXX-XXXX.'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Como você responde?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Ligar no número fornecido e informar o código SMS', 'correct' => false, 'feedback' => 'Erro crítico. O número fornecido pelo suposto banco é do atacante. O código SMS é o segundo fator de autenticação — informá-lo entrega acesso total à conta bancária.'],
                                ['key' => 'b', 'text' => 'Ignorar a mensagem e ligar diretamente para o banco usando o número no verso do cartão ou site oficial', 'correct' => true,  'feedback' => 'Correto! Bancos não pedem códigos SMS por WhatsApp. Sempre contate o banco pelo número que você obteve de fonte confiável — nunca o fornecido por quem pediu contato.'],
                                ['key' => 'c', 'text' => 'Responder à mensagem pedindo mais dados para confirmar a identidade da atendente', 'correct' => false, 'feedback' => 'Não resolva. O atacante tem todas as respostas preparadas para parecer legítimo. Desligue, não informe nada, e ligue ao banco pelo canal oficial.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Não vou ligar nesse número. Vou contatar o banco pelo canal oficial que já tenho.'],
                    ],
                ],
            ],

            [
                'slug'          => 'pretexting-teams',
                'platform'      => 'teams',
                'label'         => 'Engenharia Social',
                'avatar'        => '🎭',
                'bg_color'      => '#4338ca',
                'preview'       => 'Novo "funcionário" pede acesso ao sistema via Teams',
                'intro'         => 'Você recebe uma mensagem no Teams de alguém se apresentando como novo funcionário do departamento.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Oi! Sou o Lucas, comecei hoje no time de Operações. O TI ainda não liberou meu acesso ao sistema de estoque.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Meu gestor está em viagem e o chamado de TI pode demorar dias. Você poderia me emprestar seu login temporariamente para eu não atrasar as entregas de hoje?'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Qual a resposta correta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Emprestar o login temporariamente para não prejudicar as operações', 'correct' => false, 'feedback' => 'Erro grave. Compartilhar credenciais viola política de segurança e transfere responsabilidade legal das ações para você. Essa é uma técnica clássica de pretexting.'],
                                ['key' => 'b', 'text' => 'Recusar, orientar o colega a abrir chamado urgente no TI e verificar com o gestor por outro canal', 'correct' => true,  'feedback' => 'Correto! Credenciais são pessoais e intransferíveis. O processo de acesso para novos funcionários existe por motivo — sugira o caminho correto e ofereça ajuda para escalar o chamado.'],
                                ['key' => 'c', 'text' => 'Pedir o crachá ou matrícula do novo funcionário para confirmar antes de ceder o acesso', 'correct' => false, 'feedback' => 'Insuficiente. Mesmo verificando identidade, compartilhar credenciais é proibido por política de segurança e cria risco legal para você. A solução é sempre pelo processo oficial de TI.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Não posso compartilhar meu acesso. Vou te ajudar a escalar o chamado de TI com urgência.'],
                    ],
                ],
            ],

            [
                'slug'          => 'data-exfil-email',
                'platform'      => 'email',
                'label'         => 'Exfiltração de Dados',
                'avatar'        => '📤',
                'bg_color'      => '#7e22ce',
                'preview'       => 'Email pedindo lista de clientes para "análise de mercado"',
                'intro'         => 'Você recebe um email de alguém que se identifica como consultor externo contratado pela diretoria.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá, sou o Roberto Alves, consultor externo contratado pela diretoria para mapeamento estratégico.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Preciso urgentemente da base de clientes ativos (nome, email, CNPJ, histórico de compras) para análise. Por favor, exporte do CRM e envie para este email até sexta.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'O projeto é confidencial — não precisa comunicar o seu gestor, a autorização já veio da diretoria.'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'Como você deve agir?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Enviar os dados como pedido — veio da diretoria e parece legítimo', 'correct' => false, 'feedback' => 'Erro gravíssimo. Isso configura vazamento de dados pessoais sob a LGPD, com multa de até 2% do faturamento. "Não avise seu gestor" é um alerta vermelho claro.'],
                                ['key' => 'b', 'text' => 'Confirmar a contratação com seu gestor e com a diretoria por canal interno antes de qualquer exportação', 'correct' => true,  'feedback' => 'Correto! Dados de clientes são ativos sensíveis sob a LGPD. Pedidos que incluem "não avise seu gestor" são red flags. Toda exportação de base de dados exige autorização formal por canal interno.'],
                                ['key' => 'c', 'text' => 'Pedir o contrato assinado por email antes de enviar os dados', 'correct' => false, 'feedback' => 'Insuficiente. Um atacante pode forjar documentos. A validação precisa ser com pessoas internas que você conhece — gestor, jurídico, DPO — não com documentos enviados pelo próprio solicitante.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou confirmar com meu gestor e a diretoria antes de qualquer exportação de dados.'],
                    ],
                ],
            ],

            [
                'slug'          => 'wi-fi-trap-wapp',
                'platform'      => 'wapp',
                'label'         => 'Armadilha de Wi-Fi',
                'avatar'        => '📶',
                'bg_color'      => '#0e7490',
                'preview'       => 'Grupo da empresa divulga rede Wi-Fi "nova" no escritório',
                'intro'         => 'No grupo do WhatsApp da empresa, alguém posta sobre uma nova rede Wi-Fi corporativa.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Pessoal, o TI instalou uma nova rede Wi-Fi mais rápida no escritório! 🎉'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Nome da rede: Corp-Guest-Premium\nSenha: empresa2024\n\nJá estou conectado e tá bem mais rápido que o anterior!'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'O que você faz com essa informação?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Conectar imediatamente — veio no grupo da empresa', 'correct' => false, 'feedback' => 'Erro. Grupos de WhatsApp podem ser infiltrados ou mensagens podem ser de contas comprometidas. Uma rede Wi-Fi falsa (Evil Twin) pode capturar todo seu tráfego sem criptografia.'],
                                ['key' => 'b', 'text' => 'Confirmar com o TI pelo canal oficial (chamado/email corporativo) se essa rede foi realmente criada por eles', 'correct' => true,  'feedback' => 'Correto! Mudanças de infraestrutura de TI têm comunicado oficial. Redes Wi-Fi falsas são vetores de ataque Man-in-the-Middle que capturam credenciais e dados. Confirme sempre com TI.'],
                                ['key' => 'c', 'text' => 'Conectar só para testar a velocidade, sem usar sistemas críticos', 'correct' => false, 'feedback' => 'Ainda arriscado. Em uma rede maliciosa, dados como credenciais salvas no navegador, emails em background e sessões ativas podem ser capturados sem você "usar" nada ativamente.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou confirmar com o TI pelo canal oficial antes de conectar.'],
                    ],
                ],
            ],

            [
                'slug'          => 'deepfake-audio-wapp',
                'platform'      => 'wapp',
                'label'         => 'Áudio Falso (Deepfake)',
                'avatar'        => '🎙️',
                'bg_color'      => '#be123c',
                'preview'       => 'Mensagem de voz imitando o diretor pedindo ação urgente',
                'intro'         => 'Você recebe um áudio no WhatsApp que parece ser do diretor da empresa pedindo uma ação urgente.',
                'is_default'    => true,
                'demo_eligible' => false,
                'version'       => 1,
                'status'        => 'active',
                'content'       => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => '[🎙️ Mensagem de voz — 0:23]'],
                        ['type' => 'text', 'from' => 'them', 'body' => '"...preciso que você acesse o sistema de RH agora e exporte a folha de pagamento completa do mês. Manda no meu email pessoal — diretor.silva.rh@gmail.com — é urgente para uma reunião com investidores em 30 minutos."'],
                        [
                            'type'    => 'question',
                            'prompt'  => 'A voz soa exatamente como a do diretor. Como você age?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Atender ao pedido — a voz é claramente do diretor e a situação parece real', 'correct' => false, 'feedback' => 'Erro. Tecnologia de deepfake de áudio pode replicar vozes com apenas alguns minutos de amostra. Email pessoal (@gmail) para dados corporativos sensíveis é red flag independente da voz.'],
                                ['key' => 'b', 'text' => 'Ligar diretamente para o número pessoal/fixo do diretor para confirmar o pedido antes de qualquer ação', 'correct' => true,  'feedback' => 'Correto! Deepfakes de voz são tecnicamente acessíveis hoje. O uso de email pessoal para dados corporativos é irregular em qualquer cenário legítimo. Confirmação por ligação direta é o controle correto.'],
                                ['key' => 'c', 'text' => 'Enviar os dados mas com senha de proteção no arquivo', 'correct' => false, 'feedback' => 'Totalmente errado. A questão não é proteger o arquivo — é não enviar dados sensíveis para email pessoal sem autorização formal verificada. Proteção com senha não resolve o problema de origem.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou ligar diretamente para o diretor para confirmar antes de qualquer exportação.'],
                    ],
                ],
            ],
        ];
    }
}
