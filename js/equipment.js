/**
 * AIML AcademicHub - Department Management Portal
 * Equipment Records Management Script
 */

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchEquipmentInput');
    const categorySelect = document.getElementById('filterCategorySelect');
    const statusSelect = document.getElementById('filterStatusSelect');
    const labSelect = document.getElementById('filterLabSelect');
    const equipmentTable = document.getElementById('equipmentTable');
    
    if (!equipmentTable) return;

    let currentPage = 1;
    const rowsPerPage = 10;

    function filterTable() {
        const rows = equipmentTable.querySelectorAll('tbody tr');
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedCat = categorySelect ? categorySelect.value : '';
        const selectedStatus = statusSelect ? statusSelect.value : '';
        const selectedLab = labSelect ? labSelect.value : '';

        let visibleRows = [];

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const code = row.getAttribute('data-code') || '';
            const category = row.getAttribute('data-category') || '';
            const status = row.getAttribute('data-status') || '';
            const lab = row.getAttribute('data-lab') || '';

            const matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
            const matchesCat = !selectedCat || category === selectedCat;
            const matchesStatus = !selectedStatus || status === selectedStatus;
            const matchesLab = !selectedLab || lab === selectedLab;

            if (matchesSearch && matchesCat && matchesStatus && matchesLab) {
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });

        // Pagination rendering
        const totalPages = Math.ceil(visibleRows.length / rowsPerPage) || 1;
        if (currentPage > totalPages) currentPage = 1;

        visibleRows.forEach((row, index) => {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            if (index >= start && index < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        updatePaginationControls(visibleRows.length, totalPages);
    }

    function updatePaginationControls(totalVisible, totalPages) {
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');

        if (paginationInfo) {
            const start = totalVisible === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
            const end = Math.min(currentPage * rowsPerPage, totalVisible);
            paginationInfo.textContent = `Showing ${start} to ${end} of ${totalVisible} equipment entries`;
        }

        if (paginationControls) {
            let html = '';
            html += `<button class="page-btn ${currentPage === 1 ? 'disabled' : ''}" onclick="changeEquipmentPage(${currentPage - 1})"><i class="fa-solid fa-chevron-left"></i></button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changeEquipmentPage(${i})">${i}</button>`;
            }

            html += `<button class="page-btn ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}" onclick="changeEquipmentPage(${currentPage + 1})"><i class="fa-solid fa-chevron-right"></i></button>`;
            paginationControls.innerHTML = html;
        }
    }

    window.changeEquipmentPage = function(page) {
        if (page < 1) return;
        currentPage = page;
        filterTable();
    };

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (categorySelect) categorySelect.addEventListener('change', filterTable);
    if (statusSelect) statusSelect.addEventListener('change', filterTable);
    if (labSelect) labSelect.addEventListener('change', filterTable);

    // Initial filter run
    filterTable();
});

// Equipment View/Edit Modal Populate Helpers
function viewEquipmentDetails(data) {
    document.getElementById('viewEqCode').textContent = data.code;
    document.getElementById('viewEqName').textContent = data.name;
    document.getElementById('viewEqCategory').textContent = data.category;
    document.getElementById('viewEqLab').textContent = data.lab;
    document.getElementById('viewEqPurchase').textContent = data.purchase;
    document.getElementById('viewEqWarranty').textContent = data.warranty;
    document.getElementById('viewEqQty').textContent = data.quantity;
    document.getElementById('viewEqStatus').textContent = data.status;
    openModal('viewEquipmentModal');
}

function openEditEquipmentModal(data) {
    document.getElementById('editEqId').value = data.id;
    document.getElementById('editEqCode').value = data.code;
    document.getElementById('editEqName').value = data.name;
    document.getElementById('editEqCategory').value = data.category;
    document.getElementById('editEqLab').value = data.labId;
    document.getElementById('editEqPurchase').value = data.purchase;
    document.getElementById('editEqWarranty').value = data.warranty;
    document.getElementById('editEqQuantity').value = data.quantity;
    document.getElementById('editEqStatus').value = data.status;
    openModal('editEquipmentModal');
}

function confirmDeleteEquipment(id, name) {
    if (confirm(`Are you sure you want to delete equipment "${name}"?`)) {
        window.location.href = `equipment.php?action=delete&id=${id}`;
    }
}
