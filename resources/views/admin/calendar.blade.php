@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title">Admin Leave Calendar</h4>
                    <p class="category">View all staff leaves and public holidays</p>
                </div>
                <div class="card-content">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content">
                    <h5>Legend</h5>
                    <div class="d-flex flex-wrap">
                        <span class="badge-legend" style="background: #4caf50;">🎉 Holiday</span>
                        <span class="badge-legend" style="background: #2196F3;">Annual Leave</span>
                        <span class="badge-legend" style="background: #ff9800;">Sick Leave</span>
                        <span class="badge-legend" style="background: #e91e63;">Maternity Leave</span>
                        <span class="badge-legend" style="background: #9c27b0;">Paternity Leave</span>
                        <span class="badge-legend" style="background: #00bcd4;">Examinations</span>
                        <span class="badge-legend" style="background: #795548;">Sports</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
        }

        .badge-legend {
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            margin: 5px;
            font-size: 12px;
        }

        .d-flex {
            display: flex;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 3px;
            padding: 2px 5px;
        }
    </style>

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: function (info, successCallback, failureCallback) {
                    // Reuse the existing events endpoint for now
                    fetch('/calendar/events?start=' + info.startStr + '&end=' + info.endStr)
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(error => {
                            console.error('Error fetching events:', error);
                            failureCallback(error);
                        });
                },
                eventClick: function (info) {
                    if (info.event.extendedProps.type === 'leave') {
                        // Redirect to Admin view of leave? Or just show details.
                        // Keeping user experience simple for now.
                    }
                },
                height: 'auto'
            });
            calendar.render();
        });
    </script>
@endsection