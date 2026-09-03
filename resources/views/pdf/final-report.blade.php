<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>گزارش نهایی</title>
    <style>
        @font-face {
            font-family: 'noto-sans-arabic';
            src: url('{{ public_path('fonts/noto-sans-arabic/NotoSansArabic-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'noto-sans-arabic';
            src: url('{{ public_path('fonts/noto-sans-arabic/NotoSansArabic-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'noto-sans-arabic', sans-serif;
            direction: rtl;
            text-align: right;
            color: #1f2430;
            font-size: 11px;
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #00b48d;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0 0 4px 0;
            color: #00b48d;
        }

        .header .meta {
            font-size: 10px;
            color: #6b7280;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }

        .summary .value {
            font-size: 16px;
            font-weight: bold;
            display: block;
        }

        .summary .label {
            font-size: 9px;
            color: #6b7280;
        }

        .winner-box {
            background-color: #fff9e6;
            border: 1px solid #cfb917;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }

        .winner-box .title {
            font-size: 10px;
            color: #92720b;
            margin-bottom: 4px;
        }

        .winner-box .name {
            font-size: 16px;
            font-weight: bold;
        }

        h2.section-title {
            font-size: 13px;
            color: #00b48d;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin: 18px 0 8px 0;
        }

        table.ranking {
            width: 100%;
            border-collapse: collapse;
        }

        table.ranking th, table.ranking td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            font-size: 10px;
        }

        table.ranking th {
            background-color: #f3f4f6;
            text-align: right;
        }

        table.ranking tr.top3 {
            background-color: #fffbe6;
        }

        table.ranking td.num {
            text-align: center;
        }

        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>گزارش نهایی — {{ $appName }}</h1>
        <div class="meta">
            کوهورت (تاریخ شروع): {{ \Morilog\Jalali\Jalalian::fromDateTime($startDate)->format('Y/m/d') }}
            ({{ $startDate }})
            &nbsp;|&nbsp;
            تاریخ تولید گزارش: {{ \Morilog\Jalali\Jalalian::fromDateTime($generatedAt)->format('Y/m/d H:i') }}
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="value">{{ number_format($summary['total_teams']) }}</span>
                <span class="label">تعداد تیم‌ها</span>
            </td>
            <td>
                <span class="value">{{ number_format($summary['total_score']) }}</span>
                <span class="label">مجموع امتیاز</span>
            </td>
            <td>
                <span class="value">{{ number_format($summary['total_coin']) }}</span>
                <span class="label">مجموع سکه</span>
            </td>
            <td>
                <span class="value">{{ $summary['winner']?->rank ?? '—' }}</span>
                <span class="label">رتبه برنده</span>
            </td>
        </tr>
    </table>

    @if ($summary['winner'])
        <div class="winner-box">
            <div class="title">تیم برنده</div>
            <div class="name">{{ $summary['winner']->name }}</div>
            <div class="meta">
                امتیاز: {{ number_format($summary['winner']->score) }} &nbsp;|&nbsp;
                سکه: {{ number_format($summary['winner']->coin) }} &nbsp;|&nbsp;
                شناسه تیم: {{ $summary['winner']->team_identifier }}
            </div>
        </div>
    @endif

    <x-filament::section>
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
