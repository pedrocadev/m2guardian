# Banco de imagens — Guardião Digital

Esta pasta é o **único lugar onde imagens do site são armazenadas**. **Cada referência no código tem sua própria cópia da imagem** — isso permite trocar a imagem de UM lugar específico sem afetar outros usos.

## 📁 Estrutura

```
public/images/
├── brand/                       ← logo único da marca
│   └── logo.png
├── mascots/                     ← um mascote por contexto de uso
│   ├── login-admin.png
│   ├── login-leader.png
│   ├── training-welcome-greeting.png
│   ├── training-welcome-explain.png
│   ├── training-welcome-guardian.png
│   ├── training-index-start.png
│   ├── training-index-progress.png
│   ├── training-index-done.png
│   ├── training-show-greeting.png
│   ├── training-show-sidebar.png
│   ├── training-show-correct.png
│   ├── training-show-wrong.png
│   ├── training-transition-wapp.png
│   ├── training-transition-teams.png
│   ├── training-transition-email.png
│   ├── training-transition-fallback.png
│   ├── completion-n1.png
│   ├── completion-n2.png
│   ├── completion-n3.png
│   ├── completion-n4.png
│   └── completion-n5.png
└── backgrounds/                 ← um background por contexto
    ├── admin-bg.jpg
    ├── login-leader.jpg
    ├── training-welcome.jpg
    ├── training-index.jpg
    ├── training-show.jpg
    ├── training-transition.jpg
    └── training-completed.jpg
```

## 🔄 Como trocar uma imagem (sem mexer em código)

**Cada arquivo é usado em UM único lugar.** Pra trocar uma imagem específica:

1. Identifica o nome do arquivo correspondente ao contexto (veja a tabela abaixo)
2. Renomeia sua imagem nova com o mesmo nome do arquivo
3. Sobrescreve em `public/images/{subpasta}/{nome}.{ext}`

A imagem aparece automaticamente no contexto certo, **sem afetar outros lugares**.

### Exemplos práticos

**Quero trocar o mascote da tela de login do admin** (e SÓ do admin):
- Sobrescrever `public/images/mascots/login-admin.png`
- O mascote da transição WhatsApp (que era a mesma imagem antes) **não é afetado**

**Quero trocar o background da tela de welcome** (e SÓ dela):
- Sobrescrever `public/images/backgrounds/training-welcome.jpg`
- Todos os outros backgrounds continuam com a imagem original

## 📍 Mapa completo de uso

### Mascotes

| Arquivo | Onde aparece |
|---------|--------------|
| `login-admin.png` | Tela de login do admin (mascote de corpo inteiro, sem moldura) |
| `login-leader.png` | Tela de login do líder (mascote de corpo inteiro, sem moldura) |
| `training-welcome-guardian.png` | Welcome — mascote principal (mascote positivo, joinha) |
| `training-welcome-greeting.png` | Tour do líder — passo 1 (mascote pensativo) |
| `training-welcome-explain.png` | Tour do líder — passo 2 (mascote pensativo) |
| `training-index-start.png` | Index — não começou (mascote correndo) |
| `training-index-progress.png` | Index — em progresso (mascote pensativo) |
| `training-index-done.png` | Index — concluiu tudo (mascote comemorando) |
| `training-show-greeting.png` | Show — saudação inicial (mascote pensativo, analítico) |
| `training-show-sidebar.png` | Show — mascote lateral fixo (mascote positivo, joinha) |
| `training-show-correct.png` | Show — feedback de acerto (mascote comemorando) |
| `training-show-wrong.png` | Show — feedback de erro (mascote triste) |
| `training-transition-wapp.png` | Transição — cenário WhatsApp (mascote correndo) |
| `training-transition-teams.png` | Transição — cenário Microsoft Teams (mascote correndo) |
| `training-transition-email.png` | Transição — cenário E-mail (mascote correndo) |
| `training-transition-fallback.png` | Transição — fallback (mascote correndo) |
| `completion-n1.png` | Conclusão — Nível 1 (Aprendiz Iniciante, mascote pensativo) |
| `completion-n2.png` | Conclusão — Nível 2 (Aprendiz Guardião, mascote correndo) |
| `completion-n3.png` | Conclusão — Nível 3 (Guardião Atento, mascote positivo) |
| `completion-n4.png` | Conclusão — Nível 4 (Guardião Estratégico, mascote comemorando) |
| `completion-n5.png` | Conclusão — Nível 5 (Guardião Certificado, mascote comemorando) |

### Backgrounds

| Arquivo | Onde aparece |
|---------|--------------|
| `admin-bg.jpg` | Pano de fundo do painel admin (Filament `simple-layout`) |
| `login-leader.jpg` | Pano de fundo do lado esquerdo da tela de login do líder |
| `training-welcome.jpg` | Tela welcome do colaborador |
| `training-index.jpg` | Tela index de cenários |
| `training-show.jpg` | Tela do chat durante o cenário |
| `training-transition.jpg` | Tela de transição entre cenários |
| `training-completed.jpg` | Tela de conclusão final |

### Brand

| Arquivo | Onde aparece |
|---------|--------------|
| `logo.png` | Logo unificado — usado em 4 telas de training |

## 🎨 Padrões técnicos

- **Mascotes:** PNG transparente, ~512×512 ou maior
- **Backgrounds:** JPG (peso menor), ≥1920px de largura
- **Logo:** PNG transparente
- **Nomenclatura:** `{contexto}-{descrição}.{ext}` em kebab-case

## ➕ Adicionando um novo uso de imagem

Quando criar uma nova tela:
1. Defina o nome contextual (ex: `training-quiz-intro.png`)
2. Coloque o arquivo na subpasta apropriada
3. Use no Blade: `<img src="/images/mascots/training-quiz-intro.png">` ou `{{ asset('...') }}`
