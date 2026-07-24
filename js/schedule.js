/**
 * AIML AcademicHub - Department Management Portal
 * Lab Schedule Timetable Filter & Print Script
 */

document.addEventListener('DOMContentLoaded', function() {
    const yearSelect = document.getElementById('filterYear');
    const divSelect = document.getElementById('filterDivision');
    const batchSelect = document.getElementById('filterBatch');
    const timetableContainer = document.getElementById('timetableContainer');

    if (!timetableContainer) return;

    function applyScheduleFilter() {
        const selectedDiv = divSelect ? divSelect.value : 'ALL';
        const selectedBatch = batchSelect ? batchSelect.value : 'ALL';

        const scheduleCards = timetableContainer.querySelectorAll('.schedule-card-item');

        scheduleCards.forEach(card => {
            const cardDiv = card.getAttribute('data-division');
            const cardBatch = card.getAttribute('data-batch');

            const matchDiv = (selectedDiv === 'ALL' || cardDiv === selectedDiv);
            const matchBatch = (selectedBatch === 'ALL' || cardBatch === selectedBatch);

            if (matchDiv && matchBatch) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (divSelect) divSelect.addEventListener('change', applyScheduleFilter);
    if (batchSelect) batchSelect.addEventListener('change', applyScheduleFilter);
});

function printTimetable() {
    window.print();
}
