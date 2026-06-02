---
description: Verifica saude do banco de dados MariaDB em producao. Gera script para rodar via PuTTY e analisa o resultado.
---

Use o subagent **db-health-checker** para gerar o script completo de health check do banco em produção.

Como o acesso ao DB é via PuTTY (o agente não consegue SSH direto), o fluxo é:

1. Agente gera o script bash
2. **Você** cola no PuTTY (já logado como root via `sudo -i`)
3. Cola o output de volta pra mim
4. Eu analiso e gero o relatório de saúde

Periodicidade recomendada: **semanal** ou após mudanças estruturais (migrations novas).
