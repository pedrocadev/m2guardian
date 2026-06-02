---
description: Revisao automatica de clean code no diff atual (ou em arquivos especificos). Procura duplicacao, codigo morto, redundancia, comentarios obsoletos e gotchas do projeto. Reporta sem modificar.
---

Use o subagent **clean-code-reviewer** para analisar o diff atual no projeto.

Se eu (Pedro) passei argumentos com caminhos específicos, encaminhe os caminhos para o agente. Senão, ele analisa o `git status` + `git diff` automaticamente.

Quando o agente terminar:

1. Mostre o relatório dele aqui
2. Pergunte **se eu quero que você aplique as correções** (sim/não/só as críticas)
3. Se eu disser sim, faça os edits — mas **NÃO commite** (Pedro testa antes)
