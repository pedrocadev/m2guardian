<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 20)->unique();
            $table->string('title', 120);
            $table->text('body');
            $table->timestamps();
        });

        DB::table('platform_feedbacks')->insert([
            [
                'platform' => 'wapp',
                'title'    => 'Aprendizado do bloco WhatsApp',
                'body'     => "Você acabou de encarar tentativas de golpe pelo WhatsApp. Lembre-se:\n\n• Desconfie de urgência (\"preciso agora\", \"é rápido\")\n• Cheque o número, mesmo se o nome do contato parecer familiar — perfis podem ser clonados\n• Nunca envie códigos de verificação por chat, nem para \"suporte\"\n• Se receber um link, valide o domínio antes de clicar\n\nQuando algo parecer estranho, chame um colega ou confirme por outro canal (ligação, presencial).",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'teams',
                'title'    => 'Aprendizado do bloco Microsoft Teams',
                'body'     => "Golpes internos são cada vez mais comuns. No Teams:\n\n• A tag \"Externo\" no topo do chat é um alerta oficial — pessoa de fora da organização\n• Executivos raramente pedem dados sensíveis por chat — canal errado deve levantar suspeita\n• Anexos inesperados, mesmo de \"colegas\", podem ser comprometidos\n• Convites de reunião podem esconder phishing no link\n\nNa dúvida, ligue direto para a pessoa ou confirme pelo Slack/e-mail interno.",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'email',
                'title'    => 'Aprendizado do bloco E-mail',
                'body'     => "O e-mail continua sendo o principal vetor de ataque. Ao receber uma mensagem:\n\n• Verifique o endereço completo do remetente (não só o nome exibido)\n• Passe o mouse sobre links antes de clicar — o destino real aparece\n• Anexos inesperados (.zip, .html, .xlsm, .docx com macros) exigem cautela extra\n• Assinaturas digitais, logos e formatação profissional NÃO garantem legitimidade\n\nSe o e-mail pede ação urgente ou dados sensíveis, valide pelo canal oficial da empresa mencionada.",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'telegram',
                'title'    => 'Aprendizado do bloco Telegram',
                'body'     => "Telegram é conhecido pela facilidade de criar perfis anônimos e grupos. Fique atento:\n\n• Qualquer pessoa pode criar um perfil com nome/foto de um executivo\n• Bots automáticos frequentemente enviam links maliciosos disfarçados de \"promoções\" ou \"prêmios\"\n• Grupos de \"investimentos\" e \"oportunidades\" costumam ser esquemas\n• Nunca compartilhe códigos de acesso ou dados bancários por chat\n\nSe alguém alegar ser de uma empresa que você conhece, confirme pelo canal oficial dela.",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'slack',
                'title'    => 'Aprendizado do bloco Slack',
                'body'     => "No Slack, os golpes tendem a explorar a confiança do ambiente corporativo:\n\n• Convites de \"guest\" externos podem imitar colegas ou parceiros\n• DMs (mensagens diretas) de contas comprometidas espalham malware\n• Links encurtados ou anexos suspeitos merecem atenção redobrada\n• Comandos slash de bots desconhecidos podem executar ações não autorizadas\n\nQuando algo parecer fora do padrão do time, valide pelo canal público antes de agir.",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_feedbacks');
    }
};
