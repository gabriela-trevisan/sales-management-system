<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        
        .info-box {
            background-color: white;
            padding: 15px;
            border-left: 4px solid #2563eb;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Proposta Comercial</h1>
        <p style="margin: 0;">{{ $proposal->number }}</p>
    </div>
    
    <div class="content">
        <p>Olá <strong>{{ $customerName }}</strong>,</p>
        
        <p>
            Segue em anexo nossa proposta comercial conforme solicitado.
        </p>
        
        <div class="info-box">
            <p style="margin: 0;"><strong>Proposta:</strong> {{ $proposal->number }}</p>
            <p style="margin: 5px 0 0 0;"><strong>Válida até:</strong> {{ $proposal->expiration_date->format('d/m/Y') }}</p>
        </div>
        
        <p>
            A proposta está anexada em formato PDF para sua análise detalhada. 
            Caso tenha alguma dúvida ou necessite de esclarecimentos adicionais, 
            estamos à disposição.
        </p>
        
        <p>
            Aguardamos seu retorno e ficamos à disposição para quaisquer esclarecimentos.
        </p>
        
        <p style="margin-top: 30px;">
            Atenciosamente,<br>
            <strong>{{ $proposal->creator->name }}</strong><br>
            {{ config('app.name') }}
        </p>
    </div>
    
    <div class="footer">
        <p>
            Este é um email automático. Por favor, não responda diretamente a esta mensagem.
        </p>
        <p style="margin-top: 10px;">
            © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
        </p>
    </div>
</body>
</html>
