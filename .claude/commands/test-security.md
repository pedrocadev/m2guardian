---
description: Roda bateria de testes de seguranca defensivos contra a producao do M2 Guardian (TLS, headers, exposicao, rate limit, injection). Use periodicamente ou apos mudancas em auth/seguranca.
---

Use o subagent **security-tester** para executar a bateria completa de testes de segurança contra `https://guardiao.m2cloud.com.br`.

Após o agente terminar, mostre o relatório dele aqui de forma resumida (apenas o score geral + lista de achados ordenados por severidade). Se houver achado **CRÍTICO** (🔴), destaque com alerta visual e recomende ação imediata.

Lembrete: este é um teste **defensivo e não-invasivo**. Não estamos atacando — estamos validando que nossas defesas funcionam.
