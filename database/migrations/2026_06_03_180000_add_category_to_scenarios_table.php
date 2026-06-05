<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->string('category', 60)->nullable()->after('platform');
            $table->index('category');
        });

        $map = [
            'ceo-wapp'         => 'solicitacoes_urgentes',
            'fornecedor-wapp'  => 'atencao_remetentes',
            'suporte-wapp'     => 'compartilhamento_informacoes',
            'banco-wapp'       => 'cuidado_senhas',
            'rh-wapp'          => 'atencao_remetentes',
            'parceiro-wapp'    => 'anexos_downloads',
            'ceo-teams'        => 'solicitacoes_urgentes',
            'ti-teams'         => 'anexos_downloads',
            'juridico-teams'   => 'solicitacoes_urgentes',
            'compliance-teams' => 'cuidado_senhas',
            'spear-phishing'   => 'validacao_links',
            'invoice-fraud'    => 'atencao_remetentes',
            'exec-compromise'  => 'atencao_remetentes',
        ];

        foreach ($map as $slug => $category) {
            DB::table('scenarios')->where('slug', $slug)->update(['category' => $category]);
        }
    }

    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};
