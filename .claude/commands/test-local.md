---
description: Testa funcionalmente a aplicacao M2 Guardian rodando localmente (Herd em http://m2guardian.test). Use ANTES de commitar.
---

Use o subagent **local-tester** para executar a bateria de testes funcionais contra `http://m2guardian.test`.

Após o agente terminar, mostre o relatório resumido (passou/falhou + lista de problemas). Se houve falhas:

1. **Verifica primeiro se o Herd está ligado** (clica no ícone do Herd na bandeja → status do PHP)
2. Se sim, olha o log local: `c:\Projects\m2guardian\storage\logs\laravel-YYYY-MM-DD.log`
3. **NÃO commite** o código antes de corrigir
