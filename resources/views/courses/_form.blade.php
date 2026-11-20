@csrf

<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Mata Kuliah</label>
        <input id="name" name="name" type="text" value="{{ old('name', $course->name ?? '') }}" required
            maxlength="255" autofocus
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <label for="code" class="block text-sm font-medium text-gray-700">Kode Mata Kuliah</label>
        <input id="code" name="code" type="text" value="{{ old('code', $course->code ?? '') }}" required
            maxlength="20"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <label for="lecturer" class="block text-sm font-medium text-gray-700">Nama Dosen</label>
        <input id="lecturer" name="lecturer" type="text" value="{{ old('lecturer', $course->lecturer ?? '') }}" required
            maxlength="255"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <x-input-error :messages="$errors->get('lecturer')" class="mt-2" />
    </div>

    <div>
        <label for="color" class="block text-sm font-medium text-gray-700">Warna Label</label>
        <div class="mt-1 flex items-center gap-3">
            <input id="color" name="color" type="color" value="{{ old('color', $course->color ?? '#4f46e5') }}" required
                class="h-10 w-14 cursor-pointer rounded border border-gray-300 bg-white p-1">
            <span class="text-sm text-gray-500">Pilih warna untuk label mata kuliah.</span>
        </div>
        <x-input-error :messages="$errors->get('color')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
        <a href="{{ route('courses.index') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
            Batal
        </a>
        <button type="submit"
            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ $submitLabel }}
        </button>
    </div>
</div>
