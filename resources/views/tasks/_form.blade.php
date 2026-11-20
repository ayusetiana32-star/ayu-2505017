@csrf

<div class="space-y-6">
    @if ($courses->isEmpty())
        <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Anda belum memiliki mata kuliah. <a href="{{ route('courses.create') }}" class="font-semibold underline">Buat mata kuliah terlebih dahulu.</a>
        </div>
    @endif

    <div>
        <label for="course_id" class="block text-sm font-medium text-gray-700">Mata Kuliah</label>
        <select id="course_id" name="course_id" required @disabled($courses->isEmpty())
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih mata kuliah</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('course_id', $task->course_id ?? '') === (string) $course->id)>
                    {{ $course->code }} — {{ $course->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul Tugas</label>
        <input id="title" name="title" type="text" value="{{ old('title', $task->title ?? '') }}" required maxlength="255"
            autofocus
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" required maxlength="10000"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $task->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <label for="deadline" class="block text-sm font-medium text-gray-700">Tenggat</label>
        <input id="deadline" name="deadline" type="datetime-local"
            value="{{ old('deadline', isset($task) ? $task->deadline->format('Y-m-d\TH:i') : '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700">Prioritas</label>
            <select id="priority" name="priority" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('priority', $task->priority ?? 'sedang') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach (['belum' => 'Belum', 'proses' => 'Proses', 'selesai' => 'Selesai'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $task->status ?? 'belum') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
        <a href="{{ route('tasks.index') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
            Batal
        </a>
        <button type="submit" @disabled($courses->isEmpty())
            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
            {{ $submitLabel }}
        </button>
    </div>
</div>
