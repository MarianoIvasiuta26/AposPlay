<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; }
        .header { background: #16a34a; padding: 28px 32px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 20px; }
        .body { padding: 32px; }
        .body h2 { font-size: 16px; color: #111827; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { text-align: left; background: #f9fafb; color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 14px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px 14px; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #374151; }
        td:first-child { font-weight: 600; color: #111827; width: 40%; }
        .footer { background: #f9fafb; padding: 18px 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Nueva solicitud de cuenta Owner — AposPlay</h1>
        </div>
        <div class="body">
            <h2>Datos del complejo</h2>
            <table>
                <tr><th>Campo</th><th>Detalle</th></tr>
                <tr><td>Nombre</td><td>{{ $complexName }}</td></tr>
                <tr><td>Ciudad</td><td>{{ $complexCity }}</td></tr>
                <tr><td>Dirección</td><td>{{ $complexAddress }}</td></tr>
                <tr><td>Canchas</td><td>{{ $complexCourts }}</td></tr>
            </table>

            <h2>Datos del responsable</h2>
            <table>
                <tr><th>Campo</th><th>Detalle</th></tr>
                <tr><td>Nombre</td><td>{{ $contactName }}</td></tr>
                <tr><td>Email</td><td><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></td></tr>
                <tr><td>Teléfono</td><td>{{ $contactPhone }}</td></tr>
            </table>

            <p style="font-size:14px; color:#6b7280;">Podés responder directamente a este email para contactar al solicitante.</p>
        </div>
        <div class="footer">
            AposPlay · Apostoles, Misiones, Argentina
        </div>
    </div>
</body>
</html>
