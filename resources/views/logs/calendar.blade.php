<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('カレンダー') }}
            </h2>
            <a href="{{ route('logs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新しいログ
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ja',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                height: 'auto',
                firstDay: 1, // 月曜日を週の開始日に
                events: '{{ route("api.calendar.events") }}',
                eventSourceFailure: function() {
                    alert('ログの読み込みに失敗しました');
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },
                dateClick: function(info) {
                    // 日付クリック時の処理 - ログ作成画面に遷移
                    var createUrl = '{{ route("logs.create") }}?date=' + info.dateStr;
                    window.location.href = createUrl;
                },
                eventContent: function(arg) {
                    return {
                        html: '<div class="fc-event-title-container"><div class="fc-event-title fc-sticky">' + 
                              arg.event.title + '</div></div>'
                    };
                },
                eventDidMount: function(info) {
                    // ツールチップ表示
                    var tags = info.event.extendedProps.tags;
                    if (tags && tags.length > 0) {
                        info.el.title = 'タグ: ' + tags.join(', ');
                    }
                }
            });
            
            calendar.render();
        });
    </script>

    <style>
        /* カスタムスタイル */
        .fc-event {
            cursor: pointer;
            border-radius: 3px;
            padding: 1px 3px;
            font-size: 11px;
        }
        
        .fc-day-today {
            background-color: #fef3c7 !important;
        }
        
        .fc-button-primary {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
        }
        
        .fc-button-primary:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
        
        .fc-event-title {
            font-size: 11px;
            padding: 1px 2px;
        }

        .fc-daygrid-day:hover {
            background-color: #f8fafc;
            cursor: pointer;
        }
    </style>
</x-app-layout>