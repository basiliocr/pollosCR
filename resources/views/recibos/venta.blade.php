<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 12px; color: #333; }
        .ticket { width: 100%; max-width: 300px; margin: 0 auto; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .titulo { font-size: 16px; font-weight: bold; }
        hr { border: none; border-top: 1px dashed #999; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .total-row td { font-size: 14px; font-weight: bold; padding-top: 6px; }
        .footer { margin-top: 12px; font-size: 10px; color: #777; }
        .cancelada { color: #c00; border: 2px solid #c00; padding: 4px; text-align: center; font-weight: bold; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="ticket">
        @if ($venta->estado === 'cancelada')
            <div class="cancelada">VENTA CANCELADA</div>
        @endif

        <div class="center">
            <div class="titulo">Pollos BCR</div>
            <div>{{ $venta->sucursal->nombre }}</div>
            <div>{{ $venta->sucursal->direccion }}</div>
        </div>

        <hr>

        <table>
            <tr>
                <td>Recibo:</td>
                <td class="right">#{{ strtoupper(substr($venta->id, 0, 8)) }}</td>
            </tr>
            <tr>
                <td>Fecha:</td>
                <td class="right">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Atendió:</td>
                <td class="right">{{ $venta->user->name }}</td>
            </tr>
            @if ($venta->cliente)
            <tr>
                <td>Cliente:</td>
                <td class="right">{{ $venta->cliente->nombre }}</td>
            </tr>
            @if ($venta->cliente->nit)
            <tr>
                <td>NIT:</td>
                <td class="right">{{ $venta->cliente->nit }}</td>
            </tr>
            @endif
            @endif
        </table>

        <hr>

        <table>
            <thead>
                <tr class="bold">
                    <td>Cant</td>
                    <td>Detalle</td>
                    <td class="right">Subtotal</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                <tr>
                    <td>{{ rtrim(rtrim(number_format($detalle->cantidad, 3), '0'), '.') }}</td>
                    <td>
                        @if ($detalle->producto)
                            {{ $detalle->producto->nombre }}
                        @elseif ($detalle->combo)
                            {{ $detalle->combo->nombre }} (combo)
                        @endif
                    </td>
                    <td class="right">{{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr>

        <table>
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="right">{{ number_format($venta->total, 2) }} Bs</td>
            </tr>
        </table>

        <hr>

        <table>
            <tr class="bold">
                <td colspan="2">Pago</td>
            </tr>
            @foreach ($venta->pagos as $pago)
            <tr>
                <td>{{ $pago->metodoPago->nombre }}</td>
                <td class="right">{{ number_format($pago->monto, 2) }} Bs</td>
            </tr>
            @endforeach
        </table>

        <div class="footer center">
            <hr>
            ¡Gracias por su compra!<br>
            Este documento no es una factura oficial.
        </div>
    </div>
</body>
</html>