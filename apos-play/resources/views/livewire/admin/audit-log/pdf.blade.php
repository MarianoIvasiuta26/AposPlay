<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Auditoría — AposPlay</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 20px;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        .meta {
            font-size: 9px;
            color: #666;
            margin-bottom: 15px;
        }
        .filters {
            background: #f5f5f5;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9px;
        }
        .filters span {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #374151;
            color: white;
            text-align: left;
            padding: 6px 8px;
            font-size: 9px;
            text-transform: uppercase;
        }
        td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f3f4f6; color: #374151; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }
        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
        }
        .count {
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Reporte de Auditoría — AposPlay</h1>
    <div class="meta">
        Generado el {{ $generatedAt }} por {{ $generatedBy }}
    </div>

    @if(count($filters) > 0)
        <div class="filters">
            Filtros aplicados:
            @foreach($filters as $key => $value)
                <span>{{ $key }}:</span> {{ $value }}@if(!$loop->last) | @endif
            @endforeach
        </div>
    @endif

    <div class="count">
        Total de registros: {{ $logs->count() }}@if($logs->count() >= 1000) (limitado a 1000)@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha y hora</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Modelo</th>
                <th>Detalle</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                    <td>
                        <span class="badge badge-{{ $log->action->color() }}">
                            {{ $log->action->label() }}
                        </span>
                    </td>
                    <td>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($log->description, 80) }}</td>
                    <td>{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        AposPlay — Reporte de Auditoría — Página {PAGE_NUM} de {PAGE_COUNT}
    </div>
</body>
</html>
