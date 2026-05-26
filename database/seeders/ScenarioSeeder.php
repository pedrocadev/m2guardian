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
            [
                'slug' => 'ceo-wapp',
                'platform' => 'wapp',
                'label' => 'Diretoria Executiva',
                'avatar' => '👨‍💼',
                'bg_color' => '#1e3a8a',
                'preview' => 'CEO solicita transferência urgente via WhatsApp',
                'intro' => 'Você recebe uma mensagem no WhatsApp do número desconhecido alegando ser o CEO da empresa.',
                'is_default' => true,
                'demo_eligible' => true,
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Oi, é o Carlos, CEO. Preciso da sua ajuda em uma operação sigilosa.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Estou em reunião e não posso falar agora. Preciso que você faça uma transferência de R$ 87.450 para um fornecedor estratégico.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Vou te enviar os dados bancários. É urgente. Não comente com ninguém.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Como você responderia a essa solicitação?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Confirmar pessoalmente com o CEO por outro canal (Teams/telefone interno) antes de fazer qualquer ação', 'correct' => true, 'feedback' => 'Correto! Solicitações financeiras urgentes por canais não-oficiais são o padrão de fraude BEC (Business Email Compromise). Sempre confirme por canal alternativo.'],
                                ['key' => 'b', 'text' => 'Pedir os dados bancários e executar a transferência rapidamente', 'correct' => false, 'feedback' => 'Erro grave. Você acabou de cair em uma fraude BEC. A urgência e o pedido de sigilo são bandeiras vermelhas clássicas.'],
                                ['key' => 'c', 'text' => 'Responder pedindo o CPF do CEO para confirmar identidade', 'correct' => false, 'feedback' => 'Insuficiente. Dados pessoais podem ser obtidos com engenharia social. Verifique sempre por canal oficial conhecido.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou confirmar pelo Teams primeiro. Aguarde.'],
                    ],
                ],
            ],
            [
                'slug' => 'it-teams',
                'platform' => 'teams',
                'label' => 'Suporte de TI',
                'avatar' => '💻',
                'bg_color' => '#0078d4',
                'preview' => 'Falso suporte de TI pede senha para "verificação urgente"',
                'intro' => 'Você recebe uma mensagem no Teams de alguém dizendo ser do suporte de TI.',
                'is_default' => true,
                'demo_eligible' => true,
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Olá! Sou da equipe de TI. Detectamos atividade suspeita na sua conta.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Para evitar o bloqueio nas próximas 2 horas, preciso confirmar sua senha atual para validar.'],
                        [
                            'type' => 'question',
                            'prompt' => 'Qual é a melhor resposta?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Compartilhar a senha para evitar o bloqueio', 'correct' => false, 'feedback' => 'Erro crítico. Nenhum suporte de TI legítimo jamais pede senha. Esse é um ataque de phishing interno.'],
                                ['key' => 'b', 'text' => 'Recusar e reportar ao canal oficial de TI/Segurança', 'correct' => true, 'feedback' => 'Correto! TI nunca pede senha. Reporte imediatamente ao canal oficial — pode ser uma conta comprometida sendo usada para movimentação lateral.'],
                                ['key' => 'c', 'text' => 'Trocar a senha primeiro e depois enviar a nova', 'correct' => false, 'feedback' => 'Ainda errado. O problema não é a senha — é o pedido em si. Nunca envie senha por nenhum canal.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou reportar isso ao canal oficial de segurança. Por favor, abra um chamado formal.'],
                    ],
                ],
            ],
            [
                'slug' => 'invoice-fraud',
                'platform' => 'email',
                'label' => 'Fatura Fraudulenta',
                'avatar' => '📧',
                'bg_color' => '#dc2626',
                'preview' => 'Email com fatura falsa de fornecedor conhecido',
                'intro' => 'Você recebe um email de um fornecedor regular com uma fatura em anexo e novos dados bancários.',
                'is_default' => true,
                'demo_eligible' => true,
                'version' => 1,
                'status' => 'active',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'from' => 'them', 'body' => 'Prezado, segue em anexo a fatura referente ao mês corrente.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Atenção: alteramos nossos dados bancários. Por favor, atualize seu cadastro e efetue o pagamento na nova conta indicada no boleto.'],
                        ['type' => 'text', 'from' => 'them', 'body' => 'Anexo: fatura_outubro.pdf'],
                        [
                            'type' => 'question',
                            'prompt' => 'Como proceder antes de pagar?',
                            'options' => [
                                ['key' => 'a', 'text' => 'Pagar imediatamente nos novos dados para evitar atraso', 'correct' => false, 'feedback' => 'Erro grave. Mudança de dados bancários é o vetor #1 de fraude. Sempre confirme por telefone (número que você já tem cadastrado).'],
                                ['key' => 'b', 'text' => 'Ligar para o fornecedor usando o número que já está no seu cadastro (não o do email) para confirmar a alteração', 'correct' => true, 'feedback' => 'Correto! Validação por canal independente (out-of-band) é o padrão para confirmar mudanças bancárias. Nunca use contatos do próprio email suspeito.'],
                                ['key' => 'c', 'text' => 'Responder o email pedindo confirmação dos novos dados', 'correct' => false, 'feedback' => 'Insuficiente. Se o email foi comprometido, o atacante vai confirmar. Use canal independente — telefone, visita presencial, ou conta verificada.'],
                            ],
                        ],
                        ['type' => 'text', 'from' => 'me', 'body' => 'Vou ligar no telefone cadastrado para confirmar a alteração antes de qualquer pagamento.'],
                    ],
                ],
            ],
        ];
    }
}
