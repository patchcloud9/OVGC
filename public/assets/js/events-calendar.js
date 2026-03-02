/**
 * FullCalendar initialisation for the public events calendar.
 * Loaded on /events page only.
 *
 * Responsive behaviour:
 *   < 768px  → starts in listMonth (readable on phones)
 *   ≥ 768px  → starts in dayGridMonth (full month grid)
 * The Month / List toggle lets users switch at any time.
 */
document.addEventListener('DOMContentLoaded', function () {
    var calEl = document.getElementById('events-calendar');
    if (!calEl) return;

    var isMobile = window.innerWidth < 768;

    var cal = new FullCalendar.Calendar(calEl, {
        initialView: isMobile ? 'listMonth' : 'dayGridMonth',
        fixedWeekCount: false,
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,listMonth'
        },
        buttonText: {
            dayGridMonth: 'Month',
            listMonth:    'List'
        },
        noEventsText: 'No events this month.',
        height: 'auto',
        events: {
            url: '/events/feed',
            method: 'GET',
            failure: function () {
                alert('There was an error loading events. Please try refreshing the page.');
            }
        },
        eventDidMount: function (info) {
            var status = info.event.extendedProps.status;
            if (status === 'cancelled') {
                info.el.style.opacity = '0.55';
                info.el.style.textDecoration = 'line-through';

                var titleEl = info.el.querySelector('.fc-event-title');
                if (titleEl) {
                    var badge = document.createElement('span');
                    badge.className = 'ev-cancelled-badge';
                    badge.textContent = 'Cancelled';
                    titleEl.appendChild(badge);
                }
            }
        },
        eventClick: function (info) {
            var url = info.event.extendedProps.detailUrl;
            if (url) {
                window.location.href = url;
            }
        }
    });

    cal.render();
});
