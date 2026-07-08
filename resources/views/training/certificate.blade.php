<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Certificado — Guardião Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, Arial, sans-serif;
            min-height: 100vh;
            background: #111;
            color: #eee;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 20px 40px;
        }

        .topbar {
            width: 100%;
            max-width: 1200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .topbar h1 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .topbar-actions { display: flex; gap: 10px; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            transition: filter 0.15s, transform 0.05s;
        }
        .btn:active { transform: scale(0.98); }
        .btn-print {
            background: #CC0000;
            color: #fff;
        }
        .btn-print:hover { filter: brightness(1.1); }
        .btn-linkedin {
            background: #0a66c2;
            color: #fff;
        }
        .btn-linkedin:hover { filter: brightness(1.1); }
        .btn-linkedin svg { width: 15px; height: 15px; }
        .btn-back {
            background: #333;
            color: #eee;
        }
        .btn-back:hover { background: #444; }

        /* ---------- Certificado ---------- */
        .cert-frame {
            width: 100%;
            max-width: 1200px;
            aspect-ratio: 2000 / 1414;
            position: relative;
            background: #000;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border-radius: 6px;
            overflow: hidden;
        }
        .cert-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Coordenadas .cert-name e .cert-date calibradas para o PNG
           /images/Certificado/Certificado Guardião.png (2000x1414).
           Se o PNG for trocado por um layout diferente, recalibrar top/left/width. */
        .cert-name {
            position: absolute;
            top: 45.5%;
            left: 15%;
            right: 40%;
            text-align: center;
            color: #fff;
            font-family: 'Great Vibes', 'Segoe Script', cursive;
            font-size: clamp(28px, 5.2vw, 62px);
            line-height: 1.05;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 12px rgba(204, 0, 0, 0.35);
            pointer-events: none;
        }

        /* Data de emissão — centralizada na linha branca dentro da caixa, abaixo do label */
        .cert-date {
            position: absolute;
            top: 86%;
            left: 12%;
            width: 20%;
            text-align: center;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: clamp(11px, 1.3vw, 17px);
            font-weight: 600;
            letter-spacing: 0.4px;
            pointer-events: none;
        }

        @media print {
            @page { size: A4 landscape; margin: 0; }
            body { background: #fff !important; padding: 0 !important; }
            .topbar { display: none !important; }
            .cert-frame {
                max-width: 100% !important;
                width: 100% !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

@php
    $issuedAt = $session->certificate_issued_at;
    // CERTIFICATION_NAME é o task id literal da LinkedIn "Add to Profile" API — não é placeholder.
    $linkedinParams = http_build_query([
        'startTask'        => 'CERTIFICATION_NAME',
        'name'             => 'Certificado de Postura Digital — Guardião Digital',
        'organizationName' => 'M2 Cloud & Security',
        'issueYear'        => $issuedAt->year,
        'issueMonth'       => $issuedAt->month,
    ]);
    $linkedinUrl = 'https://www.linkedin.com/profile/add?' . $linkedinParams;
@endphp

<div class="topbar">
    <h1>🎓 Seu Certificado Guardião Digital</h1>
    <div class="topbar-actions">
        <a href="{{ route('training.completed') }}" class="btn btn-back">← Voltar</a>
        <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener" class="btn btn-linkedin">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
            Adicionar ao LinkedIn
        </a>
        <button type="button" class="btn btn-print" onclick="window.print()">
            🖨️ Imprimir / Salvar PDF
        </button>
    </div>
</div>

<div class="cert-frame">
    <img
        src="{{ asset('images/Certificado/' . rawurlencode('Certificado Guardião.png')) }}"
        alt="Certificado Guardião Digital"
        class="cert-image"
    >
    <div class="cert-name">{{ $session->certificate_name }}</div>
    <div class="cert-date">{{ $issuedAt->format('d/m/Y') }}</div>
</div>

</body>
</html>
