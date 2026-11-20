<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">Ringkasan tugas dan tenggat kuliah Anda.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section aria-label="Statistik tugas" class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                @php
                    $statCards = [
                        [
                            'label' => 'Belum selesai',
                            'value' => $stats['unfinished'],
                            'detail' => 'Tugas yang masih perlu dikerjakan',
                            'color' => 'border-indigo-500',
                        ],
                        [
                            'label' => 'Terlambat',
                            'value' => $stats['overdue'],
                            'detail' => 'Tenggat sudah terlewati',
                            'color' => 'border-red-500',
                        ],
                        [
                            'label' => 'Deadline hari ini',
                            'value' => $stats['today'],
                            'detail' => 'Tenggat tersisa hari ini',
                            'color' => 'border-amber-500',
                        ],
                        [
                            'label' => '7 hari ke depan',
                            'value' => $stats['next_seven_days'],
                            'detail' => 'Tenggat dari besok hingga 7 hari',
                            'color' => 'border-green-500',
                        ],
                    ];
                @endphp

                @foreach ($statCards as $card)
                    <article class="rounded-xl border-l-4 {{ $card['color'] }} bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $card['value'] }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $card['detail'] }}</p>
                    </article>
                @endforeach
            </section>

            <section>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tenggat terdekat</h3>
                        <p class="mt-1 text-sm text-gray-500">Tugas yang terlambat atau jatuh tempo dalam 7 hari.</p>
                    </div>
                    <a href="{{ route('tasks.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        Semua tugas
                    </a>
                </div>

                @if ($deadlineTasks->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow-sm">
                        <p class="font-medium text-gray-800">Tidak ada tenggat yang perlu diperhatikan.</p>
                        <p class="mt-1 text-sm text-gray-500">Tugas mendatang akan muncul di sini.</p>
                    </div>
                @else
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <ul class="divide-y divide-gray-100">
                            @foreach ($deadlineTasks as $task)
                                <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold text-white"
                                                style="background-color: {{ $task->course->color }}">
                                                {{ $task->course->code }} · {{ $task->course->name }}
                                            </span>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $task->deadline_badge_class }}">
                                                {{ $task->time_remaining }}
                                            </span>
                                        </div>
                                        <a href="{{ route('tasks.edit', $task) }}"
                                            class="mt-2 block truncate font-semibold text-gray-900 hover:text-indigo-700">
                                            {{ $task->title }}
                                        </a>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-3">
                                        <span class="text-sm text-gray-500">{{ $task->deadline->format('d M Y, H:i') }}</span>
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                            {{ match ($task->priority) {
                                                'tinggi' => 'bg-red-100 text-red-800',
                                                'sedang' => 'bg-amber-100 text-amber-800',
                                                default => 'bg-green-100 text-green-800',
                                            } }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
