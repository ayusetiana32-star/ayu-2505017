<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Daftar Tugas</h2>
                <p class="mt-1 text-sm text-gray-500">Pantau tenggat dan progres tugas kuliah Anda.</p>
            </div>
            <a href="{{ route('tasks.create') }}"
                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tambah Tugas
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div role="status" class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('tasks.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
                    <div class="xl:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari judul tugas</label>
                        <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" maxlength="100"
                            placeholder="Masukkan judul..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700">Mata Kuliah</label>
                        <select id="course_id" name="course_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua mata kuliah</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected((string) ($filters['course_id'] ?? '') === (string) $course->id)>
                                    {{ $course->code }} — {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua status</option>
                            @foreach (['belum' => 'Belum', 'proses' => 'Proses', 'selesai' => 'Selesai'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Prioritas</label>
                        <select id="priority" name="priority"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua prioritas</option>
                            @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="deadline_order" class="block text-sm font-medium text-gray-700">Urutkan deadline</label>
                        <select id="deadline_order" name="deadline_order"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="asc" @selected(($filters['deadline_order'] ?? 'asc') === 'asc')>Terdekat</option>
                            <option value="desc" @selected(($filters['deadline_order'] ?? '') === 'desc')>Terjauh</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <a href="{{ route('tasks.index') }}"
                        class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                        Reset
                    </a>
                    <button type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Terapkan Filter
                    </button>
                </div>
            </form>

            @if ($tasks->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm">
                    @if (!empty($filters['search']) || !empty($filters['course_id']) || !empty($filters['status']) || !empty($filters['priority']))
                        <h3 class="text-lg font-semibold text-gray-900">Tugas tidak ditemukan</h3>
                        <p class="mt-2 text-sm text-gray-500">Coba ubah atau reset filter pencarian Anda.</p>
                    @else
                        <h3 class="text-lg font-semibold text-gray-900">Belum ada tugas</h3>
                        <p class="mt-2 text-sm text-gray-500">Tambahkan tugas dan tenggatnya agar mudah dipantau.</p>
                        <a href="{{ route('tasks.create') }}"
                            class="mt-5 inline-flex rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Tambah Tugas
                        </a>
                    @endif
                </div>
            @else
                @php
                    $priorityClasses = [
                        'rendah' => 'bg-green-100 text-green-800',
                        'sedang' => 'bg-amber-100 text-amber-800',
                        'tinggi' => 'bg-red-100 text-red-800',
                    ];
                    $statusClasses = [
                        'belum' => 'bg-gray-100 text-gray-700',
                        'proses' => 'bg-blue-100 text-blue-800',
                        'selesai' => 'bg-emerald-100 text-emerald-800',
                    ];
                    $statusLabels = [
                        'belum' => 'Belum',
                        'proses' => 'Proses',
                        'selesai' => 'Selesai',
                    ];
                @endphp

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($tasks as $task)
                        <article class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white"
                                    style="background-color: {{ $task->course->color }}">
                                    {{ $task->course->code }} · {{ $task->course->name }}
                                </span>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $priorityClasses[$task->priority] }}">
                                    Prioritas {{ ucfirst($task->priority) }}
                                </span>
                            </div>

                            <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
                            <p class="mt-2 line-clamp-3 whitespace-pre-line text-sm text-gray-600">{{ $task->description }}</p>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$task->status] }}">
                                    {{ $statusLabels[$task->status] }}
                                </span>
                                <span class="text-sm {{ $task->status !== 'selesai' && $task->deadline->isPast() ? 'font-medium text-red-600' : 'text-gray-500' }}">
                                    Tenggat: {{ $task->deadline->format('d M Y, H:i') }}
                                </span>
                            </div>

                            <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4 mt-5">
                                <form method="POST" action="{{ route('tasks.toggle-completion', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="text-sm font-medium {{ $task->status === 'selesai' ? 'text-gray-600 hover:text-gray-900' : 'text-emerald-700 hover:text-emerald-900' }}">
                                        {{ $task->status === 'selesai' ? 'Tandai belum selesai' : 'Tandai selesai' }}
                                    </button>
                                </form>

                                <div class="flex items-center gap-3">
                                    <a href="{{ route('tasks.edit', $task) }}"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                        onsubmit="return confirm('Hapus tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div>{{ $tasks->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
