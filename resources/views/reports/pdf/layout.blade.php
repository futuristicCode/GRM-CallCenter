<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'GRM – Rapport')</title>
    <style>
        @page { margin: 20mm 15mm 25mm 15mm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.5;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18pt;
            color: #1e3a5f;
            margin: 0 0 4px 0;
        }
        .header .meta {
            font-size: 8pt;
            color: #6b7280;
        }
        .footer {
            position: fixed;
            bottom: -20mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
        .footer .page:after { content: counter(page); }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th {
            background: #2563eb;
            color: #fff;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 10px;
            text-align: left;
        }
        td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 9pt; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 7pt;
            font-weight: bold;
        }
        .badge-haute { background: #fee2e2; color: #991b1b; }
        .badge-moyenne { background: #fef3c7; color: #92400e; }
        .badge-basse { background: #d1fae5; color: #065f46; }
        .badge-en_attente { background: #fef3c7; color: #92400e; }
        .badge-en_cours { background: #dbeafe; color: #1e40af; }
        .badge-resolu { background: #d1fae5; color: #065f46; }
        .badge-rejete { background: #fee2e2; color: #991b1b; }
        .badge-attente_client { background: #f3f4f6; color: #374151; }
        .badge-archive { background: #e5e7eb; color: #4b5563; }
        .stat-grid { width: 100%; margin-bottom: 15px; }
        .stat-grid td { width: 25%; text-align: center; border: 1px solid #e5e7eb; }
        .stat-grid .num { font-size: 22pt; font-weight: bold; display: block; }
        .stat-grid .lbl { font-size: 7pt; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
        .card { border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 15px; overflow: hidden; }
        .card h3 {
            background: #f1f5f9;
            margin: 0;
            padding: 8px 12px;
            font-size: 10pt;
            color: #1e3a5f;
            border-bottom: 1px solid #e5e7eb;
        }
        .card-body { padding: 0; }
        .section-title {
            font-size: 12pt;
            color: #1e3a5f;
            border-left: 4px solid #2563eb;
            padding-left: 10px;
            margin: 20px 0 10px 0;
        }
        .info-grid { width: 100%; }
        .info-grid td { width: 50%; vertical-align: top; border: none; padding: 4px 8px; }
        .info-grid .label { font-size: 7pt; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
        .info-grid .value { font-size: 10pt; font-weight: 600; }
        .bar-container { background: #e5e7eb; border-radius: 4px; height: 16px; overflow: hidden; }
        .bar { height: 16px; border-radius: 4px; }
        .bar-blue { background: #2563eb; }
        .bar-amber { background: #f59e0b; }
        .bar-emerald { background: #10b981; }
        .bar-red { background: #ef4444; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="footer">
        {{ __('GRM – Gestion des Réclamations') }} | <span class="page">{{ __('Page') }} </span> | {{ __('Généré le') }} {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="header">
        <h1>@yield('title')</h1>
        <div class="meta">
            {{ __('Généré le') }} {{ now()->format('d/m/Y') }} {{ __('à') }} {{ now()->format('H:i') }} {{ __('par') }} {{ auth()->user()->name }}
            @hasSection('period')
                | {{ __('Période') }} : @yield('period')
            @endif
        </div>
    </div>

    @yield('content')
</body>
</html>
