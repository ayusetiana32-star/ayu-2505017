<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Kalender Deadline</h2>
                <p class="mt-1 text-sm text-gray-500">Klik event untuk melihat detail tugas.</p>
            </div>
            <a href="{{ route('tasks.index') }}"
                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                Daftar tugas
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                <div id="calendar" aria-label="Kalender deadline tugas"></div>
                <p id="calendar-error" role="alert" class="mt-4 hidden text-sm text-red-600">
                    Kalender gagal memuat tugas. Silakan muat ulang halaman.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarElement = document.getElementById('calendar');
            const errorElement = document.getElementById('calendar-error');
            const calendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 'auto',
                dayMaxEvents: true,
                nowIndicator: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek',
                },
                events: @json(route('tasks.events')),
                eventClick: function (info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.assign(info.event.url);
                    }
                },
                eventSourceFailure: function () {
                    errorElement.classList.remove('hidden');
                },
            });

            calendar.render();
        });
    </script>
</x-app-layout>
