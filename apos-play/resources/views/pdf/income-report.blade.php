<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; background: #fff; }

        .header { padding: 16px 20px 12px; border-bottom: 2px solid #4f46e5; }
        .header h1 { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .header .meta { margin-top: 4px; font-size: 9px; color: #666; }

        .summary { display: table; width: 100%; padding: 12px 20px; border-bottom: 1px solid #e5e7eb; }
        .summary-cell { display: table-cell; width: 25%; padding-right: 16px; }
        .summary-label { font-size: 8px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; }
        .summary-value { font-size: 14px; font-weight: bold; margin-top: 2px; }
        .green  { color: #16a34a; }
        .red    { color: #dc2626; }
        .indigo { color: #4f46e5; }
        .gray   { color: #374151; }

        .table-wrap { padding: 12px 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead tr { background-color: #4f46e5; color: #fff; }
        thead th { padding: 6px 8px; text-align: left; font-weight: 600; font-size: 8px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        tbody tr:nth-child(odd)  { background-color: #ffffff; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 9999px; font-size: 8px; font-weight: 600; }
        .badge-income  { background: #dcfce7; color: #15803d; }
        .badge-refund  { background: #fee2e2; color: #dc2626; }
        .badge-partial { background: #fef9c3; color: #b45309; }

        .footer { padding: 8px 20px; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>

<div class="header">
    <h1>Reporte de Ingresos — AposPlay</h1>
    <div class="meta">
        Período:
        {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}
        al
        {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
        &nbsp;|&nbsp;
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<div class="summary">
    <div class="summary-cell">
        <div class="summary-label">Registros</div>
        <div class="summary-value gray">{{ $reservations->count() }}</div>
    </div>
    <div class="summary-cell">
        <div class="summary-label">Ingresos brutos</div>
        <div class="summary-value green">${{ number_format($totalIncome, 2, ',', '.') }}</div>
    </div>
    <div class="summary-cell">
        <div class="summary-label">Reembolsos</div>
        <div class="summary-value red">${{ number_format($totalRefunds, 2, ',', '.') }}</div>
    </div>
    <div class="summary-cell">
        <div class="summary-label">Ingreso neto</div>
        <div class="summary-value indigo">${{ number_format($totalIncome - $totalRefunds, 2, ',', '.') }}</div>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cancha</th>
                <th>Usuario</th>
                <th>Precio Total</th>
                <th>Descuento</th>
                <th>Monto Pagado</th>
                <th>Estado Pago</th>
                <th>ID Pago MP</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $r)
                @php
                    $tipo = $getTipo($r);
                    $badgeClass = match($r->payment_status) {
                        'refunded'         => 'badge-refund',
                        'partial_refunded' => 'badge-partial',
                        default            => 'badge-income',
                    };
                @endphp
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->reservation_date->format('d/m/Y') }}</td>
                    <td>{{ $r->court?->name ?? '-' }}</td>
                    <td>{{ $r->user?->name ?? '-' }}</td>
                    <td>${{ number_format((float)$r->total_price, 2, ',', '.') }}</td>
                    <td>${{ number_format((float)($r->discount_amount ?? 0), 2, ',', '.') }}</td>
                    <td>${{ number_format((float)$r->amount_paid, 2, ',', '.') }}</td>
                    <td>{{ $r->payment_status ?? '-' }}</td>
                    <td style="font-size:8px;">{{ $r->payment_id ?? '-' }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $tipo }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding: 20px; color: #9ca3af;">
                        Sin registros para el período seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="footer">
    AposPlay &mdash; Reporte generado automáticamente. No requiere firma.
</div>

</body>
</html>
