<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .sub { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f0f0f0; text-align: left; padding: 6px; border-bottom: 2px solid #ccc; }
        td { padding: 6px; border-bottom: 1px solid #eee; }
        .right { text-align: right; }
        .resumen { margin-top: 16px; padding: 10px; background: #f9f9f9; }
        .resumen strong { font-size: 14px; }
    </style>
</head>
<body>
    <h1>Pollos BCR — Reporte de ventas</h1>
    <div class="sub">
        Del {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
        al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Sucursal</th>
                <th class="right">Total (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $v)
            <tr>
                <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $v->user->name }}</td>
                <td>{{ $v->sucursal->nombre }}</td>
                <td class="right">{{ number_format($v->total, 2) }}</td>
            </tr>
            @endforeach
            @if ($ventas->isEmpty())
            <tr><td colspan="4" style="text-align:center; color:#999;">Sin ventas en este rango</td></tr>
            @endif
        </tbody>
    </table>

    <div class="resumen">
        <div>Número de ventas: {{ $cantidad }}</div>
        <strong>Total vendido: {{ number_format($total, 2) }} Bs</strong>
    </div>
</body>
</html>