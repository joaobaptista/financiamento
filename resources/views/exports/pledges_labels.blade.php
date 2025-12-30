<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Etiquetas de Apoiadores - {{ $campaign->title }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f0f0;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 10px;
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .label {
            border: 1px dashed #ccc;
            padding: 15px;
            height: 65mm; /* Aproximadamente 4 por coluna em A4 */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 12px;
            overflow: hidden;
            box-sizing: border-box;
        }
        .name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .address {
            line-height: 1.4;
            flex-grow: 1;
        }
        .reward {
            font-style: italic;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 5px;
            margin-top: 5px;
            font-size: 10px;
        }
        .no-print {
            background: #333;
            color: white;
            padding: 10px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .container { width: 100%; margin: 0; padding: 0; }
            .label { border: 1px solid #eee; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        Pressione <strong>Ctrl + P</strong> para imprimir ou salvar como PDF. Configurado para 12 etiquetas por página (3x4).
        <button onclick="window.print()" style="margin-left: 20px; padding: 5px 15px; cursor: pointer;">Imprimir Agora</button>
    </div>

    <div class="container">
        @foreach($pledges as $p)
            <div class="label">
                <div>
                    <div class="name">{{ $p->user?->name }}</div>
                    <div class="address">
                        {{ $p->user?->address_street }}, {{ $p->user?->address_number }}
                        @if($p->user?->address_complement)
                            <br>{{ $p->user?->address_complement }}
                        @endif
                        <br>{{ $p->user?->address_neighborhood }}
                        <br>{{ $p->user?->address_city }} - {{ $p->user?->address_state }}
                        <br><strong>CEP: {{ $p->user?->postal_code }}</strong>
                    </div>
                </div>
                <div class="reward">
                    Recompensa: {{ $p->reward?->title ?? 'Apoio livre' }}
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
