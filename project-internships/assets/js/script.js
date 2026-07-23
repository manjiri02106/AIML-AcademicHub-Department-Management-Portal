/**
 * Academic Hub - Projects & Internships Interactive Script
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Live Table Search Filter
    const searchInputs = document.querySelectorAll('.table-search-input');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const targetTableId = e.target.getAttribute('data-table');
            const table = document.getElementById(targetTableId);
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });

    // 2. Dropdown Filter (Status, Department, etc.)
    const filterSelects = document.querySelectorAll('.table-filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            const filterValue = e.target.value.toLowerCase();
            const colIndex = parseInt(e.target.getAttribute('data-col'), 10);
            const targetTableId = e.target.getAttribute('data-table');
            const table = document.getElementById(targetTableId);

            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cell = row.cells[colIndex];
                    if (!cell) return;
                    const cellText = cell.textContent.trim().toLowerCase();
                    if (filterValue === '' || cellText.includes(filterValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });

    // 3. Modal Handlers
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    };

    // Close modal when clicking outside modal box
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });

    // 4. Progress Percentage Sync
    const progressRange = document.getElementById('progress_range');
    const progressValueDisplay = document.getElementById('progress_value');
    if (progressRange && progressValueDisplay) {
        progressRange.addEventListener('input', (e) => {
            progressValueDisplay.textContent = e.target.value + '%';
        });
    }

    // 5. Export Table to CSV / Excel
    window.exportTableToCSV = function(tableId, filename = 'report.csv') {
        const table = document.getElementById(tableId);
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            let rowData = [];
            const cols = row.querySelectorAll('th, td');
            cols.forEach(col => {
                // Ignore action buttons column if marked with .no-export
                if (!col.classList.contains('no-export')) {
                    let data = col.innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""');
                    rowData.push('"' + data + '"');
                }
            });
            csv.push(rowData.join(','));
        });

        const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    };

    // 6. Print Page Trigger
    window.printReport = function() {
        window.print();
    };
});
