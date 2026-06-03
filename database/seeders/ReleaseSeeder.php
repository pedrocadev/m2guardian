<?php

namespace Database\Seeders;

use App\Models\Release;
use Illuminate\Database\Seeder;

class ReleaseSeeder extends Seeder
{
    public function run(): void
    {
        Release::firstOrCreate(
            ['title' => 'Cadastro de empresa com CNPJ e líder principal'],
            [
                'released_at' => '2026-06-03',
                'published'   => true,
                'content'     => <<<MD
### O que mudou

- **CNPJ obrigatório** no cadastro de empresa, com validação automática e busca da razão social via Receita Federal (BrasilAPI)
- Novo campo **"Apelido"** pra referência interna curta (ex: "M2", "ACME")
- **Empresa só nasce com líder** atrelado — não é mais possível cadastrar empresa órfã
- **Líder principal** (cadastrado junto com a empresa) tem proteções fortes:
  - Não pode ser arquivado
  - Empresa e nome não podem ser alterados
- **Arquivar substitui Excluir** — empresas e líderes nunca são apagados de verdade; dados preservados
- **Empresa nunca fica sem líder** — bloqueio automático ao arquivar o último
- **Sidebar reorganizada** — Colaboradores agora vem antes de Cenários

### Para você

- Use o filtro **"Arquivadas"** pra ver empresas/líderes desativados
- Desarquive a qualquer momento clicando em **"Desarquivar"**
- Se precisar criar empresa com CNPJ já existente (mesmo arquivado), o sistema avisa

Boa, **{nome}**! Continue protegendo sua equipe.
MD,
            ]
        );
    }
}
