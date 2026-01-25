<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Proposta {{ $proposal->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            padding: 20px;
        }
        
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header .proposal-number {
            font-size: 14px;
            color: #6b7280;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft { background-color: #f3f4f6; color: #374151; }
        .status-sent { background-color: #dbeafe; color: #1e40af; }
        .status-approved { background-color: #dcfce7; color: #166534; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-expired { background-color: #fed7aa; color: #9a3412; }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #6b7280;
            padding: 4px 0;
            width: 30%;
        }
        
        .info-value {
            display: table-cell;
            padding: 4px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table thead {
            background-color: #f9fafb;
        }
        
        table th {
            text-align: left;
            padding: 8px;
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        
        table td {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .summary-box {
            float: right;
            width: 250px;
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .summary-total {
            border-top: 2px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .notes-box {
            background-color: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 12px;
            margin-top: 20px;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Proposta Comercial</h1>
            <div class="proposal-number">{{ $proposal->number }}</div>
            <div style="margin-top: 10px;">
                <span class="status-badge status-{{ $proposal->status }}">
                    @switch($proposal->status)
                        @case('draft') Rascunho @break
                        @case('sent') Enviada @break
                        @case('approved') Aprovada @break
                        @case('rejected') Rejeitada @break
                        @case('expired') Expirada @break
                    @endswitch
                </span>
            </div>
        </div>

        <!-- Cliente -->
        <div class="section">
            <div class="section-title">Informações do Cliente</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nome:</div>
                    <div class="info-value">{{ $proposal->customer->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Documento:</div>
                    <div class="info-value">{{ $proposal->customer->document }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $proposal->customer->email }}</div>
                </div>
                @if($proposal->customer->phone)
                <div class="info-row">
                    <div class="info-label">Telefone:</div>
                    <div class="info-value">{{ $proposal->customer->phone }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Datas -->
        <div class="section">
            <div class="section-title">Validade</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Data de Emissão:</div>
                    <div class="info-value">{{ $proposal->issue_date->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Data de Validade:</div>
                    <div class="info-value">{{ $proposal->expiration_date->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Itens -->
        <div class="section">
            <div class="section-title">Itens da Proposta</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%">Produto/Serviço</th>
                        <th style="width: 15%" class="text-right">Quantidade</th>
                        <th style="width: 15%" class="text-right">Valor Unit.</th>
                        <th style="width: 15%" class="text-right">Desconto</th>
                        <th style="width: 15%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proposal->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            @if($item->product->sku)
                                <br><small style="color: #6b7280;">SKU: {{ $item->product->sku }}</small>
                            @endif
                            @if($item->description && $item->description !== $item->product->name)
                                <br><small style="color: #6b7280;">{{ $item->description }}</small>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-right">
                            @if($item->discount_percentage > 0)
                                {{ number_format($item->discount_percentage, 2, ',', '.') }}%
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right"><strong>R$ {{ number_format($item->total, 2, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumo Financeiro -->
        <div class="clearfix">
            <div class="summary-box">
                <div class="summary-row">
                    <span style="color: #6b7280;">Subtotal:</span>
                    <span>R$ {{ number_format($proposal->subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span style="color: #dc2626;">Desconto:</span>
                    <span style="color: #dc2626;">- R$ {{ number_format($proposal->discount, 2, ',', '.') }}</span>
                </div>
                <div class="summary-total">
                    <div class="summary-row" style="margin-bottom: 0;">
                        <span>TOTAL:</span>
                        <span style="color: #2563eb;">R$ {{ number_format($proposal->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observações -->
        @if($proposal->notes)
        <div class="notes-box">
            <div style="font-weight: bold; margin-bottom: 5px;">Observações:</div>
            <div style="white-space: pre-wrap;">{{ $proposal->notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>Proposta gerada em {{ now()->format('d/m/Y H:i') }}</div>
            <div>Criada por {{ $proposal->creator->name }}</div>
        </div>
    </div>
</body>
</html>
