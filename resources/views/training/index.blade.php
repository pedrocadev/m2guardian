<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treinamento — Guardião Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f5f7; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border-radius: 12px; padding: 48px 40px; text-align: center; max-width: 480px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .icon { font-size: 48px; margin-bottom: 16px; }
        .badge { display: inline-block; background: #111; color: white; font-size: 10px; font-weight: 700; letter-spacing: 1px; padding: 4px 10px; border-radius: 4px; margin-bottom: 20px; }
        .title { font-size: 22px; font-weight: 800; margin-bottom: 12px; }
        .text { color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 8px; }
        .name { font-weight: 700; color: #111; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🛡️</div>
        <div class="badge">GUARDIÃO DIGITAL</div>
        <div class="title">Bem-vindo ao Treinamento!</div>
        <p class="text">Olá, <span class="name">{{ $collaborator->name ?? $collaborator->email }}</span>!</p>
        <p class="text" style="margin-top: 12px; color: #999; font-size: 13px;">
            O módulo de treinamento estará disponível em breve.<br>
            (Fase 4 — em construção)
        </p>
    </div>
</body>
</html>
