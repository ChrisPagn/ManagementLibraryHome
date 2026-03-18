<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #2d1810;
            background: #fff;
        }

        /* ── En-tête ── */
        .header {
            border-bottom: 3px solid #c9a96e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 22px;
            color: #1a0f0a;
            letter-spacing: 0.05em;
        }

        .header .subtitle {
            color: #c9a96e;
            font-size: 10px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .header .date {
            float: right;
            font-size: 10px;
            color: #888;
            margin-top: 5px;
        }

        /* ── Stats ── */
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #fef3e2;
            border-radius: 6px;
            padding: 10px 16px;
        }

        .stat {
            display: table-cell;
            text-align: center;
        }

        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #c9a96e;
        }

        .stat-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* ── Sections par type ── */
        .type-section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .type-header {
            background: #c9a96e;
            color: white;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
            letter-spacing: 0.05em;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #fef3e2;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #888;
            border-bottom: 1px solid #e8dcc8;
        }

        tbody tr {
            border-bottom: 1px solid #f5f0e8;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 6px 8px;
            font-size: 10px;
            vertical-align: middle;
        }

        .title-cell {
            font-weight: bold;
            color: #1a0f0a;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-available {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-borrowed {
            background: #fef3c7;
            color: #92400e;
        }

        /* ── Pied de page ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #e8dcc8;
            padding: 6px 0;
            text-align: center;
            font-size: 9px;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="footer">
    ManagementLibraryHome — Exporté le {{ $stats['date'] }}
</div>

{{-- En-tête --}}
<div class="header">
    <span class="date">{{ $stats['date'] }}</span>
    <h1>📚 Ma Bibliothèque</h1>
    <div class="subtitle">Catalogue complet de la médiathèque familiale</div>
</div>

{{-- Statistiques --}}
<div class="stats">
    <div class="stat">
        <div class="stat-number">{{ $stats['total'] }}</div>
        <div class="stat-label">Items total</div>
    </div>
    <div class="stat">
        <div class="stat-number">{{ $stats['available'] }}</div>
        <div class="stat-label">Disponibles</div>
    </div>
    <div class="stat">
        <div class="stat-number">{{ $stats['borrowed'] }}</div>
        <div class="stat-label">Empruntés</div>
    </div>
    <div class="stat">
        <div class="stat-number">{{ $items->count() }}</div>
        <div class="stat-label">Types de médias</div>
    </div>
</div>

{{-- Items groupés par type --}}
@foreach($items as $typeName => $typeItems)
<div class="type-section">
    <div class="type-header">
        {{ $typeName }} ({{ $typeItems->count() }})
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:35%">Titre</th>
                <th style="width:20%">Auteur</th>
                <th style="width:15%">Éditeur</th>
                <th style="width:8%">Année</th>
                <th style="width:10%">Langue</th>
                <th style="width:12%">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($typeItems as $item)
            <tr>
                <td class="title-cell">{{ $item->title }}</td>
                <td>{{ $item->author ?? '—' }}</td>
                <td>{{ $item->publisher ?? '—' }}</td>
                <td>{{ $item->published_year ?? '—' }}</td>
                <td>{{ strtoupper($item->language ?? '—') }}</td>
                <td>
                    <span class="badge {{ $item->status === 'available'
                        ? 'badge-available' : 'badge-borrowed' }}">
                        {{ $item->status === 'available' ? 'Dispo' : 'Emprunté' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

</body>
</html>