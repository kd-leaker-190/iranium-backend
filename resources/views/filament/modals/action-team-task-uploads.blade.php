<div class="space-y-4">
    <div class="text-sm text-gray-600">
        تیم: <strong>{{ $record->team?->name }}</strong>
        — عملیات: <strong>{{ $record->action?->name }}</strong>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="border-b">
                <tr class="text-right">
                    <th class="py-2 px-3">عنوان وظیفه</th>
                    <th class="py-2 px-3">نوع</th>
                    <th class="py-2 px-3">تاریخ ثبت</th>
                    <th class="py-2 px-3">فایل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $item['task_title'] }}</td>
                        <td class="py-2 px-3">{{ $item['task_type'] }}</td>
                        <td class="py-2 px-3">
                            {{ optional($item['done_at'])->format('Y-m-d H:i') }}
                        </td>
                        <td class="py-2 px-3">
                            @if ($item['has_file'] && $item['file_url'])
                                <a class="text-primary-600 underline" href="{{ $item['file_url'] }}"
                                    download="{{ $item['download_name'] }}">
                                    دانلود ({{ $item['download_name'] }})
                                </a>
                            @else
                                <span class="text-gray-400">ندارد</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="py-3 px-3 text-gray-500" colspan="4">
                            چیزی برای نمایش وجود ندارد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
