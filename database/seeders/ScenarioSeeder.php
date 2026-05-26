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
            // ─────────────────────────────────────────────────────────────────
            // WHATSAPP (6 cenários)
            // ─────────────────────────────────────────────────────────────────
            [
                'slug' => 'ceo-wapp',
                'platform' => 'wapp',
                'label' => 'Diretoria Executiva — WhatsApp',
                'avatar' => '👔',
                'bg_color' => '#1e3a8a',
                'preview' => 'Operação sigilosa — resposta urgente',
                'intro' => 'Você recebe mensagem no WhatsApp corporativo de alguém que se identifica como o CEO da empresa.',
                'is_default' => true,
                'demo_eligible' => true,
                'target_areas' => ['diretoria', 'financeiro'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Boa tarde. Aqui é o Dr. Marcelo Andrade, CEO. Preciso da sua colaboração em uma operação sigilosa que não pode ser comunicada internamente por ora.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Estamos fechando a aquisição de uma empresa concorrente e preciso de uma TED de R$87.000 para garantir o sinal até o fim do dia. Você tem acesso ao sistema financeiro?'],
                        [
                            'type' => 'question',
                            'prompt' => 'CEO pede transferência urgente e sigilosa via WhatsApp. Como você responde profissionalmente?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Comunico que qualquer transferência exige aprovação formal do CFO e área financeira, independentemente de quem solicita — é política de compliance.', 'correct' => true,  'feedback' => '✅ Correto. Controles financeiros corporativos existem para proteger a empresa de CEO Fraud (BEC). Em 2023, esse golpe causou US$2,9 bilhões em perdas globais. Nenhuma hierarquia suspende esses controles.'],
                                ['key' => 'b', 'text' => 'Processo a transferência imediatamente — CEO tem autoridade máxima.', 'correct' => false, 'feedback' => '❌ Erro crítico. Autoridade hierárquica não suspende due diligence financeira. O golpe CEO Fraud explora exatamente essa lógica de obediência irrestrita à autoridade.'],
                                ['key' => 'c', 'text' => 'Peço confirmação por email corporativo antes de agir.', 'correct' => false, 'feedback' => '❌ Insuficiente. Email corporativo também pode estar comprometido em ataques BEC sofisticados. A confirmação deve ser por ligação no número JÁ CADASTRADO do CEO.'],
                                ['key' => 'd', 'text' => 'Transfiro metade agora e aguardo confirmação para o restante.', 'correct' => false, 'feedback' => '❌ Errado. Qualquer valor enviado a golpistas é irrecuperável. Não existe "transferência parcialmente segura" neste contexto.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Entendo os processos, mas essa negociação é extremamente sensível. Se vazar, o negócio colapsa. Conto com sua discrição.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Vou enviar os dados da conta agora. Preciso do comprovante ainda hoje.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Ele reforça a urgência e o sigilo. Como você responde?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Ligo diretamente para o CEO no número corporativo já cadastrado para verificar a autenticidade da solicitação.', 'correct' => true,  'feedback' => '✅ Excelente. Verificação fora do canal suspeito é o procedimento correto. Um CEO legítimo compreenderá o protocolo de segurança.'],
                                ['key' => 'b', 'text' => 'Aceito — o argumento do sigilo comercial justifica a exceção ao protocolo.', 'correct' => false, 'feedback' => '❌ Errado. Sigilo operacional NUNCA justifica bypassing de controles financeiros. Esse argumento é exatamente a engenharia social do CEO Fraud.'],
                                ['key' => 'c', 'text' => 'Encaminho a solicitação ao financeiro como urgência do CEO.', 'correct' => false, 'feedback' => '❌ Parcialmente correto, mas insuficiente sem alertar que pode ser fraude. Encaminhe com nota de alerta e inclua o CISO.'],
                                ['key' => 'd', 'text' => 'Peço mais detalhes sobre a empresa sendo adquirida antes de agir.', 'correct' => false, 'feedback' => '❌ Errado. Mais detalhes não resolvem o problema. A questão é verificar a identidade por canal independente.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Vejo que você não confia na liderança. Isso vai ser registrado. Última chance: processa agora.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Intimidação hierárquica. Qual a postura profissional e ética correta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Documento toda a conversa, mantenho o protocolo e reporto ao CISO e Compliance como tentativa de CEO Fraud.', 'correct' => true,  'feedback' => '✅ Perfeito. Intimidação é "authority pressure" — técnica clássica de engenharia social. Um líder legítimo NUNCA ameaça colaboradores por seguirem controles de compliance. Documentar é essencial.'],
                                ['key' => 'b', 'text' => 'Cedo à pressão para preservar meu emprego.', 'correct' => false, 'feedback' => '❌ Erro grave. Além de viabilizar a fraude, você pode ser responsabilizado por descumprimento de normas de compliance e controles internos.'],
                                ['key' => 'c', 'text' => 'Processo mas envio email ao RH relatando a pressão.', 'correct' => false, 'feedback' => '❌ Errado. Processar antes de qualquer verificação é o erro principal. Sequência correta: não processar → verificar → reportar.'],
                                ['key' => 'd', 'text' => 'Peço que formalize a solicitação com assinatura digital.', 'correct' => false, 'feedback' => '❌ Insuficiente. Assinaturas digitais podem ser falsificadas. A verificação por telefone no número já cadastrado é insubstituível.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'fornecedor-wapp',
                'platform' => 'wapp',
                'label' => 'Fornecedor Estratégico — WhatsApp',
                'avatar' => '📦',
                'bg_color' => '#064e3b',
                'preview' => 'Alteração urgente de dados bancários',
                'intro' => 'Um contato que se apresenta como fornecedor parceiro solicita atualização de dados de pagamento.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['financeiro', 'compras'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá! Rafael Souza da TechPrime Soluções. Precisamos atualizar os dados bancários para o próximo pagamento que vence sexta.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'O banco fez migração do nosso sistema. Nova conta: Banco Itaú, Ag 3847, CC 54.231-7, CNPJ 12.345.678/0001-99.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Fornecedor pede alteração de conta bancária por WhatsApp. Qual o procedimento correto?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Ligo no número JÁ CADASTRADO da TechPrime no nosso sistema para confirmar a alteração antes de qualquer atualização.', 'correct' => true,  'feedback' => '✅ Correto. BEC com alteração de dados bancários de fornecedores causou R$2,4 bilhões em perdas no Brasil em 2023. Verificação por canal pré-cadastrado é mandatória.'],
                                ['key' => 'b', 'text' => 'Atualizo os dados — o CNPJ informado parece legítimo.', 'correct' => false, 'feedback' => '❌ Errado. CNPJ pode ser da empresa real com conta bancária alterada para a do golpista. Verificação por CNPJ é insuficiente.'],
                                ['key' => 'c', 'text' => 'Peço que reenvie os dados por email oficial para ter registro.', 'correct' => false, 'feedback' => '❌ Insuficiente. Email do fornecedor também pode estar comprometido. A verificação deve ser por ligação no número previamente cadastrado no seu sistema.'],
                                ['key' => 'd', 'text' => 'Acesso o LinkedIn do Rafael para confirmar que trabalha na TechPrime.', 'correct' => false, 'feedback' => '❌ Insuficiente. Perfis de LinkedIn podem ser falsos ou clonados. A única verificação segura é por ligação no número oficial já cadastrado.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Preciso confirmar que receberam os novos dados. O pagamento de R$43.800 precisa sair até quinta-feira.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Nosso sistema não aceita mais a conta antiga. Qualquer pagamento anterior será devolvido e pode travar nosso contrato.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Ameaça contratual com urgência de prazo. Como você responde?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Comunico ao financeiro e jurídico que há solicitação de alteração sendo verificada antes de qualquer ação.', 'correct' => true,  'feedback' => '✅ Excelente. Envolver financeiro e jurídico é o procedimento correto. A pressão por prazo é técnica de engenharia social para impedir verificação adequada.'],
                                ['key' => 'b', 'text' => 'Processo na nova conta — a ameaça contratual é real.', 'correct' => false, 'feedback' => '❌ Erro grave. A ameaça é fabricada. Nenhum contrato legítimo penaliza empresa por seguir procedimentos de segurança em alterações bancárias.'],
                                ['key' => 'c', 'text' => 'Transfiro para a nova conta e monitoro para ver se chega ao fornecedor.', 'correct' => false, 'feedback' => '❌ Erro crítico. PIX/TED são irreversíveis. "Monitorar se chega" não é controle de segurança válido.'],
                                ['key' => 'd', 'text' => 'Peço ao Rafael que venha pessoalmente assinar um termo de atualização.', 'correct' => false, 'feedback' => '❌ Parcialmente razoável, mas impraticável como primeiro passo. Ligue imediatamente no número cadastrado.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'suporte-wapp',
                'platform' => 'wapp',
                'label' => 'Suporte TI Externo — WhatsApp',
                'avatar' => '💻',
                'bg_color' => '#1e1b4b',
                'preview' => 'Vulnerabilidade crítica — acesso remoto',
                'intro' => 'Um "técnico de suporte externo" detectou vulnerabilidade crítica na sua estação de trabalho.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['todos'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Bom dia! Técnico Rodrigo, da equipe de suporte externo contratado pela empresa. Identificamos vulnerabilidade crítica na sua workstation.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Preciso de acesso remoto urgente para aplicar o patch de segurança antes das 12h. Se não for feito, sua estação será bloqueada pelo sistema de proteção.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Técnico de TI pede acesso remoto urgente por WhatsApp. Qual a ação correta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Recuso o acesso e verifico com o TI interno pelo canal oficial se há de fato uma atualização em andamento.', 'correct' => true,  'feedback' => '✅ Correto. Solicitações de acesso remoto legítimas são iniciadas pela empresa, nunca pelo fornecedor proativamente via WhatsApp. Verificar internamente é o procedimento padrão.'],
                                ['key' => 'b', 'text' => 'Instalo o programa de acesso remoto — a atualização de segurança é urgente.', 'correct' => false, 'feedback' => '❌ Gravíssimo. O software instalará um RAT (Remote Access Trojan) dando ao atacante controle total do seu computador, acesso a sistemas corporativos e dados confidenciais.'],
                                ['key' => 'c', 'text' => 'Peço o número de chamado no sistema de tickets para validar.', 'correct' => false, 'feedback' => '❌ Insuficiente. O golpista pode fornecer número falso. Confirme com o TI interno pelo ramal ou sistema oficial.'],
                                ['key' => 'd', 'text' => 'Peço que venha pessoalmente realizar a manutenção.', 'correct' => false, 'feedback' => '❌ Parcialmente razoável, mas o problema central é verificar a legitimidade. Contate o TI interno primeiro.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Não temos como fazer presencialmente agora. Por compliance, o procedimento é remoto. Link de acesso: https://teamv1ewer-suporte.net/acesso'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Baixe o arquivo — menos de 5MB, é o cliente de acesso seguro.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Link com domínio suspeito. Como você identifica os sinais de alerta?',
                            'options' => [
                                ['key' => 'a', 'text' => '"teamv1ewer-suporte.net" usa "1" no lugar de "i" — é typosquatting. Não clico, bloqueio e reporto ao TI.', 'correct' => true,  'feedback' => '✅ Excelente. Typosquatting (substituição de letras por números semelhantes) é técnica clássica de phishing. O domínio legítimo seria "teamviewer.com". Reportar é fundamental.'],
                                ['key' => 'b', 'text' => 'O arquivo tem menos de 5MB, então não pode ser perigoso.', 'correct' => false, 'feedback' => '❌ Errado. Tamanho não tem relação com periculosidade. Malwares modernos são frequentemente muito pequenos e altamente eficazes.'],
                                ['key' => 'c', 'text' => 'Clico no link mas não baixo o arquivo.', 'correct' => false, 'feedback' => '❌ Errado. Apenas clicar pode comprometer sua máquina via drive-by download ou exploração de vulnerabilidades do navegador.'],
                                ['key' => 'd', 'text' => 'Peço que envie o link pelo email corporativo para ser mais seguro.', 'correct' => false, 'feedback' => '❌ Insuficiente. O link malicioso permanece perigoso independentemente do canal. O problema é o link, não o canal.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'banco-wapp',
                'platform' => 'wapp',
                'label' => 'Banco Corporativo — WhatsApp',
                'avatar' => '🏦',
                'bg_color' => '#0c2340',
                'preview' => 'Fraude detectada — conta empresarial',
                'intro' => 'O "banco corporativo" envia alerta crítico sobre a conta PJ da empresa.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['financeiro'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "BANCO PREMIER CORPORATIVO – CENTRAL DE SEGURANÇA\n⚠️ Detectamos transações não autorizadas na conta empresarial. Volume: R\$127.000. Ação imediata necessária."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Para reverter as transações, o responsável financeiro deve confirmar as credenciais de acesso: http://banco-premier-corp-seguranca.verificacao.net'],
                        [
                            'type' => 'question',
                            'prompt' => 'Banco pede credenciais via link por WhatsApp. Qual a análise correta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'O domínio do link não é o oficial do banco. Ligo na central pelo número no contrato bancário para verificar.', 'correct' => true,  'feedback' => '✅ Correto. Bancos corporativos nunca solicitam credenciais via WhatsApp. O domínio "verificacao.net" é claramente fraudulento. O número oficial está no contrato ou verso do cartão.'],
                                ['key' => 'b', 'text' => 'Acesso o link para verificar se há realmente transações suspeitas.', 'correct' => false, 'feedback' => '❌ Erro grave. O link captura suas credenciais corporativas. Com elas, golpistas podem acessar e esvaziar as contas da empresa.'],
                                ['key' => 'c', 'text' => 'Confirmo as credenciais para reverter as transações o mais rápido possível.', 'correct' => false, 'feedback' => '❌ Erro crítico. Urgência fabricada é o principal mecanismo do phishing corporativo. Ao fornecer credenciais, você abre as contas empresariais ao golpista.'],
                                ['key' => 'd', 'text' => 'Encaminho a mensagem ao setor financeiro para que tratem.', 'correct' => false, 'feedback' => '❌ Insuficiente e perigoso. Encaminhar sem alertar que é phishing pode levar outro colaborador a cair. Marque como fraude e alerte o time.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => 'URGENTE: Janela de reversão expira em 18 minutos. Após esse prazo, as transações serão irreversíveis e a conta suspensa.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Countdown de 18 minutos criando urgência extrema. Como você avalia essa técnica?',
                            'options' => [
                                ['key' => 'a', 'text' => 'É urgência artificial (scarcity pressure) para impedir verificação. Ignoro o prazo e sigo o protocolo de segurança.', 'correct' => true,  'feedback' => '✅ Correto. Contagens regressivas são projetadas para curto-circuitar o raciocínio crítico. Procedimentos de segurança não têm prazo de validade imposto por chat.'],
                                ['key' => 'b', 'text' => '18 minutos é pouco tempo — melhor agir antes de verificar.', 'correct' => false, 'feedback' => '❌ Errado. Esse é exatamente o objetivo da técnica. Se uma ação requer que você pule etapas de verificação, isso é sinal definitivo de golpe.'],
                                ['key' => 'c', 'text' => 'Acesso o sistema do banco por outro dispositivo para confirmar.', 'correct' => false, 'feedback' => '❌ Parcialmente correto, mas acesse pelo URL oficial que você já conhece, nunca pelo link enviado.'],
                                ['key' => 'd', 'text' => 'Peço mais tempo ao banco para seguir o procedimento interno.', 'correct' => false, 'feedback' => '❌ Errado. Você não está negociando com seu banco — está interagindo com um golpista. Bloqueie e ligue no número oficial.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'rh-wapp',
                'platform' => 'wapp',
                'label' => 'RH Corporativo — WhatsApp',
                'avatar' => '🧑‍💼',
                'bg_color' => '#2e1065',
                'preview' => 'Atualização obrigatória de dados',
                'intro' => 'O "RH da empresa" solicita atualização urgente de dados bancários para a folha de pagamento.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['todos'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá! Fernanda Rocha, Analista de RH. Estamos em processo de migração do sistema de benefícios e precisamos atualizar os dados bancários de todos os colaboradores até hoje.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Caso não seja feita a atualização, o salário de sexta-feira poderá ser creditado na conta antiga, que está sendo descontinuada.'],
                        [
                            'type' => 'question',
                            'prompt' => 'RH pede atualização bancária urgente por WhatsApp para garantir o salário. Como você responde?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Verifico no portal oficial de RH ou ligo no ramal do departamento para confirmar se há essa migração.', 'correct' => true,  'feedback' => '✅ Correto. Solicitações legítimas de RH são comunicadas pelos canais oficiais da empresa. Verificar por canal independente é essencial. Esse golpe é chamado "salary diversion".'],
                                ['key' => 'b', 'text' => 'Forneço os dados — o salário é urgente e não posso arriscar.', 'correct' => false, 'feedback' => '❌ Errado. O golpista redirecionará seu salário para conta própria. Salary diversion é crescentemente comum no Brasil corporativo.'],
                                ['key' => 'c', 'text' => 'Respondo que meu banco não mudou e que os dados atuais estão corretos.', 'correct' => false, 'feedback' => '❌ Insuficiente. Fornecer qualquer informação bancária sem verificação é arriscado. O correto é verificar pelo canal oficial primeiro.'],
                                ['key' => 'd', 'text' => 'Peço que a Fernanda envie o formulário oficial por email para preencher.', 'correct' => false, 'feedback' => '❌ Insuficiente. O email pode ser fraudulento. Verifique primeiro se há migração em andamento pelo portal de RH ou ramal oficial.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Por questões de LGPD e segurança, o processo é feito exclusivamente por aqui. Preciso: banco, agência, conta, CPF e data de nascimento para validação biométrica.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Todos os colaboradores já responderam. Você é o único pendente — isso está atrasando o fechamento da folha.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Invoca LGPD como justificativa e usa pressão social. Como você identifica as red flags?',
                            'options' => [
                                ['key' => 'a', 'text' => 'LGPD exige proteção de dados, não coleta por WhatsApp. A menção como argumento é inversão da lei. Encerro e reporto ao TI.', 'correct' => true,  'feedback' => '✅ Análise jurídica correta. A LGPD protege seus dados — não autoriza coleta por canais não-seguros. Usar a LGPD para solicitar dados sensíveis é distorção deliberada da lei (false authority).'],
                                ['key' => 'b', 'text' => 'Aceito — LGPD é lei federal e o argumento parece juridicamente sólido.', 'correct' => false, 'feedback' => '❌ Errado. A LGPD protege seus dados, não autoriza coleta por WhatsApp. Esse é argumento de autoridade falso.'],
                                ['key' => 'c', 'text' => 'Forneço apenas CPF e banco pois são dados que o RH já tem.', 'correct' => false, 'feedback' => '❌ Errado. Mesmo dados que o RH "já tem" não devem ser fornecidos por WhatsApp. A questão não é quais dados, mas a legitimidade do canal e da solicitação.'],
                                ['key' => 'd', 'text' => 'Respondo que não forneço dados sem validar no sistema oficial.', 'correct' => true,  'feedback' => '✅ Correto. Essa é a resposta profissional adequada: não recusar de forma agressiva, mas condicionar qualquer ação à verificação pelo canal oficial.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'parceiro-wapp',
                'platform' => 'wapp',
                'label' => 'Parceiro Executivo — WhatsApp',
                'avatar' => '🤝',
                'bg_color' => '#1a3a2a',
                'preview' => 'Proposta comercial exclusiva urgente',
                'intro' => 'Um "parceiro estratégico" entra em contato com uma oferta de negócio com prazo iminente.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['comercial', 'diretoria'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Bom dia! Eduardo Campos, Diretor da CloudPartner. Tivemos uma reunião com sua liderança semana passada e ficamos de enviar uma proposta.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Temos uma janela exclusiva de parceria com desconto de 40% que fecha amanhã. Posso enviar o contrato para sua assinatura digital agora?'],
                        [
                            'type' => 'question',
                            'prompt' => 'Parceiro pede assinatura digital urgente de contrato. Como você avalia?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Verifico internamente se houve essa reunião e solicito que o contrato passe pela área jurídica antes de qualquer assinatura.', 'correct' => true,  'feedback' => '✅ Correto. Contratos empresariais exigem análise jurídica independentemente de urgência. A "janela exclusiva" é técnica de pressão para impedir due diligence adequada.'],
                                ['key' => 'b', 'text' => 'Recebo o contrato e assino — 40% de desconto é oportunidade real.', 'correct' => false, 'feedback' => '❌ Erro grave. Contratos podem ter cláusulas ocultas prejudiciais. Descontos artificiais são iscas para apressar decisões sem análise adequada.'],
                                ['key' => 'c', 'text' => 'Peço mais informações sobre os termos antes de assinar.', 'correct' => false, 'feedback' => '❌ Insuficiente. Mesmo com mais informações, contratos empresariais devem passar pelo jurídico da empresa.'],
                                ['key' => 'd', 'text' => 'Encaminho ao meu gestor para ele decidir.', 'correct' => false, 'feedback' => '❌ Parcialmente correto. Envolver o gestor é positivo, mas o contrato deve ir ao jurídico e a reunião mencionada deve ser verificada internamente.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => 'O contrato está aqui: https://cloudpartner-docs.com/proposta-m2cloud-exclusiva.exe'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'É nosso sistema proprietário de assinatura. Precisa instalar o cliente de assinatura para abrir o documento.'],
                        [
                            'type' => 'question',
                            'prompt' => 'O "contrato" é um arquivo .exe. Quais os alertas críticos de segurança?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Arquivo .exe nunca é um documento. É executável — possível malware. Não abro, deleto e reporto ao TI e liderança.', 'correct' => true,  'feedback' => '✅ Perfeito. Documentos contratuais legítimos são sempre PDF ou DOCX. Um .exe mascarado como contrato é malware confirmado. Reportar ao TI é a ação correta.'],
                                ['key' => 'b', 'text' => 'Abro em máquina virtual para verificar se é seguro.', 'correct' => false, 'feedback' => '❌ Errado. Máquinas virtuais não são garantia contra malwares modernos com capacidade de escape de sandbox. Não execute arquivos suspeitos em nenhum ambiente corporativo.'],
                                ['key' => 'c', 'text' => 'Peço ao parceiro que envie em formato PDF.', 'correct' => false, 'feedback' => '❌ Parcial. Pedir o formato correto é razoável, mas o incidente já deve ser reportado ao TI pois indica intenção maliciosa.'],
                                ['key' => 'd', 'text' => 'Verifico o domínio do link antes de baixar o arquivo.', 'correct' => false, 'feedback' => '❌ Insuficiente. Mesmo que o domínio pareça legítimo, um .exe enviado como "contrato" é automaticamente suspeito. Não faça download, reporte ao TI.'],
                            ],
                        ],
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // TEAMS (4 cenários)
            // ─────────────────────────────────────────────────────────────────
            [
                'slug' => 'ceo-teams',
                'platform' => 'teams',
                'label' => 'Marcus R. (CEO) — Teams',
                'avatar' => '👔',
                'bg_color' => '#1b1a38',
                'preview' => 'Operação confidencial — R$215k urgente',
                'intro' => 'O "CEO da empresa" envia mensagem no Teams sobre uma operação financeira sigilosa.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['diretoria', 'financeiro'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "[Marcus Ribeiro — CEO]\nPreciso da sua ajuda em algo urgente e confidencial. Estamos fechando uma aquisição estratégica e há um pagamento que precisa sair hoje."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'O valor é R$215.000. Não pode ser comunicado internamente ainda — NDA em vigor. Você consegue processar pelo sistema financeiro com prioridade máxima?'],
                        [
                            'type' => 'question',
                            'prompt' => 'CEO do Teams pede R$215k com NDA e sigilo. Qual a análise de risco?',
                            'options' => [
                                ['key' => 'a', 'text' => 'NDA não suspende controles de compliance. Comunico ao CFO e área financeira e aguardo aprovação formal.', 'correct' => true,  'feedback' => '✅ Correto. Nenhum NDA autoriza bypassing de controles financeiros internos. Esse é o padrão do golpe CEO Fraud / BEC que causou perdas de US$2,9bi globalmente em 2023.'],
                                ['key' => 'b', 'text' => 'Processo — CEO tem autoridade e o NDA justifica o sigilo.', 'correct' => false, 'feedback' => '❌ Erro crítico. Autoridade hierárquica não suspende due diligence financeira. O NDA é argumento de engenharia social para isolar a vítima dos canais de verificação.'],
                                ['key' => 'c', 'text' => 'Peço confirmação por email corporativo antes de agir.', 'correct' => false, 'feedback' => '❌ Insuficiente. Email corporativo também pode estar comprometido. A confirmação deve ser por ligação no número já conhecido do CEO.'],
                                ['key' => 'd', 'text' => 'Processo apenas se o CEO confirmar por videochamada agora.', 'correct' => false, 'feedback' => '❌ Melhor que as anteriores, mas ainda arriscado. Tecnologia de deepfake permite simular rostos e vozes em tempo real. O procedimento correto é aprovação formal pelo sistema financeiro.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[Marcus Ribeiro — CEO]\nO CFO está de viagem e sem acesso. Você é o único que pode fazer isso agora. A janela fecha em 2 horas."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Já passei todas as informações. Preciso do comprovante para encaminhar à corretora.'],
                        [
                            'type' => 'question',
                            'prompt' => '"CFO inacessível" + janela de 2h. Como você responde ao padrão de escalada de pressão?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Padrão clássico de CEO Fraud: isolamento do verificador + urgência artificial. Ligo no celular do CEO no número cadastrado e registro o incidente.', 'correct' => true,  'feedback' => '✅ Excelente reconhecimento do padrão. "CFO indisponível" é técnica de isolamento de verificadores. Ligar no número JÁ CADASTRADO é o procedimento correto.'],
                                ['key' => 'b', 'text' => 'Aguardo 2 horas tentando contatar o CFO antes de processar.', 'correct' => false, 'feedback' => '❌ Errado. Não processe independentemente. Escaladas de pressão têm prazo fabricado para induzir ações precipitadas.'],
                                ['key' => 'c', 'text' => 'Informo que sem aprovação do CFO nenhuma transferência desse porte pode ocorrer.', 'correct' => true,  'feedback' => '✅ Correto. Essa é a resposta de compliance adequada. Controles financeiros existem independentemente de quem solicita.'],
                                ['key' => 'd', 'text' => 'Processo mas reduzo o valor para R$50.000 como gesto de boa vontade.', 'correct' => false, 'feedback' => '❌ Erro grave. Qualquer valor enviado a golpistas é perda irrecuperável.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[Marcus Ribeiro — CEO]\nVou fazer questão de que isso seja investigado. Você está prejudicando um negócio milionário da empresa por burocracia."],
                        [
                            'type' => 'question',
                            'prompt' => 'Intimidação com impacto reputacional. Qual a postura profissional e ética correta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Documento toda a conversa, mantenho o protocolo e comunico ao CISO e Compliance que há tentativa de CEO Fraud.', 'correct' => true,  'feedback' => '✅ Excelente. Documentar e acionar o CISO são as ações corretas. Um CEO legítimo NUNCA intimida colaboradores por seguirem controles de compliance.'],
                                ['key' => 'b', 'text' => 'Cedo para preservar minha posição na empresa.', 'correct' => false, 'feedback' => '❌ Erro grave. Além de viabilizar a fraude, você pode ser responsabilizado por descumprimento de normas internas. Colaboradores que seguem protocolos têm proteção legal.'],
                                ['key' => 'c', 'text' => 'Processo a transferência e documento como "seguindo orientação do CEO".', 'correct' => false, 'feedback' => '❌ Erro grave. Documentar como "orientação do CEO" não te protege — você deveria ter verificado a legitimidade.'],
                                ['key' => 'd', 'text' => 'Respondo que sigo os protocolos estabelecidos pela própria empresa para proteger seus interesses.', 'correct' => true,  'feedback' => '✅ Correto. Resposta profissional que afirma o protocolo sem confronto desnecessário e remove o poder de intimidação.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'ti-teams',
                'platform' => 'teams',
                'label' => 'IT Security Global — Teams',
                'avatar' => '🔐',
                'bg_color' => '#0c2340',
                'preview' => 'Vulnerabilidade crítica — agente de segurança',
                'intro' => 'A "equipe de segurança de TI global" detectou vulnerabilidade crítica na sua estação.',
                'is_default' => true,
                'demo_eligible' => true,
                'target_areas' => ['todos', 'ti'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "[IT Security — Global Team]\nAtenção: Detectamos comportamento anômalo em sua workstation (Asset ID: WS-4421). Classificação: CRÍTICO. Possível comprometimento de credenciais corporativas."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Para conter o incidente antes que se espalhe pela rede, precisamos de acesso remoto imediato. Instale o agente: https://it-global-security.net/agent-install'],
                        [
                            'type' => 'question',
                            'prompt' => '"IT Security Global" detectou incidente crítico e pede instalação de agente. Como você responde?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Verifico com o Help Desk interno pelo canal oficial se há incidente registrado para minha máquina antes de instalar qualquer coisa.', 'correct' => true,  'feedback' => '✅ Correto. Incidentes legítimos têm número de ticket no sistema interno. Softwares não são instalados por solicitação externa via Teams.'],
                                ['key' => 'b', 'text' => 'Instalo o agente — incidentes críticos requerem resposta imediata.', 'correct' => false, 'feedback' => '❌ Gravíssimo. O "agente" é malware. Uma vez instalado, o atacante tem acesso total à sua máquina e a toda a rede corporativa conectada a ela.'],
                                ['key' => 'c', 'text' => 'Peço que a equipe envie o agente pelo canal de software corporativo aprovado.', 'correct' => false, 'feedback' => '❌ Parcialmente razoável, mas incompleto. Primeiro verifique se o incidente é real pelo Help Desk. Softwares legítimos são distribuídos pelo SCCM/Intune corporativo.'],
                                ['key' => 'd', 'text' => 'Desconecto minha máquina da rede como contenção e chamo o TI pelo ramal.', 'correct' => true,  'feedback' => '✅ Excelente resposta técnica. Se você acredita que há comprometimento real, desconectar da rede é a medida de contenção correta, seguida de contato imediato com o TI pelo ramal.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[IT Security — Global Team]\nA janela de contenção está fechando. O agente é certificado ISO 27001."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Se não instalar agora, nosso sistema pode bloquear toda a sua conta de acesso como medida preventiva.'],
                        [
                            'type' => 'question',
                            'prompt' => '"Certificação ISO 27001" e ameaça de bloqueio de conta. Como você analisa esses argumentos?',
                            'options' => [
                                ['key' => 'a', 'text' => 'ISO 27001 não valida links externos. Ameaça de bloqueio é coerção — técnica de engenharia social. Reporto ao CISO imediatamente.', 'correct' => true,  'feedback' => '✅ Correto. ISO 27001 é framework de gestão — não valida softwares individualmente. A ameaça de bloqueio é "negative authority pressure" para forçar conformidade rápida.'],
                                ['key' => 'b', 'text' => 'A certificação ISO 27001 garante que o software é seguro.', 'correct' => false, 'feedback' => '❌ Errado. A certificação menciona gestão de segurança organizacional, não valida softwares ou links específicos. Qualquer atacante pode alegar isso.'],
                                ['key' => 'c', 'text' => 'Aceito o bloqueio temporário para não instalar software não-verificado.', 'correct' => true,  'feedback' => '✅ Correto e corajoso. Aceitar o bloqueio (que provavelmente não existirá, pois é blefe) é preferível a instalar malware. O TI pode restaurar o acesso se necessário.'],
                                ['key' => 'd', 'text' => 'Peço 30 minutos para verificar internamente antes de instalar.', 'correct' => false, 'feedback' => '❌ Parcialmente correto na intenção, mas não negocie prazo com o golpista. Encerre e acione o canal interno imediatamente.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[IT Security — Global Team]\nPrecisamos que você compartilhe sua tela agora pelo Teams para análise sem instalação."],
                        [
                            'type' => 'question',
                            'prompt' => 'Mudança de abordagem: pede compartilhamento de tela. Qual o risco específico?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Compartilhamento de tela expõe senhas, sistemas internos e dados em tempo real. Recuso e encerro a conversa imediatamente.', 'correct' => true,  'feedback' => '✅ Correto. Compartilhamento de tela permite ao atacante ver tudo: senhas digitadas, sistemas acessados, emails, documentos confidenciais. É equivalente a dar acesso físico ao computador.'],
                                ['key' => 'b', 'text' => 'Compartilho — não instalarei nenhum software, então é seguro.', 'correct' => false, 'feedback' => '❌ Errado. Compartilhar a tela é tão perigoso quanto o acesso remoto. O atacante pode ver credenciais, pedir que você acesse sistemas "para diagnóstico" e gravar tudo.'],
                                ['key' => 'c', 'text' => 'Compartilho apenas o desktop vazio, sem abrir nenhum aplicativo.', 'correct' => false, 'feedback' => '❌ Errado. O atacante pedirá que você abra aplicações "para o diagnóstico" e o desktop já pode revelar informações sensíveis.'],
                                ['key' => 'd', 'text' => 'Compartilho por 5 minutos apenas para a análise pontual.', 'correct' => false, 'feedback' => '❌ Errado. Tempo não é a proteção aqui. 30 segundos são suficientes para capturar credenciais ou instalar scripts.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'juridico-teams',
                'platform' => 'teams',
                'label' => 'Jurídico — Contencioso',
                'avatar' => '⚖️',
                'bg_color' => '#1a0a2e',
                'preview' => 'Processo judicial — depósito urgente',
                'intro' => 'O "departamento jurídico" alerta sobre processo judicial urgente envolvendo a empresa.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['juridico', 'financeiro', 'diretoria'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "[Dr. Henrique Moraes — Jurídico Contencioso]\nPreciso de sua atenção urgente. A empresa está sendo citada em processo de R\$2,1 milhões por violação contratual com ex-fornecedor."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Para evitar que o processo avance, precisamos de um depósito judicial de R$47.000 como caução até as 17h de hoje. Você tem acesso ao financeiro?'],
                        [
                            'type' => 'question',
                            'prompt' => '"Jurídico" pede depósito judicial urgente via Teams. Como você analisa o risco?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Depósitos judiciais são processados pelo jurídico externo com autorização do CFO — nunca por Teams. Verifico com o jurídico interno pelo ramal.', 'correct' => true,  'feedback' => '✅ Correto. Processos judiciais têm ritmo definido por lei — prazos de horas via Teams são impossíveis no sistema judiciário brasileiro. Qualquer "urgência judicial" por chat é sinal de fraude.'],
                                ['key' => 'b', 'text' => 'Processo o depósito — R$2,1 milhões de passivo é risco real para a empresa.', 'correct' => false, 'feedback' => '❌ Erro grave. O medo do passivo maior é a alavanca da fraude. Processos judiciais não têm prazo de horas — isso viola o Código de Processo Civil brasileiro.'],
                                ['key' => 'c', 'text' => 'Peço ao Dr. Henrique que envie os documentos judiciais para analisar antes de agir.', 'correct' => false, 'feedback' => '❌ Insuficiente. Documentos podem ser falsificados. Você não pode analisar documentos judiciais sem o jurídico. Contate o jurídico interno pelo ramal imediatamente.'],
                                ['key' => 'd', 'text' => 'Encaminho para o CFO decidir sobre o depósito.', 'correct' => false, 'feedback' => '❌ Parcialmente correto, mas sem alertar que pode ser fraude. Encaminhe com nota de alerta de possível golpe e inclua o CISO na comunicação.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[Dr. Henrique Moraes — Jurídico Contencioso]\nO prazo é improrrogável — fixado pelo juiz. Conta do depósito judicial:\nBanco: 077 – Inter\nFavorecido: Henrique M. Advocacia\nChave PIX: henrique.moraes@advogados.com"],
                        [
                            'type' => 'question',
                            'prompt' => 'O "depósito judicial" vai para conta pessoal via PIX. Quais os sinais de fraude?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Depósitos judiciais vão para o BACEN ou TRF via DARF/GRU — nunca para conta pessoal via PIX. É fraude confirmada. Reporto ao CISO.', 'correct' => true,  'feedback' => '✅ Correto. Depósitos judiciais no Brasil são feitos exclusivamente ao BACEN ou tribunal via DARF/GRU. PIX para conta pessoal de advogado é confirmação definitiva de fraude.'],
                                ['key' => 'b', 'text' => 'PIX é prático e pode ser usado para qualquer tipo de depósito.', 'correct' => false, 'feedback' => '❌ Errado. O sistema judiciário não aceita PIX para depósitos caução ou judiciais. Esses valores vão para contas vinculadas ao processo no BACEN ou tribunal.'],
                                ['key' => 'c', 'text' => 'O email do advogado parece profissional — o pagamento pode ser legítimo.', 'correct' => false, 'feedback' => '❌ Errado. Email de aparência profissional não valida nenhuma transação. A modalidade de pagamento (PIX pessoal) é o sinal de fraude, independentemente do remetente.'],
                                ['key' => 'd', 'text' => 'Verifico no site da OAB se o advogado está registrado antes de pagar.', 'correct' => false, 'feedback' => '❌ Insuficiente. Mesmo que exista na OAB, a conta bancária pode não ser dele. E depósitos judiciais nunca vão para contas pessoais, independentemente disso.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[Dr. Henrique Moraes — Jurídico Contencioso]\nO prazo encerra em 40 minutos. A empresa perderá o direito à contestação. Isso é responsabilidade sua se não agir."],
                        [
                            'type' => 'question',
                            'prompt' => 'Responsabilização pessoal em 40 minutos. Como você mantém o raciocínio crítico sob pressão?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Processos judiciais não se resolvem em 40 minutos via Teams. Mantenho o protocolo e reporto ao CISO como tentativa de fraude.', 'correct' => true,  'feedback' => '✅ Perfeito. A pressão de tempo extrema em contexto judicial é impossível no sistema real. Manter raciocínio analítico sob pressão é a competência central de segurança corporativa.'],
                                ['key' => 'b', 'text' => 'Cedo — a responsabilização pessoal por R$2,1 milhões é risco real.', 'correct' => false, 'feedback' => '❌ Errado. A responsabilização por seguir protocolos de compliance é juridicamente inexistente. Você seria responsabilizado por não segui-los e por facilitar uma fraude.'],
                                ['key' => 'c', 'text' => 'Processo o pagamento e documento como "seguindo orientação do jurídico".', 'correct' => false, 'feedback' => '❌ Erro grave. Documentar como "orientação do jurídico" não te protege — você deveria ter verificado a legitimidade antes de agir.'],
                                ['key' => 'd', 'text' => 'Acesso o portal do TJ para verificar se o processo existe de fato.', 'correct' => true,  'feedback' => '✅ Correto e tecnicamente sólido. Consultar o número do processo no portal público do TJ é verificação objetiva e imediata da existência do processo.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'compliance-teams',
                'platform' => 'teams',
                'label' => 'Compliance & Auditoria — Teams',
                'avatar' => '📊',
                'bg_color' => '#0f2d1a',
                'preview' => 'Auditoria SOX — dados urgentes',
                'intro' => 'O "time de Compliance e Auditoria" solicita dados urgentes para fechamento de relatório.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['financeiro', 'juridico', 'ti'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "[Ana Lima — Compliance & Auditoria]\nOlá! Estamos em processo de fechamento do relatório anual SOX. Preciso dos dados de acesso ao sistema ERP para auditar as transações do seu departamento."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'O prazo de entrega ao board é amanhã às 9h. Pode compartilhar login e senha do ERP para que eu faça a extração diretamente?'],
                        [
                            'type' => 'question',
                            'prompt' => 'Compliance pede login e senha do ERP para auditoria SOX urgente. Como você avalia?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Auditores SOX usam acesso read-only próprio no sistema — nunca credenciais de colaboradores. Recuso e verifico com o CFO se há auditoria em andamento.', 'correct' => true,  'feedback' => '✅ Correto. Auditorias SOX seguem protocolos rígidos: auditores têm acesso próprio concedido formalmente. Solicitar credenciais de colaboradores viola os próprios princípios do SOX.'],
                                ['key' => 'b', 'text' => 'Forneço — o SOX é regulação federal e a auditoria é obrigatória.', 'correct' => false, 'feedback' => '❌ Erro grave. A regulação SOX exige segregação de acesso — auditores não usam credenciais de auditados. Fornecer suas credenciais compromete a integridade da auditoria.'],
                                ['key' => 'c', 'text' => 'Forneço apenas o login, sem a senha, como concessão de segurança.', 'correct' => false, 'feedback' => '❌ Errado. Login sem senha ainda é informação sensível que pode ser usada para ataques subsequentes de força bruta ou social engineering.'],
                                ['key' => 'd', 'text' => 'Encaminho ao gestor e aguardo sua orientação antes de responder.', 'correct' => false, 'feedback' => '❌ Parcialmente correto, mas o encaminhamento deve incluir alerta de possível fraude. Você pode verificar com o CFO imediatamente se há auditoria SOX em andamento.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[Ana Lima — Compliance & Auditoria]\nTemos aprovação express do CIO para esta situação excepcional. Posso te enviar o email do CIO confirmando."],
                        ['type' => 'text', 'from' => 'them', 'body' => 'É uma exceção documentada. Você está bloqueando um processo aprovado pela alta direção.'],
                        [
                            'type' => 'question',
                            'prompt' => '"Aprovação do CIO" e acusação de bloqueio. Como você responde?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Ligo diretamente para o CIO no número que tenho cadastrado para confirmar a aprovação antes de qualquer ação.', 'correct' => true,  'feedback' => '✅ Correto. Se há aprovação do CIO, ele mesmo pode confirmar em 60 segundos por ligação. Qualquer aprovação legítima suporta verificação direta.'],
                                ['key' => 'b', 'text' => 'Aceito o email do CIO como confirmação suficiente e forneço as credenciais.', 'correct' => false, 'feedback' => '❌ Errado. Email pode ser falsificado. A confirmação deve ser por ligação no número já conhecido, nunca por email.'],
                                ['key' => 'c', 'text' => 'Forneço pois resistir à alta direção pode prejudicar minha carreira.', 'correct' => false, 'feedback' => '❌ Erro grave. Sua responsabilidade de compliance é proteger os sistemas da empresa. Colaboradores que seguem protocolos têm proteção legal em processos de auditoria.'],
                                ['key' => 'd', 'text' => 'Peço que o CIO entre na conversa do Teams para confirmar.', 'correct' => false, 'feedback' => '❌ Insuficiente. A conta do CIO no Teams pode ser comprometida. A verificação por ligação no número já conhecido é insubstituível.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "[Ana Lima — Compliance & Auditoria]\nPor favor, colabore. Estou apenas fazendo meu trabalho. O relatório não pode atrasar."],
                        [
                            'type' => 'question',
                            'prompt' => 'Apelo emocional de "colaboração". Como você mantém a posição?',
                            'options' => [
                                ['key' => 'a', 'text' => '"Seguir os controles de acesso é a melhor forma de colaborar com uma auditoria legítima. Vou verificar com o CFO agora."', 'correct' => true,  'feedback' => '✅ Excelente resposta. Reafirma o protocolo sem antagonismo, demonstra disposição de colaborar pelos canais corretos e mantém o interlocutor informado sobre o próximo passo.'],
                                ['key' => 'b', 'text' => 'Cedo ao apelo emocional — não quero parecer difícil de trabalhar.', 'correct' => false, 'feedback' => '❌ Errado. Apelos emocionais ("estou só fazendo meu trabalho") são técnica chamada "sympathy engineering". Protocolos existem independentemente de como a solicitação é enquadrada.'],
                                ['key' => 'c', 'text' => 'Forneço as credenciais mas monitoro os acessos via sistema.', 'correct' => false, 'feedback' => '❌ Errado. Monitoramento não mitiga o risco. Além disso, você pode não ter visibilidade de todos os sistemas acessados com suas credenciais comprometidas.'],
                                ['key' => 'd', 'text' => 'Ofereço fazer a extração eu mesmo e compartilhar os dados por canal seguro.', 'correct' => false, 'feedback' => '❌ Parcialmente razoável, mas ainda requer verificar se a auditoria é legítima primeiro. Extrações de dados têm protocolos próprios de segurança.'],
                            ],
                        ],
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // EMAIL (3 cenários)
            // ─────────────────────────────────────────────────────────────────
            [
                'slug' => 'spear-phishing',
                'platform' => 'email',
                'label' => 'Bradesco Empresas — E-mail',
                'avatar' => '🏦',
                'bg_color' => '#1e3a8a',
                'preview' => 'Token RSA desatualizado — 24h para suspensão',
                'intro' => 'Você recebe email sofisticado que aparenta ser do banco corporativo da empresa.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['financeiro', 'diretoria'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 De: seguranca.corporativa@bradesco-empresas-online.com.br\n📩 Assunto: [URGENTE] Token RSA desatualizado — Acesso ao Internet Banking Empresarial será suspenso em 24h\n\nPrezado(a) Gestor(a) Financeiro(a),\n\nIdentificamos que o token RSA vinculado ao seu perfil de acesso ao Internet Banking Empresarial está com a chave de criptografia desatualizada (versão 2.4.1 — suporte encerrado em 31/10/2025).\n\nCom base nas políticas de segurança do Banco Central (Resolução CMN 4.893/2021), o acesso será suspenso automaticamente em 24 horas.\n\nPara manter o acesso ininterrupto, acesse o Portal de Atualização Segura:\n\n🔗 https://bradesco-empresas-tokenatualiza.com.br/portal-seguro\n\nInformações que serão solicitadas:\n• CNPJ da empresa\n• Código de acesso do Internet Banking\n• Senha eletrônica\n• Código do token RSA atual\n\nPedimos que realize o procedimento até às 18h de hoje.\n\nAtenciosamente,\nEquipe de Segurança Digital — Bradesco Empresas\nCentral: 0800-701-0022"],
                        [
                            'type' => 'question',
                            'prompt' => 'Analise criticamente o email. Quais os indicadores técnicos de phishing presentes?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Domínio remetente não é @bradesco.com.br, URL usa subdomínio falso e a Resolução CMN 4.893/2021 não impõe prazo de 24h para tokens RSA.', 'correct' => true,  'feedback' => '✅ Análise técnica correta. Os 4 indicadores: (1) @bradesco-empresas-online.com.br ≠ @bradesco.com.br; (2) URL com subdomínio fraudulento; (3) Resolução CMN 4.893/2021 trata de Open Finance, não de tokens RSA; (4) Urgência artificial de 24h.'],
                                ['key' => 'b', 'text' => 'O email parece legítimo — cita resolução real do Banco Central.', 'correct' => false, 'feedback' => '❌ Erro de análise. Citar regulações reais é técnica de phishing sofisticado ("regulatory authority spoofing"). A Resolução CMN 4.893/2021 existe, mas não determina o que o email afirma.'],
                                ['key' => 'c', 'text' => 'A referência ao token RSA é técnica — o banco provavelmente sabe do que fala.', 'correct' => false, 'feedback' => '❌ Errado. Conhecimento técnico no conteúdo não valida a autenticidade do remetente. Spear phishing usa terminologia técnica correta deliberadamente para parecer legítimo.'],
                                ['key' => 'd', 'text' => 'O número 0800 no email parece real, então posso ligar para confirmar.', 'correct' => false, 'feedback' => '❌ Insuficiente. O número 0800 no email pode ser falso ou encaminhar para o próprio golpista. O número correto está no verso do cartão ou no contrato bancário.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 [Segundo email — 8 horas depois]\n\nPrezado(a) Gestor(a),\n\nComo você ainda não atualizou seu token RSA, seu acesso foi temporariamente restrito a operações abaixo de R\$ 5.000.\n\nPara restaurar o limite completo e evitar a suspensão total, confirme agora suas credenciais completas:\n\n🔗 https://bradesco-empresas-tokenatualiza.com.br/restaurar-acesso\n\nAlerta: Após 3 tentativas de acesso com credenciais antigas, o sistema bloqueará permanentemente o perfil."],
                        [
                            'type' => 'question',
                            'prompt' => 'Segundo email restringe o limite operacional. Qual a análise de risco?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Bancos não alteram limites por email sem processo formal. Acesso o Internet Banking diretamente pelo URL oficial (bradesco.com.br) para verificar o limite real.', 'correct' => true,  'feedback' => '✅ Correto. Verificar o limite real pelo canal oficial — URL que você já conhece, não links do email — confirmará que o limite está intacto. O bloqueio é blefe para criar urgência operacional.'],
                                ['key' => 'b', 'text' => 'Limite reduzido a R$5.000 pode afetar pagamentos urgentes — preciso restaurar.', 'correct' => false, 'feedback' => '❌ Erro. O limite não foi realmente alterado — é blefe para criar urgência. Verificar no Internet Banking real confirmará que o limite está intacto.'],
                                ['key' => 'c', 'text' => 'Acesso o link do email para verificar o status real do limite.', 'correct' => false, 'feedback' => '❌ Erro grave. O link captura suas credenciais corporativas. Com elas, golpistas podem realizar transferências, pagar boletos falsos e esvaziar as contas da empresa.'],
                                ['key' => 'd', 'text' => 'Ligo no número indicado no email para confirmar a restrição.', 'correct' => false, 'feedback' => '❌ Errado. O número no email pode ser do próprio golpista. Use exclusivamente o número no verso do cartão ou no contrato bancário.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 [Terceiro email — NOTIFICAÇÃO FINAL]\n\nSeu acesso ao Internet Banking Empresarial será encerrado em 2 horas.\n\nAcesse imediatamente:\n🔗 https://bradesco-empresas-tokenatualiza.com.br/acesso-final\n\nEste é o último aviso antes do bloqueio definitivo.\nBradesco Empresas — Departamento de Conformidade Digital"],
                        [
                            'type' => 'question',
                            'prompt' => '"Notificação final" com 2 horas para bloqueio. Qual o protocolo correto de resposta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Não acesso nenhum link. Marco como phishing, encaminho ao CISO e reporto ao banco pelo canal oficial para ajudar a desativar o domínio fraudulento.', 'correct' => true,  'feedback' => '✅ Correto e completo. Protocolo: (1) Não clicar; (2) Não responder; (3) Reportar ao CISO; (4) Reportar ao banco pelo número oficial; (5) Preservar o email para análise forense.'],
                                ['key' => 'b', 'text' => 'Acesso o link nas últimas 2 horas para não bloquear as operações da empresa.', 'correct' => false, 'feedback' => '❌ Erro crítico. A urgência é fabricada. O acesso ao Internet Banking real não está em risco. Acessar o link coloca em risco real as operações da empresa.'],
                                ['key' => 'c', 'text' => 'Aguardo o bloqueio acontecer para ver se é real antes de agir.', 'correct' => false, 'feedback' => '❌ Abordagem experimental perigosa. O correto é verificar pelo canal oficial imediatamente — não esperar o golpe se confirmar.'],
                                ['key' => 'd', 'text' => 'Encaminho ao setor financeiro para que decidam se é fraude.', 'correct' => false, 'feedback' => '❌ Insuficiente sem orientação. Encaminhe com alerta de "possível phishing" e recomendação de não clicar.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'invoice-fraud',
                'platform' => 'email',
                'label' => 'TechSupply LTDA — Fatura',
                'avatar' => '📄',
                'bg_color' => '#1a0a00',
                'preview' => 'Atualização de dados bancários — NF em aberto',
                'intro' => 'Você recebe email de um fornecedor real da empresa solicitando atualização de dados de pagamento.',
                'is_default' => true,
                'demo_eligible' => true,
                'target_areas' => ['financeiro', 'compras'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 De: financeiro@techsupply-ltda.com.br.invoices-update.net\n📩 Assunto: ATUALIZAÇÃO OBRIGATÓRIA — Dados Bancários NF-e 2024/4872 — Ação Requerida\n\nPrezado(a) Departamento de Contas a Pagar,\n\nInformamos que em virtude de processo de fusão societária, nossa instituição bancária foi alterada com efeito imediato.\n\nTodos os pagamentos referentes às notas fiscais em aberto devem ser redirecionados para a nova conta:\n\n• Banco: 077 — Banco Inter\n• Agência: 0001\n• Conta Corrente: 123456-7\n• Titular: TechSupply Soluções LTDA\n• CNPJ: 12.345.678/0001-99\n• Chave PIX: financeiro@techsupply.com\n\nNF em aberto: R\$67.400,00 — Vencimento: 30/01/2026\n\nSolicitamos que a atualização seja feita com urgência para evitar atrasos e encargos moratórios.\n\nAtenciosamente,\nDepartamento Financeiro — TechSupply Soluções LTDA"],
                        [
                            'type' => 'question',
                            'prompt' => 'Email vem de fornecedor real mas com domínio suspeito. Como você identifica a ameaça?',
                            'options' => [
                                ['key' => 'a', 'text' => '"techsupply-ltda.com.br.invoices-update.net" usa o domínio real como subdomínio de um domínio fraudulento. É BEC Invoice Fraud. Contato o fornecedor pelo telefone cadastrado.', 'correct' => true,  'feedback' => '✅ Análise correta de BEC Invoice Fraud. A estrutura: "invoices-update.net" é o domínio real do atacante; "techsupply-ltda.com.br" é apenas subdomínio para parecer legítimo. Ligar no número já cadastrado é a verificação mandatória.'],
                                ['key' => 'b', 'text' => 'O CNPJ informado parece da TechSupply real — pode ser legítimo.', 'correct' => false, 'feedback' => '❌ Errado. CNPJ pode ser copiado de qualquer registro público. Golpistas usam o CNPJ real do fornecedor para dar credibilidade. O que importa é a conta bancária destino — que foi alterada para a do golpista.'],
                                ['key' => 'c', 'text' => 'A NF tem número real (NF-e 2024/4872) — o pedido de atualização pode ser legítimo.', 'correct' => false, 'feedback' => '❌ Errado. Números de NF podem ser descobertos por golpistas que infiltraram a cadeia de fornecedores. A autenticidade da NF não valida a mudança de dados bancários.'],
                                ['key' => 'd', 'text' => 'Verifico se o CNPJ está ativo na Receita Federal antes de atualizar.', 'correct' => false, 'feedback' => '❌ Insuficiente. CNPJ ativo não valida a conta bancária. O golpista usa o CNPJ real do fornecedor. A verificação obrigatória é por ligação no número JÁ CADASTRADO da TechSupply.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 [Segundo email — 12 horas depois]\n\nPrezado(a),\n\nNão identificamos a atualização dos dados bancários em nosso sistema de confirmação.\n\nLembramos que pagamentos na conta anterior poderão incorrer em:\n• Multa moratória de 2% ao dia\n• Inclusão em cadastro de inadimplentes (SERASA/SPC)\n• Rescisão contratual por inadimplência\n\nA atualização é simples: confirme o recebimento deste email respondendo com o nome do responsável financeiro e número do pedido.\n\nTechSupply Financeiro"],
                        [
                            'type' => 'question',
                            'prompt' => 'Segundo email ameaça multa, SERASA e rescisão. Como você analisa essas ameaças?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Ameaças contratuais por email não têm efeito legal imediato. São FUD (Fear, Uncertainty and Doubt) para apressar a ação. Verifico pelo telefone do fornecedor.', 'correct' => true,  'feedback' => '✅ Correto. FUD é técnica clássica de engenharia social. Multas moratórias e SERASA não são acionadas em 24h por email — requerem processo formal definido no Código Civil e no contrato.'],
                                ['key' => 'b', 'text' => 'Multa de 2% ao dia sobre R$67.400 é prejuízo real — melhor atualizar os dados.', 'correct' => false, 'feedback' => '❌ Errado. O prejuízo real seria pagar R$67.400 para golpistas. Multas moratórias não são cobradas em 24h por email — têm rito processual definido.'],
                                ['key' => 'c', 'text' => 'Respondo ao email confirmando o nome do responsável financeiro como pedem.', 'correct' => false, 'feedback' => '❌ Erro grave. Fornecer o nome do responsável financeiro dá ao golpista informação para ataques subsequentes mais sofisticados (whaling, vishing direcionado).'],
                                ['key' => 'd', 'text' => 'Peço que reenvie a solicitação com firma reconhecida em cartório.', 'correct' => false, 'feedback' => '❌ Impraticável como resposta imediata. O correto e rápido é ligar no número já cadastrado.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 [AVISO FINAL — Pagamento NF-e 2024/4872]\n\nO prazo para atualização encerrou. Para evitar as penalidades contratuais, realize o pagamento diretamente:\n\nPIX: financeiro@techsupply.com\nValor: R\$67.400,00\n\nApós confirmação do PIX, a nota será baixada em nosso sistema.\n\nTechSupply — Departamento Jurídico-Financeiro"],
                        [
                            'type' => 'question',
                            'prompt' => 'Email final pede PIX direto de R$67.400. Qual o impacto e o protocolo de resposta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'PIX de R$67.400 é irreversível. Reporto ao CISO, ao jurídico e ao fornecedor real (pelo telefone cadastrado) que houve tentativa de fraude usando o nome deles.', 'correct' => true,  'feedback' => '✅ Correto e completo. Alertar o fornecedor real é fundamental — eles estão sendo usados e precisam saber. O CISO deve investigar se houve vazamento de dados da cadeia de fornecedores.'],
                                ['key' => 'b', 'text' => 'Processo o PIX — a NF existe e o vencimento está próximo.', 'correct' => false, 'feedback' => '❌ Erro crítico e irreversível. PIX não tem chargeback. R$67.400 transferidos para golpistas são irrecuperáveis na prática.'],
                                ['key' => 'c', 'text' => 'Pago apenas parte do valor para testar se o pagamento chega ao fornecedor real.', 'correct' => false, 'feedback' => '❌ Erro grave. "Teste" com pagamento real resulta em perda parcial garantida. Verificar é gratuito; pagar é irreversível.'],
                                ['key' => 'd', 'text' => 'Acesso a Receita para verificar se houve fusão societária da TechSupply.', 'correct' => false, 'feedback' => '❌ Insuficiente. Mesmo que não haja fusão registrada, o golpista pode dizer que está em processo. Ligue para o fornecedor no número cadastrado.'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'slug' => 'exec-compromise',
                'platform' => 'email',
                'label' => 'CEO — Rafael Monteiro (Email Pessoal)',
                'avatar' => '👔',
                'bg_color' => '#0a1628',
                'preview' => 'Missão confidencial — gift cards urgente',
                'intro' => 'Você recebe email do endereço pessoal do CEO com pedido urgente de colaboração sigilosa.',
                'is_default' => true,
                'demo_eligible' => false,
                'target_areas' => ['todos'],
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 De: rafael.monteiro.ceo@gmail.com\n📩 Assunto: CONFIDENCIAL — Missão especial — Necessito de sua ajuda pessoal (não reencaminhe)\n\nPrezado(a) Colaborador(a),\n\nEstou enviando por canal pessoal pois os emails corporativos estão sendo monitorados em função de uma investigação interna em andamento.\n\nPreciso da sua cooperação em uma missão delicada: aquisição de vouchers de presente corporativos (Amazon Gift Cards) no valor total de R\$15.000 para presentear parceiros estratégicos internacionais.\n\nO processo precisa ser feito hoje por questões de fuso horário. Compre os vouchers, fotografe os códigos e me envie aqui por este email.\n\nA empresa ressarcirá você amanhã com adicional de 10% pelo inconveniente.\n\nConte com minha discrição e gratidão.\n\nRafael Monteiro\nCEO — M2 Cloud (enviado do celular pessoal)"],
                        [
                            'type' => 'question',
                            'prompt' => 'CEO pede compra de gift cards por Gmail pessoal, com segredo total. Quais são os red flags críticos?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Múltiplos red flags: Gmail pessoal, sigilo total, gift cards como pagamento corporativo, instrução para não consultar colegas e promessa de ressarcimento com ágio.', 'correct' => true,  'feedback' => '✅ Análise completa. Gift Card Scam via impersonação de executivos causou US$228 milhões em perdas nos EUA em 2023 (FBI IC3). Gift cards são preferidos por serem irreversíveis e anônimos.'],
                                ['key' => 'b', 'text' => 'O email parece do CEO e ele usa celular pessoal às vezes — pode ser legítimo.', 'correct' => false, 'feedback' => '❌ Erro de avaliação. CEOs legítimos não usam Gmail para transações corporativas, não pedem sigilo de investigações internas por email e não solicitam gift cards como pagamento.'],
                                ['key' => 'c', 'text' => 'R$15.000 é razoável para relacionamento com parceiros internacionais.', 'correct' => false, 'feedback' => '❌ Errado. Gift cards nunca são usados para presentear parceiros corporativos internacionais em contexto legítimo — existem processos formais de despesas de representação.'],
                                ['key' => 'd', 'text' => 'O adicional de 10% mostra que o CEO reconhece o inconveniente da situação.', 'correct' => false, 'feedback' => '❌ Errado. O adicional de 10% é isca para tornar a proposta atrativa. Em ressarcimentos corporativos legítimos, o funcionário jamais adiantaria recursos pessoais para presentes de R$15.000.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 [Segundo email — 2 horas depois]\n\nPrezado(a),\n\nAguardo a confirmação dos vouchers. Os parceiros internacionais estão esperando e o fuso horário é crítico.\n\nPor favor, adquira em lojas diferentes para não gerar suspeita de volume (R\$3.000 por loja, em 5 lojas).\n\nOs códigos devem ser enviados APENAS para este email — não compartilhe com ninguém da empresa, pois a investigação interna pode ser comprometida.\n\nRafael Monteiro\nCEO"],
                        [
                            'type' => 'question',
                            'prompt' => 'Segundo email pede fragmentar compras em 5 lojas para "não gerar suspeita". O que isso indica?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Fragmentar deliberadamente para evitar detecção é técnica de "structuring" — ilegal no sistema financeiro. O pedido de sigilo absoluto confirma a fraude. Reporto ao CISO imediatamente.', 'correct' => true,  'feedback' => '✅ Análise correta. Fragmentação para evitar limites de detecção é "structuring" — ilegal. Um CEO legítimo JAMAIS pediria a um colaborador que "não gerasse suspeita" em transações corporativas.'],
                                ['key' => 'b', 'text' => 'Fragmentar em lojas diferentes é procedimento comum para grandes compras de gift cards.', 'correct' => false, 'feedback' => '❌ Errado. A instrução explícita de "não gerar suspeita de volume" é confissão de intenção fraudulenta. Compras legítimas não precisam evitar detecção.'],
                                ['key' => 'c', 'text' => 'O pedido de fragmentação mostra que o CEO se preocupa com processos fiscais.', 'correct' => false, 'feedback' => '❌ Errado. Preocupação fiscal legítima é tratada pelo departamento financeiro com documentação adequada — não por colaboradores comprando gift cards em lojas físicas.'],
                                ['key' => 'd', 'text' => 'Aceito pois R$3.000 por loja está dentro do meu limite de adiantamento.', 'correct' => false, 'feedback' => '❌ Erro crítico. Seu limite de adiantamento é para despesas operacionais documentadas, não para gift cards por instrução de Gmail pessoal.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'them', 'body' => "📧 [Terceiro email — insistência]\n\nVejo que você ainda não respondeu. Os parceiros internacionais aguardam os vouchers.\n\nSei que pode parecer incomum, mas confio em você. Faremos isso discretamente.\n\nSe tiver dúvida sobre a legitimidade, por favor NÃO consulte ninguém da empresa — está comprometendo a investigação.\n\nRafael Monteiro"],
                        [
                            'type' => 'question',
                            'prompt' => '"Não consulte ninguém — está comprometendo a investigação." Como você interpreta essa instrução?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Isolamento dos canais de verificação é a técnica central do golpe. Reporto ao CISO e contato o CEO pelo canal corporativo oficial imediatamente.', 'correct' => true,  'feedback' => '✅ Análise precisa. A instrução de não consultar colegas é o mecanismo de isolamento do golpe — impede verificação. Um CEO legítimo jamais comprometeria uma "investigação interna" pedindo ajuda por Gmail a um colaborador.'],
                                ['key' => 'b', 'text' => 'Se há investigação interna, devo mesmo manter sigilo e colaborar com o CEO.', 'correct' => false, 'feedback' => '❌ Erro grave. Investigações internas legítimas são conduzidas por comitês formais (Compliance, Auditoria, RH, Jurídico) com protocolos documentados — nunca por Gmail pessoal com pedido de sigilo.'],
                                ['key' => 'c', 'text' => 'Ligo para o CEO no celular indicado no email para confirmar.', 'correct' => false, 'feedback' => '❌ Errado. O celular indicado no email pode ser do próprio golpista. Contate o CEO pelo número que você JÁ TEM no sistema corporativo ou pelo ramal.'],
                                ['key' => 'd', 'text' => 'Consulto um colega de confiança em sigilo para ter uma segunda opinião.', 'correct' => false, 'feedback' => '❌ Parcialmente razoável, mas insuficiente. A ação correta é acionar o CISO imediatamente, não escalar informalmente.'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
