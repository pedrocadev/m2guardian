<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_feedbacks', function (Blueprint $table) {
            $table->string('guardian_image', 255)->nullable()->after('title');
            $table->json('slides')->nullable()->after('body');
        });

        // Repopula com o roteiro do docx "Card de encerramento" (5 slides por plataforma).
        // WhatsApp, Teams e E-mail seguem o texto oficial fornecido pelo Pedro;
        // Telegram e Slack recebem uma versao adaptada com a mesma estrutura de 5 slides
        // (Intro / Sinais / Regra de ouro / Resumo / Fecho) — editaveis via admin depois.

        $data = [
            'wapp' => [
                'title' => 'Mensagem do Guardião — Missão concluída',
                'slides' => [
                    [
                        'title' => 'Missão concluída no WhatsApp',
                        'body'  => '<p>Você passou por diferentes situações de <strong>engenharia social</strong> em mensagens.</p><p>Em todas elas, a estratégia foi semelhante: usar <strong>autoridade, urgência e informações convincentes</strong> para impulsionar uma decisão rápida.</p>',
                    ],
                    [
                        'title' => 'Sinais de alerta que se repetiram',
                        'body'  => '<ul><li>Mensagens enviadas por <strong>números não salvos</strong>.</li><li>Uso de nomes, cargos, fotos ou informações convincentes para gerar confiança.</li><li>Pedidos relacionados a <strong>dinheiro, dados bancários, dados pessoais ou acesso ao computador</strong>.</li><li>Urgência, prazos curtos e contagem regressiva.</li><li>Exigência de sigilo ou tentativa de pular o processo normal.</li><li>Envio de <strong>links externos, anexos suspeitos</strong> ou programas para instalação.</li><li>Pedidos de <strong>senhas, códigos de autenticação</strong> ou aprovação de transações.</li></ul>',
                    ],
                    [
                        'title' => 'Regra de ouro da jornada',
                        'body'  => '<p>Sempre que receber, por mensagem, um pedido que envolva <strong>dinheiro, acesso a sistemas ou dados da organização</strong>:</p><blockquote><strong>Pare</strong> — Valide por um canal oficial — Só então siga o processo — Registre — Comunique.</blockquote><p>Isso vale mesmo quando a mensagem parece vir de alguém conhecido, quando o pedido é bem explicado ou quando há pressão por rapidez. Nenhum cargo, logotipo ou argumento substitui a validação em um canal oficial.</p>',
                    ],
                    [
                        'title' => 'Resumo',
                        'body'  => '<ul><li><strong>Nunca</strong> use o mesmo canal que fez o contato para validar.</li><li><strong>Nunca</strong> clique em links ou abra anexos enviados por números não salvos antes de validar.</li><li><strong>Nunca</strong> compartilhe senhas, códigos, dados bancários ou pessoais fora dos canais oficiais.</li><li><strong>Sempre</strong> confirme pedidos financeiros, alterações de dados e acessos com um contato oficial já conhecido.</li><li><strong>Sempre</strong> registre a tentativa e comunique o responsável interno ou o canal definido para tratar incidentes.</li></ul>',
                    ],
                    [
                        'title' => 'Mensagem final do Guardião',
                        'body'  => '<p>Os golpes podem mudar de roteiro, mas o padrão é o mesmo: <strong>autoridade, urgência, segredo, links e pedidos de informações sensíveis</strong>.</p><p>Se você <em>pausar, validar por um canal oficial e comunicar a organização</em>, terá o melhor escudo contra a engenharia social no dia a dia.</p>',
                    ],
                ],
            ],
            'teams' => [
                'title' => 'Mensagem do Guardião — Missão concluída',
                'slides' => [
                    [
                        'title' => 'Missão concluída no Microsoft Teams',
                        'body'  => '<p>Nesta jornada pelo <strong>Microsoft Teams</strong>, você passou por diferentes situações.</p><p>Em todos os casos, o ataque se apoiou no mesmo padrão: uso do <strong>ambiente corporativo</strong>, <strong>pressão de tempo</strong> e <strong>aparência de legitimidade</strong> para tentar fazer você pular as etapas oficiais de validação.</p>',
                    ],
                    [
                        'title' => 'Sinais de alerta corporativos',
                        'body'  => '<ul><li>Mensagens de contas <strong>externas</strong> ao domínio da organização ou perfis solicitados sem validação prévia.</li><li>Pedidos fora do padrão recebidos a partir de perfis internos, como liderança ou Compliance.</li><li>Solicitações envolvendo <strong>transferências financeiras, compartilhamento de tela, upload de documentos</strong>, bases de colaboradores ou credenciais.</li><li>Urgência, prazos curtos e risco alegado de não conformidade.</li><li>Links, pastas ou repositórios que <strong>não pertencem</strong> aos canais oficiais aprovados.</li><li>Pedidos de aprovação em aplicativos de autenticação que <strong>você não iniciou</strong>.</li></ul>',
                    ],
                    [
                        'title' => 'Regra de ouro da jornada',
                        'body'  => '<p>Sempre que no Teams um pedido envolver <strong>dinheiro, acesso a sistemas ou compartilhamento de documentos e dados da organização</strong>:</p><blockquote><strong>Pare</strong> — Valide por um canal oficial — Siga o processo autorizado — Registre — Comunique.</blockquote><p>Estar em uma plataforma corporativa, usar um e-mail corporativo ou mencionar projetos reais <strong>não substitui</strong> a validação independente.</p>',
                    ],
                    [
                        'title' => 'Resumo',
                        'body'  => '<ul><li>Não confie apenas no nome, foto, cargo ou e-mail exibido na conversa — mesmo em contas internas.</li><li>Ao interagir com contas externas ou convidadas, <strong>refaça a validação</strong> antes de atender chamadas, compartilhar tela ou enviar arquivos.</li><li><strong>Nunca</strong> compartilhe credenciais ou aprove uma autenticação que você não iniciou.</li><li>Não faça pagamentos, altere dados sensíveis ou envie bases sem confirmar pelos canais formais.</li><li>Use apenas configurações e permissões aprovadas pela organização.</li><li><strong>Preserve provas</strong> e comunique imediatamente aos responsáveis.</li></ul>',
                    ],
                    [
                        'title' => 'Mensagem final do Guardião',
                        'body'  => '<p>No comunicador corporativo, o golpe pode parecer apenas mais uma conversa de trabalho: um pedido de liderança, um alerta de segurança, uma solicitação do jurídico, de Compliance ou de uma consultoria.</p><p><strong>Pausar, validar</strong> se a pessoa é realmente quem aparenta e se a solicitação é legítima são armas importantes para se proteger.</p>',
                    ],
                ],
            ],
            'email' => [
                'title' => 'Mensagem do Guardião — Missão concluída',
                'slides' => [
                    [
                        'title' => 'Missão concluída no E-mail',
                        'body'  => '<p>Na fase de <strong>e-mail</strong>, você encontrou diferentes situações em mensagens internas e externas ao ambiente da empresa.</p><p>Em todos os casos, o ataque explorou <strong>domínios enganosos, urgência e senso de autoridade</strong> para forçar você a agir sem validar.</p>',
                    ],
                    [
                        'title' => 'Sinais de alerta que se repetiram',
                        'body'  => '<ul><li>Endereços que parecem oficiais mas trazem <strong>domínios diferentes</strong> do verdadeiro.</li><li>Links que usam o nome do banco, fornecedor ou serviço dentro de um endereço que <strong>não pertence ao site oficial</strong>.</li><li>Pedidos de senha, token, dados bancários, chaves PIX, Gift Cards ou alteração de cadastro.</li><li>Mensagens com <strong>urgência, últimos avisos</strong>, risco de bloqueio, multas ou encargos.</li><li>Uso de termos técnicos e assinaturas convincentes para simular legitimidade.</li><li>Boas-vindas de contas pessoais ou domínios externos solicitando sigilo e afastando você dos processos da empresa.</li></ul>',
                    ],
                    [
                        'title' => 'Regra de ouro da jornada',
                        'body'  => '<p>Sempre que receber, por e-mail, um pedido envolvendo <strong>contas bancárias, alteração de dados financeiros ou pagamentos em nome da empresa</strong>:</p><blockquote><strong>Não clique</strong> em links da mensagem — Valide por canais oficiais — Siga o processo interno — Registre — Comunique.</blockquote><p>O fato de o e-mail mencionar dados reais, usar logotipos ou parecer urgente <strong>não substitui</strong> uma verificação independente.</p>',
                    ],
                    [
                        'title' => 'Resumo',
                        'body'  => '<ul><li>Leia o <strong>endereço completo</strong> do remetente, até o final do domínio, e compare com os domínios oficiais já conhecidos.</li><li><strong>Nunca</strong> acesse banco, sistemas de pagamento ou portais de fornecedores por links recebidos em e-mails inesperados.</li><li>Não informe senhas, tokens, dados bancários, chaves PIX ou credenciais em resposta a e-mails.</li><li>Nunca faça alterações de cadastro ou comprem Gift Cards com recursos próprios sem validação formal.</li><li><strong>Desconfie</strong> da combinação de autoridade, urgência, promessa de reembolso e sigilo.</li><li>Preserve o e-mail suspeito e comunique os responsáveis internos.</li></ul>',
                    ],
                    [
                        'title' => 'Mensagem final do Guardião',
                        'body'  => '<p>No e-mail, o golpe se veste de comunicado oficial: alerta de banco, mensagem de fornecedor, pedido do CEO, assinatura bonita e logotipo bem colocado.</p><p>Se você <em>olhar o domínio até o fim, evitar clicar em links suspeitos e validar pelos canais oficiais</em>, transforma o e-mail em ferramenta de trabalho seguro — e não em porta de entrada para fraudes.</p>',
                    ],
                ],
            ],
            'telegram' => [
                'title' => 'Mensagem do Guardião — Missão concluída',
                'slides' => [
                    [
                        'title' => 'Missão concluída no Telegram',
                        'body'  => '<p>Você acabou de enfrentar golpes usando as características únicas do <strong>Telegram</strong>: perfis anônimos, grupos abertos, bots automatizados e a sensação de proximidade informal.</p>',
                    ],
                    [
                        'title' => 'Sinais de alerta que se repetiram',
                        'body'  => '<ul><li>Perfis com nome ou foto de <strong>executivos ou colegas</strong> — clonagem é trivial no Telegram.</li><li>Bots automáticos com "promoções", "prêmios" ou "oportunidades" com links maliciosos.</li><li>Grupos de <strong>investimentos, criptomoedas ou oportunidades</strong> exclusivas.</li><li>Pedidos de códigos de acesso, verificação de conta ou dados bancários por chat.</li><li>Urgência combinada com sigilo (\"não fale com ninguém sobre isso\").</li></ul>',
                    ],
                    [
                        'title' => 'Regra de ouro da jornada',
                        'body'  => '<blockquote><strong>Pare</strong> — Valide pelo canal oficial da empresa ou pessoa — Registre — Comunique.</blockquote><p>Nunca compartilhe códigos, senhas ou dados bancários por Telegram, mesmo quando o perfil parecer familiar. Confirme sempre por telefone ou canal corporativo.</p>',
                    ],
                    [
                        'title' => 'Resumo',
                        'body'  => '<ul><li><strong>Nunca</strong> compartilhe códigos de verificação recebidos por SMS ou app.</li><li>Desconfie de perfis que solicitam contato fora dos canais da empresa.</li><li>Ao receber links, valide o domínio antes de clicar.</li><li>Bloqueie e denuncie bots ou perfis suspeitos.</li></ul>',
                    ],
                    [
                        'title' => 'Mensagem final do Guardião',
                        'body'  => '<p>O Telegram é ferramenta útil, mas frágil quando confundido com canal oficial. <strong>Valide sempre pelo canal certo</strong> — presencial, telefone, e-mail corporativo — antes de qualquer decisão sensível.</p>',
                    ],
                ],
            ],
            'slack' => [
                'title' => 'Mensagem do Guardião — Missão concluída',
                'slides' => [
                    [
                        'title' => 'Missão concluída no Slack',
                        'body'  => '<p>No <strong>Slack</strong>, os golpes exploram a <strong>confiança do ambiente corporativo</strong>: canais familiares, colegas conhecidos e a informalidade das DMs.</p>',
                    ],
                    [
                        'title' => 'Sinais de alerta que se repetiram',
                        'body'  => '<ul><li>Convites de <strong>guest externos</strong> que imitam colegas, parceiros ou fornecedores.</li><li>DMs de contas comprometidas de colegas espalhando malware ou pedindo dados.</li><li>Links encurtados ou anexos suspeitos em canais aparentemente confiáveis.</li><li>Comandos slash de <strong>bots desconhecidos</strong> que podem executar ações não autorizadas.</li><li>Pedidos fora do padrão de perfis de liderança, RH ou financeiro.</li></ul>',
                    ],
                    [
                        'title' => 'Regra de ouro da jornada',
                        'body'  => '<blockquote><strong>Pare</strong> — Valide pelo canal público ou pessoalmente — Siga o processo — Registre — Comunique.</blockquote><p>Estar em um workspace corporativo não substitui validação. Se algo parece fora do padrão do time, confirme antes de agir.</p>',
                    ],
                    [
                        'title' => 'Resumo',
                        'body'  => '<ul><li>Confirme identidade em <strong>canal público</strong> antes de responder pedidos sensíveis via DM.</li><li>Desconfie de guests recém-adicionados com pedidos urgentes.</li><li>Não instale bots ou apps de terceiros sem revisão de TI.</li><li>Reporte imediatamente comportamento suspeito ao admin do workspace.</li></ul>',
                    ],
                    [
                        'title' => 'Mensagem final do Guardião',
                        'body'  => '<p>O Slack é o coração da comunicação diária — e por isso também um alvo. <strong>Trate DMs sensíveis com o mesmo rigor</strong> que trataria um e-mail com anexo desconhecido: valide, registre, comunique.</p>',
                    ],
                ],
            ],
        ];

        foreach ($data as $platform => $payload) {
            DB::table('platform_feedbacks')
                ->where('platform', $platform)
                ->update([
                    'title'      => $payload['title'],
                    'slides'     => json_encode($payload['slides'], JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('platform_feedbacks', function (Blueprint $table) {
            $table->dropColumn(['guardian_image', 'slides']);
        });
    }
};
