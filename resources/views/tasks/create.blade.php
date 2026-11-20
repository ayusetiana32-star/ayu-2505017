<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Tugas</h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('tasks.store') }}">
                    @include('tasks._form', [
                        'task' => $task,
                        'courses' => $courses,
                        'submitLabel' => 'Simpan Tugas',
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
