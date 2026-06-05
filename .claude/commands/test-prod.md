---
description: Testa funcionalmente a aplicacao M2 Guardian em producao (smoke tests via HTTP). Use apos cada deploy.
---

Use o subagent **production-tester** para executar a bateria completa de testes funcionais contra `https://m2guardiao.com.br`.

Após o agente terminar, mostre o relatório dele aqui de forma resumida (escreva apenas o veredito principal + lista de falhas, se houver). Se houve falhas críticas, recomende rollback do último deploy via:

```bash
ssh ubuntu@137.131.186.168
sudo -i
cd /var/www/m2guardian
sudo -u m2guardian git log --oneline -5    # vê os últimos commits
sudo -u m2guardian git reset --hard HEAD~1 # volta 1 commit
sudo bash deploy/03-deploy-app.sh           # re-deploy do commit anterior
```
