<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('rooms.show', $room) }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kalendar - {{ $room->name }}
                </h2>
            </div>
            @if($room->status === 'available')
                <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-calendar-plus mr-2"></i>Booking Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md p-6">
            <!-- Calendar will be rendered here -->
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Calendar Scripts -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                events: function(info, successCallback, failureCallback) {
                    fetch(`{{ route('api.calendar.room-events', $room) }}?start=${info.startStr}&end=${info.endStr}`)
                        .then(response => response.json())
                        .then(data => {
                            const events = data.map(booking => {
                                let color = '#6366f1'; // indigo
                                if (booking.status === 'pending') color = '#f59e0b'; // yellow
                                else if (booking.status === 'confirmed') color = '#10b981'; // green
                                else if (booking.status === 'cancelled') color = '#ef4444'; // red
                                
                                return {
                                    id: booking.id,
                                    title: booking.title,
                                    start: booking.booking_date + 'T' + booking.start_time,
                                    end: booking.booking_date + 'T' + booking.end_time,
                                    backgroundColor: color,
                                    borderColor: color,
                                    extendedProps: {
                                        status: booking.status,
                                        user: booking.user.name,
                                        participant_count: booking.participant_count
                                    }
                                };
                            });
                            successCallback(events);
                        })
                        .catch(error => {
                            console.error('Error fetching events:', error);
                            failureCallback(error);
                        });
                },
                eventClick: function(info) {
                    window.location.href = `{{ route('bookings.show', '') }}/${info.event.id}`;
                },
                eventMouseEnter: function(info) {
                    // Show tooltip
                    const tooltip = document.createElement('div');
                    tooltip.className = 'absolute bg-gray-900 text-white p-2 rounded shadow-lg text-sm z-50';
                    tooltip.innerHTML = `
                        <div class="font-medium">${info.event.title}</div>
                        <div>Status: ${info.event.extendedProps.status}</div>
                        <div>Oleh: ${info.event.extendedProps.user}</div>
                        <div>Peserta: ${info.event.extendedProps.participant_count} orang</div>
                    `;
                    document.body.appendChild(tooltip);
                    
                    // Position tooltip
                    const rect = info.el.getBoundingClientRect();
                    tooltip.style.left = rect.left + 'px';
                    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                    
                    info.el._tooltip = tooltip;
                },
                eventMouseLeave: function(info) {
                    if (info.el._tooltip) {
                        document.body.removeChild(info.el._tooltip);
                        delete info.el._tooltip;
                    }
                },
                dateClick: function(info) {
                    if (info.date >= new Date().setHours(0,0,0,0)) {
                        window.location.href = `{{ route('bookings.create', ['room_id' => $room->id]) }}?booking_date=${info.dateStr}`;
                    }
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>