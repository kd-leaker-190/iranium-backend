@php
    // Table styling below mirrors Filament's own table tokens (vendor/filament/tables
    // resources/views/index.blade.php + components/header-cell.blade.php + columns/
    // text-column.blade.php) so this modal reads as a native part of the panel rather
    // than a separately designed page.
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <div class="flex items-center gap-4 gap-x-6 rounded-xl bg-gray-50 px-4 py-3 text-center dark:bg-white/5">
            <div class="w-full">
                <p class="text-xl font-semibold text-gray-950 dark:text-white">{{ number_format($team->score) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">امتیاز نهایی تیم (teams.score)</p>
            </div>
        </div>
        <div class="flex items-center gap-4 gap-x-6 rounded-xl bg-gray-50 px-4 py-3 text-center dark:bg-white/5">
            <div class="w-full">
                <p class="text-xl font-semibold text-gray-950 dark:text-white">{{ number_format($team->coin) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">سکه نهایی تیم (teams.coin)</p>
            </div>
        </div>
    </div>

    {{-- Score ledger --}}
    <div>
        <p class="mb-2 text-sm font-semibold text-gray-950 dark:text-white">ریز امتیازها</p>

        @if ($breakdown['score_entries']->isEmpty())
            <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning" compact>
                هیچ رکورد ریز امتیازی (ScoreTeam) برای این تیم ثبت نشده است — امتیاز نهایی
                ({{ number_format($team->score) }}) بدون سابقه‌ی قابل ردیابی است.
            </x-filament::section>
        @else
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <table class="w-full table-auto text-start">
                    <thead class="divide-y divide-gray-200 dark:divide-white/10">
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="px-3 py-3.5 text-start sm:first-of-type:ps-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">منبع</span>
                            </th>
                            <th class="px-3 py-3.5 text-start">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">مقدار</span>
                            </th>
                            <th class="px-3 py-3.5 text-start sm:last-of-type:pe-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">تاریخ</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($breakdown['score_entries'] as $entry)
                            <tr>
                                <td class="px-3 py-4 text-sm text-gray-950 dark:text-white sm:first-of-type:ps-6">{{ $entry['label'] }}</td>
                                <td class="px-3 py-4 text-sm font-semibold text-gray-950 dark:text-white">{{ number_format($entry['amount']) }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 sm:last-of-type:pe-6">
                                    {{ $entry['created_at'] ? \Morilog\Jalali\Jalalian::fromDateTime($entry['created_at'])->format('Y/m/d H:i') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @unless ($breakdown['score_reconciles'])
                <div class="mt-2">
                    <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning" compact>
                        مجموع ریز امتیازها ({{ number_format($breakdown['score_ledger_sum']) }}) با امتیاز نهایی تیم
                        ({{ number_format($team->score) }}) مطابقت ندارد — بخشی از امتیاز این تیم احتمالاً به‌صورت
                        دستی ثبت شده است.
                    </x-filament::section>
                </div>
            @endunless
        @endif
    </div>

    {{-- Coin ledger --}}
    <div>
        <p class="mb-2 text-sm font-semibold text-gray-950 dark:text-white">ریز سکه‌ها</p>

        @if ($breakdown['coin_entries']->isEmpty())
            <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning" compact>
                هیچ رکورد ریز سکه‌ای (TeamCoin) برای این تیم ثبت نشده است — سکه نهایی
                ({{ number_format($team->coin) }}) بدون سابقه‌ی قابل ردیابی است.
            </x-filament::section>
        @else
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <table class="w-full table-auto text-start">
                    <thead class="divide-y divide-gray-200 dark:divide-white/10">
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="px-3 py-3.5 text-start sm:first-of-type:ps-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">منبع</span>
                            </th>
                            <th class="px-3 py-3.5 text-start">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">مقدار</span>
                            </th>
                            <th class="px-3 py-3.5 text-start sm:last-of-type:pe-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">تاریخ</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($breakdown['coin_entries'] as $entry)
                            <tr>
                                <td class="px-3 py-4 text-sm text-gray-950 dark:text-white sm:first-of-type:ps-6">{{ $entry['label'] }}</td>
                                <td class="px-3 py-4 text-sm font-semibold text-gray-950 dark:text-white">{{ number_format($entry['amount']) }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 sm:last-of-type:pe-6">
                                    {{ $entry['created_at'] ? \Morilog\Jalali\Jalalian::fromDateTime($entry['created_at'])->format('Y/m/d H:i') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @unless ($breakdown['coin_reconciles'])
                <div class="mt-2">
                    <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning" compact>
                        مجموع ریز سکه‌ها ({{ number_format($breakdown['coin_ledger_sum']) }}) با سکه نهایی تیم
                        ({{ number_format($team->coin) }}) مطابقت ندارد — بخشی از سکه این تیم احتمالاً به‌صورت
                        دستی ثبت شده است.
                    </x-filament::section>
                </div>
            @endunless
        @endif
    </div>
</div>
