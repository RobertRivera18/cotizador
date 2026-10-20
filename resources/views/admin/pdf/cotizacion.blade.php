@php
    $logoPath = public_path('img/DISEIN.png');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;

    // Logos de los bancos en base64 para evitar errores de renderizado en DomPDF
    $pichinchaPath = public_path('img/logoPichincha.jpg');
    $pichinchaSrc = file_exists($pichinchaPath) ? 'data:image/jpg;base64,' . base64_encode(file_get_contents($pichinchaPath)) : null;

    $guayaquilPath = public_path('img/logoGuayaquil.png');
    $guayaquilSrc = file_exists($guayaquilPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($guayaquilPath)) : null;

    $pacificoPath = public_path('img/logoPacifico.png');
    $pacificoSrc = file_exists($pacificoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pacificoPath)) : null;

    $subtotal = $cotizacion->detalles->sum('subtotal');
    $iva = $subtotal * 0.15;
    $totalGeneral = $subtotal + $iva;

    $numero = $cotizacion->created_at->format('Y') . '-' . str_pad($cotizacion->id, 4, '0', STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización No. {{ $numero }}</title>
    <style>
        @page { margin: 28px 38px; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #111;
            line-height: 1.35;
        }

        table { border-collapse: collapse; }

        /* ===== ENCABEZADO ===== */
        .header { width: 100%; margin-bottom: 6px; }
        .header td { vertical-align: top; }
        .company-name {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 250px;
        }
        .company-ruc { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .company-address { font-size: 8.5px; color: #444; margin-top: 2px; line-height: 1.2; }
        .logo { height: 62px; }

        /* ===== TITULO ===== */
        .title-wrap {
            border-bottom: 1.5px solid #000;
            text-align: center;
            padding-bottom: 3px;
            margin: 8px 0 12px 0;
        }
        .title {
            display: inline-block;
            font-size: 12.5px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* ===== DATOS ===== */
        .info { width: 100%; margin-bottom: 14px; }
        .info td { padding: 2.5px 4px; vertical-align: top; }
        .info .lbl { font-weight: bold; text-align: right; width: 15%; white-space: nowrap; }
        .info .val { width: 35%; }

        /* ===== ITEMS ===== */
        .items { width: 100%; }
        .items th {
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
            text-align: left;
            padding: 2px 4px;
        }
        .items td {
            border: 1px solid #000;
            padding: 10px 6px;
            vertical-align: middle;
            text-align: center;
        }
        .items td.num { color: #b45309; }
        .items td.money { color: #1e3a8a; text-align: right; }
        .items .prod { text-transform: uppercase; text-align: left; }
        .items .prod .spec { font-size: 8.5px; color: #444; margin-top: 3px; font-weight: normal; }

        /* ===== TOTALES ===== */
        .totals-wrap { width: 100%; }
        .totals { width: 205px; }
        .totals td { border: 1px solid #000; padding: 3px 5px; }
        .totals .lbl { font-weight: bold; width: 70px; }
        .totals .val { text-align: right; color: #1e3a8a; }

        /* ===== FIRMA ===== */
        .firma { font-weight: bold; margin: 18px 0 4px 0; }

        /* ===== CONDICIONES ===== */
        .cond { width: 100%; border: 1.5px solid #000; }
        .cond td { padding: 2px 6px; text-transform: uppercase; }
        .cond .cond-title {
            text-align: center;
            font-weight: bold;
            color: #b45309;
            border-bottom: 1px solid #000;
        }

        /* ===== BANCOS ===== */
        .bank-head { width: 100%; margin-top: 16px; }
        .bank-head .bank-title {
            font-size: 12px;
            color: #1e3a8a;
            letter-spacing: 0.4px;
        }
        .bank-info { text-align: center; font-size: 9px; margin: 6px 0 10px 0; }
        .accounts { width: 100%; }
        .accounts td { padding: 6px 4px; vertical-align: middle; }
        .accounts .bank { width: 45%; font-weight: bold; font-size: 11px; }
        .accounts .acc { font-size: 11px; color: #1e3a8a; }
        .pichincha { color: #b8860b; }
        .guayaquil { color: #c2185b; }
        .pacifico { color: #0369a1; }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <table class="header">
        <tr>
            <td>
                <div class="company-name">
                    DISEIN Distribuidora y Suministros Eléctricos Industriales
                </div>
                <div class="company-ruc">RUC {{ '0991307842001' }}</div>
                <div class="company-address">
                    <strong>DIRECCIÓN:</strong> 5° Pasaje 38 B Vergeles, MZ 169, Guayaquil
                </div>
            </td>
            <td style="text-align: right;">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" class="logo" alt="Disein Electric">
                @endif
            </td>
        </tr>
    </table>

    {{-- TITULO --}}
    <div class="title-wrap">
        <span class="title">Cotización No. {{ $numero }}</span>
    </div>

    {{-- DATOS DEL CLIENTE Y ASESOR --}}
    <table class="info">
        <tr>
            <td class="lbl">FECHA:</td>
            <td class="val">{{ $cotizacion->created_at->format('j/n/Y') }}</td>
            <td class="lbl">ASESOR COMERCIAL:</td>
            <td class="val">{{ strtoupper(auth()->user()->name ?? 'LORENA BAQUE') }}</td>
        </tr>
        <tr>
            <td class="lbl">CLIENTE:</td>
            <td class="val">{{ strtoupper($cotizacion->nombre_cliente) }}</td>
            <td class="lbl">CELULAR:</td>
            <td class="val">{{ $cotizacion->telefono ?: '' }}</td>
        </tr>
        <tr>
            <td class="lbl">RUC / C.I.:</td>
            <td class="val">{{ $cotizacion->cedula_ruc ?? '' }}</td>
            <td class="lbl">TELÉFONO:</td>
            <td class="val">(04) 3916000</td>
        </tr>
        <tr>
            <td class="lbl">DIR. CLIENTE:</td>
            <td class="val">{{ strtoupper($cotizacion->direccion ?? 'GUAYAQUIL') }}</td>
            <td class="lbl">EXTENSIÓN:</td>
            <td class="val">5000</td>
        </tr>
        <tr>
            <td class="lbl">VALIDEZ:</td>
            <td class="val">VÁLIDO POR 15 DÍAS DESDE SU EMISIÓN</td>
            <td class="lbl">MAIL:</td>
            <td class="val" style="color: #1d4ed8;">{{ $cotizacion->email ?: 'contabilidad@diseinelectric.com' }}</td>
        </tr>
    </table>

    {{-- DETALLE --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 7%;">Cant</th>
                <th style="width: 48%;">Producto</th>
                <th style="width: 14%;">Precio U.</th>
                <th style="width: 14%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->detalles as $i => $d)
                @php
                    $item = $d->equipo ?? $d->generador ?? null;

                    $marca = $item?->marca ?? $d->marca ?? null;
                    $fases = $item?->fases ?? $d->fases ?? null;
                    $combustible = $item?->combustible ?? $d->combustible ?? null;
                    $potencia = $item?->capacidad_kw ?? $item?->potencia_nominal_w ?? $d->potencia ?? null;
                @endphp
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td class="num">{{ $d->cantidad }}</td>
                    <td class="prod">
                        {{ $d->descripcion }}

                        @if ($marca || $fases || $combustible || $potencia)
                            <div class="spec">
                                @if($marca) MARCA: {{ $marca }} &nbsp;|&nbsp; @endif
                                @if($fases) FASES: {{ $fases }} &nbsp;|&nbsp; @endif
                                @if($combustible) COMBUSTIBLE: {{ strtoupper($combustible) }} &nbsp;|&nbsp; @endif
                                @if($potencia) POTENCIA: {{ $potencia }} {{ is_numeric($potencia) && $potencia > 1000 ? 'W' : 'KW' }} @endif
                            </div>
                        @endif
                    </td>
                    <td class="money">$ &nbsp;&nbsp;&nbsp; {{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="money">$ &nbsp;&nbsp;&nbsp; {{ number_format($d->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALES --}}
    <table class="totals-wrap">
        <tr>
            <td></td>
            <td style="width: 205px;">
                <table class="totals">
                    <tr>
                        <td class="lbl">Subtotal:</td>
                        <td class="val">$ &nbsp;&nbsp; {{ number_format($subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">IVA 15%:</td>
                        <td class="val">$ &nbsp;&nbsp; {{ number_format($iva, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Total :</td>
                        <td class="val">$ &nbsp;&nbsp; {{ number_format($totalGeneral, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="firma">DISEIN CIA. LTDA.</div>

    {{-- CONDICIONES --}}
    <table class="cond">
        <tr><td class="cond-title">Forma de pago</td></tr>
        <tr><td>Valores incluyen IVA. Vigencia de la cotización: 15 días</td></tr>
        <tr><td>Tiempo de entrega del producto-servicio: entrega en 2 a 20 días laborables</td></tr>
        <tr><td style="color: #b45309;">Forma de pago: transferencia bancaria 50% anticipo y 50% contraentrega del producto</td></tr>
    </table>

    {{-- CUENTAS BANCARIAS --}}
    <table class="bank-head">
        <tr>
            <td class="bank-title">
                CUENTAS BANCARIAS<br>PARA TRANSFERENCIAS
            </td>
            <td style="text-align: right;">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" style="height: 38px;" alt="Disein Electric">
                @endif
            </td>
        </tr>
    </table>

    <div class="bank-info">
        <strong>NOMBRE:</strong> DISEIN DISTRIBUIDORA Y SUMINISTROS ELECTRICOS INDUSTRIALES CIA. LTDA.<br>
        <strong>RUC 0991307842001</strong><br>
        <strong>DIRECCIÓN:</strong> 5° Pasaje 38 B Vergeles, MZ 169, Guayaquil<br>
        email: contabilidad@diseinelectric.com
    </div>

    <table class="accounts">
        <tr>
            <td class="bank pichincha">
                @if($pichinchaSrc)
                    <img src="{{ $pichinchaSrc }}" alt="Banco Pichincha" style="height: 25px; vertical-align: middle; margin-right: 6px;">
                @endif
                BANCO PICHINCHA
            </td>
            <td class="acc">CUENTA CORRIENTE #2100082843</td>
        </tr>
        <tr>
            <td class="bank guayaquil">
                @if($guayaquilSrc)
                    <img src="{{ $guayaquilSrc }}" alt="Banco Guayaquil" style="height: 25px; vertical-align: middle; margin-right: 6px;">
                @endif
                Banco Guayaquil
            </td>
            <td class="acc">CUENTA CORRIENTE #0019140334</td>
        </tr>
        <tr>
            <td class="bank pacifico">
                @if($pacificoSrc)
                    <img src="{{ $pacificoSrc }}" alt="Banco del Pacífico" style="height: 25px; vertical-align: middle; margin-right: 6px;">
                @endif
                Banco del Pacífico
            </td>
            <td class="acc">CUENTA CORRIENTE #0008080372</td>
        </tr>
    </table>

</body>
</html>
