<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>QR Codes</title>
    <style>
        body {
            text-align: center;
            font-size: 12px;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        td {
            width: 25%; /* 4 per row */
            text-align: center;
            padding: 10px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .qr {
            width: 100px;
            height: 100px;
            display: block;
            margin: 0 auto 8px auto;
        }

        .coin-value {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin-top: 5px;
        }

        .coin-value img {
            width: 16px;
            height: 16px;
            object-fit: contain;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
@foreach ($coins->chunk(24) as $chunk)
<table>
    @foreach ($chunk->chunk(4) as $row)
    <tr>
        @foreach ($row as $item)
            <td>
                <img class="qr" src="{{ $item['src'] }}" alt="QR">
                <div class="coin-value">
                    <img src="{{ $item['icon'] }}" alt="coin">
                    <span>{{ $item['value'] }}</span>
                </div>
            </td>
        @endforeach

        @if ($row->count() < 4)
            @for ($i = 0; $i < 4 - $row->count(); $i++)
                <td></td>
            @endfor
        @endif
    </tr>
    @endforeach
</table>

@if (!$loop->last)
    <div class="page-break"></div>
@endif
@endforeach
</body>
</html>
