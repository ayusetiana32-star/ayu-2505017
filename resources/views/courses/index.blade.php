<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Mata Kuliah</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola mata kuliah dan informasi dosen Anda.</p>
            </div>
            <a href="{{ route('courses.create') }}"
                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tambah Mata Kuliah
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

            @if ($courses->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">Belum ada mata kuliah</h3>
                    <p class="mt-2 text-sm text-gray-500">Tambahkan mata kuliah pertama untuk mulai mengatur tugas.</p>
                    <a href="{{ route('courses.create') }}"
                        class="mt-5 inline-flex rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Tambah Mata Kuliah
                    </a>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($courses as $course)
                        <article class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg text-lg font-bold text-white"
                                    style="background-color: {{ $course->color }}">
                                    {{ mb_substr($course->name, 0, 1) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold text-white"
                                        style="background-color: {{ $course->color }}">
                                        {{ $course->code }}
                                    </span>
                                    <h3 class="mt-2 truncate text-lg font-semibold text-gray-900">{{ $course->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ $course->lecturer }}</p>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4">
                                <span class="text-sm text-gray-500">{{ $course->tasks_count }} tugas</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('courses.edit', $course) }}"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('courses.destroy', $course) }}"
                                        onsubmit="return confirm('Hapus mata kuliah ini beserta tugas dan pengingatnya?')">
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

                <div>{{ $courses->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
