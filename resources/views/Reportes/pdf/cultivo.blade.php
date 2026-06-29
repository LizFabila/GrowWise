<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $nombre_reporte ?? 'Reporte de Cultivos' }} - GrowWise</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: #ffffff;
        }

        /* ===== HEADER ===== */
        .header {
            background: #1B5E20;
            color: white;
            padding: 30px 20px 20px 20px;
            text-align: center;
            border-bottom: 5px solid #43A047;
        }
        .header h1 {
            margin: 0;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #ffffff;
        }
        .header .logo-icon {
            font-size: 42px;
            display: block;
            margin-bottom: 5px;
        }
        .header .subtitle {
            margin-top: 6px;
            font-size: 14px;
            opacity: 0.85;
            color: #c8e6c9;
            letter-spacing: 1px;
        }
        .header .reporte-nombre {
            margin-top: 10px;
            font-size: 16px;
            font-weight: 700;
            background: rgba(255,255,255,0.15);
            display: inline-block;
            padding: 6px 25px;
            border-radius: 25px;
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .header .fecha-gen {
            margin-top: 8px;
            font-size: 13px;
            opacity: 0.8;
            color: #a5d6a7;
        }

        /* ===== USUARIO ===== */
        .usuario-info {
            background: #e8f5e9;
            padding: 12px 22px;
            border-bottom: 3px solid #2E7D32;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }
        .usuario-info .label {
            font-weight: 700;
            color: #1B5E20;
        }
        .usuario-info .valor {
            color: #333;
        }

        /* ===== CULTIVO CARD ===== */
        .cultivo-card {
            background: white;
            border-radius: 12px;
            margin: 18px 20px;
            padding: 18px 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border-left: 5px solid #2E7D32;
            page-break-inside: avoid;
        }
        .cultivo-card .header-card {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
        }
        .cultivo-card .header-card .imagen {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid #2E7D32;
            flex-shrink: 0;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cultivo-card .header-card .imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cultivo-card .header-card .imagen .no-image {
            font-size: 32px;
            color: #2E7D32;
        }
        .cultivo-card h2 {
            color: #1B5E20;
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .cultivo-card h2 .badge {
            font-size: 11px;
            background: #e8f5e9;
            color: #2E7D32;
            padding: 3px 14px;
            border-radius: 20px;
            margin-left: 10px;
            font-weight: 600;
        }
        .cultivo-card .ubicacion {
            font-size: 13px;
            color: #888;
            margin-top: 2px;
        }

        /* ===== DATOS GRID ===== */
        .datos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 12px 0;
        }
        .dato-item {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .dato-item .label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .dato-item .value {
            font-size: 15px;
            font-weight: 700;
            color: #1B5E20;
            margin-top: 2px;
        }

        /* ===== PROGRESO ===== */
        .progreso-container {
            margin: 12px 0 4px 0;
        }
        .progreso-container .barra {
            background: #e9ecef;
            height: 20px;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 5px;
        }
        .progreso-container .barra .fill {
            height: 100%;
            background: #2E7D32;
            border-radius: 12px;
        }
        .progreso-container .info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            padding-bottom: 15px;
        }
        .footer .logo {
            font-weight: 700;
            color: #1B5E20;
            font-size: 16px;
        }

        .badge-estado {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-completado {
            background: #6c757d;
        }
        .text-center { text-align: center; }
        .mt-2 { margin-top: 8px; }
        .mb-2 { margin-bottom: 8px; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 600px) {
            .datos-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .cultivo-card .header-card {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<!-- ========================================== -->
<!-- HEADER -->
<!-- ========================================== -->
<div class="header">
    <span class="logo-icon">🌱</span>
    <h1>GrowWise</h1>
    <div class="subtitle">Sistema de Gestion Inteligente para Cultivos Hidroponicos</div>
    <div class="reporte-nombre">{{ $nombre_reporte ?? 'Reporte de Cultivos' }}</div>
    <div class="fecha-gen">Generado el: {{ $fecha_generacion }}</div>
</div>

<!-- ========================================== -->
<!-- USUARIO -->
<!-- ========================================== -->
<div class="usuario-info">
    <div>
        <span class="label">Propietario:</span>
        <span class="valor">{{ $usuario->nombre }} ({{ $usuario->email }})</span>
    </div>
    <div>
        <span class="label">Total cultivos:</span>
        <span class="valor">{{ count($data) }}</span>
    </div>
</div>

<!-- ========================================== -->
<!-- CULTIVOS -->
<!-- ========================================== -->
@foreach($data as $item)
    @php
        $siembra = $item['siembra'];
        $cultivo = $siembra->cultivo;
        $estado = $siembra->estado;
        $badgeClase = $estado == 'Activa' ? 'badge-estado' : 'badge-estado badge-completado';
        $tieneImagen = !empty($item['imagen_base64']);
    @endphp
    <div class="cultivo-card">

        <!-- Cabecera con imagen -->
        <div class="header-card">
            <div class="imagen">
                @if($tieneImagen)
                    <img src="{{ $item['imagen_base64'] }}" alt="{{ $cultivo->nombre }}">
                @else
                    <div class="no-image">🌱</div>
                @endif
            </div>
            <div>
                <h2>
                    {{ $cultivo->nombre }}
                    <span class="badge {{ $badgeClase }}">{{ $estado }}</span>
                </h2>
                <div class="ubicacion">
                    Modulo: {{ $siembra->modulo->nombre ?? 'N/A' }}
                    @if($siembra->charola)
                        | Charola #{{ $siembra->charola }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Datos -->
        <div class="datos-grid">
            <div class="dato-item">
                <div class="label">Fecha Siembra</div>
                <div class="value">{{ $item['fecha_siembra'] }}</div>
            </div>
            <div class="dato-item">
                <div class="label">Fecha Estimada Cosecha</div>
                <div class="value">{{ $item['fecha_estimada'] }}</div>
            </div>
            <div class="dato-item">
                <div class="label">Precio Semillas</div>
                <div class="value">${{ number_format($item['precio_semilla'], 2) }}</div>
            </div>
            <div class="dato-item">
                <div class="label">Humedad Sustrato</div>
                <div class="value">{{ $item['humedad'] }}%</div>
            </div>
            <div class="dato-item">
                <div class="label">Progreso</div>
                <div class="value">{{ $item['progreso'] }}%</div>
            </div>
            <div class="dato-item">
                <div class="label">Ubicacion</div>
                <div class="value" style="font-size: 14px;">{{ $siembra->modulo->nombre ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Barra de progreso -->
        <div class="progreso-container">
            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                <span>Progreso del ciclo</span>
                <span><strong>{{ $item['progreso'] }}%</strong> (Dia {{ $item['dias_transcurridos'] }} de {{ $item['dias_totales'] }})</span>
            </div>
            <div class="barra">
                <div class="fill" style="width: {{ $item['progreso'] }}%;"></div>
            </div>
            <div class="info">
                <span>Inicio: {{ $item['fecha_siembra'] }}</span>
                <span>Meta: {{ $item['fecha_estimada'] }}</span>
            </div>
        </div>

    </div>
@endforeach

<!-- ========================================== -->
<!-- FOOTER -->
<!-- ========================================== -->
<div class="footer">
    <div class="logo">GrowWise</div>
    Sistema de Gestion Inteligente para Cultivos Hidroponicos
    <br>© {{ date('Y') }} - Todos los derechos reservados
</div>

</body>
</html>
