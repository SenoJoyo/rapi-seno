@extends('layouts.app')
@section('title', 'Kalender Agenda')

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <style>
        #calendar {
            --fc-border-color: rgba(30, 59, 51, .12);
            --fc-button-bg-color: #1E3B33;
            --fc-button-border-color: #1E3B33;
            --fc-button-hover-bg-color: #2a4d43;
            --fc-button-hover-border-color: #2a4d43;
            --fc-button-active-bg-color: #15302a;
            --fc-button-active-border-color: #15302a;
            --fc-today-bg-color: rgba(240, 201, 74, .28);
            --fc-event-bg-color: #1E3B33;
            --fc-event-border-color: #1E3B33;
            --fc-list-event-hover-bg-color: rgba(30, 59, 51, .06);
            font-family: inherit;
        }
        #calendar .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; }
        #calendar .fc-button { font-size: .8rem; text-transform: none; }
    </style>
@endpush

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Kalender agenda sekolah</h1>
    <div id="calendar" class="card p-3 sm:p-4"></div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/id.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var isMobile = window.innerWidth < 640;
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                locale: 'id',
                initialView: isMobile ? 'listMonth' : 'dayGridMonth',
                height: 'auto',
                editable: false,
                selectable: false,
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                events: '/api/calendar-feed',
            });
            calendar.render();
        });
    </script>
@endpush
