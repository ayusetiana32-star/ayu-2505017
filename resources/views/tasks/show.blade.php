<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail Tugas</h2>
                <p class="mt-1 text-sm text-gray-500">Informasi lengkap tugas dan deadline.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('calendar') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Kembali ke kalender
                </a>
                <a href="{{ route('tasks.edit', $task) }}"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Edit tugas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <article class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold text-white"
                    style="background-color: {{ $task->course->color }}">
                    {{ $task->course->code }} · {{ $task->course->name }}
                </span>

                <h1 class="mt-4 text-2xl font-bold text-gray-900">{{ $task->title }}</h1>

                <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->deadline->format('d M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($task->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Prioritas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($task->priority) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dosen</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->course->lecturer }}</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <h2 class="text-sm font-medium text-gray-500">Deskripsi</h2>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-800">{{ $task->description }}</p>
                </div>
            </div>
        </article>
    </div>
</x-app-layout>
