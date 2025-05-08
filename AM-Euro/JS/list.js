// This file contains all client-side logic for the Computer Inventory Management List page.

// Functions in this file handle:
// - Table filtering, searching, and row selection
// - Modal handling for add/edit/delete/history
// - AJAX requests to backend for CRUD operations
// - Table refresh and UI updates (pagination, highlighting, etc.)
// - Form validation and error handling
// - Modern pagination and summary display

// All user interactions and dynamic updates for the list page are managed here.

document.addEventListener('DOMContentLoaded', function() {
    // Sets loading state (disables/enables button and adds/removes loading class)
    const setLoading = (el, loading) => {
        el.classList.toggle('loading', loading);
        el.disabled = loading;
    };

    // Validates required fields in a form and displays error messages
    // (Disabled: allow empty fields)
    const validateForm = (form) => true;

    // Returns a debounced version of a function (delays execution)
    const debounce = (fn, delay) => {
        let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), delay); };
    };

    // DOM
    const computerModal = document.getElementById('computerModal');
    const deleteModal = document.getElementById('deleteModal');
    const modalOverlay = document.querySelector('.modal-overlay');
    const computerForm = document.getElementById('computerForm');
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const lastUpdatedFrom = document.getElementById('lastUpdatedFrom');
    const lastUpdatedTo = document.getElementById('lastUpdatedTo');
    const computersTable = document.getElementById('computersTable');
    const editCommentGroup = document.getElementById('editCommentGroup');
    const editComment = document.getElementById('editComment');
    const paginationBar = document.getElementById('pagination');
    let selectedRow = null, lastUpdatedRow = null, currentPage = 1, totalPages = 1;

    // Updates the enabled/disabled state of action buttons based on row selection
    function updateButtonStates() {
        document.querySelectorAll('.action-buttons button:not(#addComputerBtn)').forEach(b => b.disabled = !selectedRow);
    }

    // Handles table row selection for highlighting and enabling actions
    computersTable.addEventListener('click', e => {
        const row = e.target.closest('tr');
        if (!row || !row.dataset.id || row.classList.contains('no-data')) return;
        if (selectedRow) selectedRow.classList.remove('selected');
        row.classList.add('selected');
        selectedRow = row;
        updateButtonStates();
    });

    // Shows a modal dialog and disables background scrolling
    function showModal(modal) {
        document.body.style.overflow = 'hidden';
        modal.style.display = modalOverlay.style.display = 'block';
        const body = modal.querySelector('.modal-body');
        if (body) body.scrollTop = 0;
        modal.offsetHeight;
        setTimeout(() => { modal.classList.add('show'); modalOverlay.classList.add('show'); }, 10);
    }
    // Hides a modal dialog and re-enables background scrolling
    function closeModal(modal) {
        modal.classList.remove('show');
        modalOverlay.classList.remove('show');
        setTimeout(() => {
            modal.style.display = modalOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
    // Closes all modals when overlay is clicked
    modalOverlay.addEventListener('click', () => document.querySelectorAll('.modal').forEach(closeModal));

    // Opens the Add Computer modal and resets the form
    document.getElementById('addComputerBtn').addEventListener('click', () => {
        computerForm.reset();
        document.getElementById('modalTitle').textContent = 'Add New Computer';
        document.getElementById('computerId').value = '';
        document.getElementById('saveBtn').textContent = 'Add Computer';
        if (editCommentGroup) editCommentGroup.style.display = 'none';
        showModal(computerModal);
    });

    // Opens the Edit Computer modal and populates it with selected row data
    document.querySelector('.edit-btn').addEventListener('click', () => {
        if (!selectedRow) return;
        fetch('listbackend.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=get&id=${selectedRow.dataset.id}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const c = data.computer;
                document.getElementById('modalTitle').textContent = 'Edit Computer';
                document.getElementById('computerId').value = c.computer_No;
                // Set department dropdown value
                document.getElementById('department').value = c.department;
                document.getElementById('user').value = c.user;
                document.getElementById('computerName').value = c.computer_name;
                document.getElementById('ip').value = c.ip;
                document.getElementById('processor').value = c.processor;
                document.getElementById('mobo').value = c.MOBO;
                document.getElementById('powerSupply').value = c.power_supply;
                document.getElementById('ram').value = c.ram;
                document.getElementById('ssd').value = c.SSD;
                document.getElementById('os').value = c.OS;
                document.getElementById('deploymentDate').value = c.deployment_date || '';
                document.getElementById('saveBtn').textContent = 'Save Changes';
                if (editCommentGroup) editCommentGroup.style.display = '';
                if (editComment) editComment.value = '';
                showModal(computerModal);
            }
        });
    });

    // Handles form submission for adding or editing a computer
    computerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!validateForm(this)) return;
        const saveBtn = document.getElementById('saveBtn');
        setLoading(saveBtn, true);
        try {
            const formData = new FormData(this);
            // Ensure deployment_date is included (for some browsers, empty date fields may not be sent)
            const deploymentDateInput = document.getElementById('deploymentDate');
            if (deploymentDateInput && !formData.has('deployment_date')) {
                formData.append('deployment_date', deploymentDateInput.value || '');
            }
            const computerId = formData.get('computer_No');
            formData.set('action', computerId ? 'update' : 'add');
            if (computerId && editComment && editComment.value.trim()) {
                formData.set('comment', editComment.value.trim());
            } else {
                formData.delete('comment');
            }
            const response = await fetch('listbackend.php', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                closeModal(computerModal);
                refreshTable();
                if (data.id || computerId) highlightUpdatedRow(data.id || computerId);
            }
        } finally {
            setLoading(saveBtn, false);
        }
    });

    // Opens the Delete Computer modal
    document.querySelector('.delete-btn').addEventListener('click', () => {
        if (!selectedRow) return;
        showModal(deleteModal);
    });
    // Handles confirmation of computer deletion
    document.querySelector('.btn-confirm').addEventListener('click', () => {
        if (!selectedRow) return;
        const computerId = selectedRow.dataset.id;
        const comment = document.getElementById('deleteComment').value;
        fetch('listbackend.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=delete&id=${computerId}&comment=${encodeURIComponent(comment)}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal(deleteModal);
                refreshTable();
                selectedRow = null;
                updateButtonStates();
            }
        });
    });

    // Opens the History modal for the selected computer
    document.querySelector('.history-btn').addEventListener('click', function() {
        const row = document.querySelector('#computersTable tr.selected');
        if (row) showHistory(row.dataset.id);
    });
    // Fetches and displays the change history for a computer
    function showHistory(computerId) {
        fetch('listbackend.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=getHistory&id=${computerId}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                displayHistoryComparison(data);
                showModal(document.getElementById('historyModal'));
            }
        });
    }

    // Displays a side-by-side comparison of previous and current computer data in the history modal
    function displayHistoryComparison(data) {
        const prevDiv = document.querySelector('.previous-version .version-details');
        const currDiv = document.querySelector('.current-version .version-details');
        const commentDiv = document.getElementById('historyChangeComment');
        const timestampSpan = document.getElementById('historyTimestamp');
        let previousHtml = '', currentHtml = '';
        const previousData = data.previous || (data.history[0] && data.history[0].changes && data.history[0].changes.previous) || {};
        const currentData = data.current || {};
        const latestHistory = data.history[0] || {};
        const fields = ['department','user','computer_name','ip','processor','MOBO','power_supply','ram','SSD','OS','deployment_date'];
        fields.forEach(field => {
            const changed = previousData[field] !== currentData[field] ? 'changed-field highlight-history' : '';
            let prevValue = previousData[field] || 'N/A';
            let currValue = currentData[field] || 'N/A';
            if (field === 'deployment_date') {
                prevValue = prevValue && prevValue !== 'N/A' ? new Date(prevValue).toLocaleDateString() : 'N/A';
                currValue = currValue && currValue !== 'N/A' ? new Date(currValue).toLocaleDateString() : 'N/A';
            }
            previousHtml += `<div class="change-field ${changed}"><span class="field-name">${formatFieldName(field)}:</span><span class="field-value">${prevValue}</span></div>`;
            currentHtml += `<div class="change-field ${changed}"><span class="field-name">${formatFieldName(field)}:</span><span class="field-value">${currValue}</span></div>`;
        });
        prevDiv.innerHTML = previousHtml;
        currDiv.innerHTML = currentHtml;
        if (data.timestamp) {
            const date = new Date(data.timestamp);
            timestampSpan.textContent = `Last Change: ${date.toLocaleString()}`;
        } else {
            timestampSpan.textContent = '';
        }
        commentDiv.innerHTML = `<strong>Change Reason:</strong> <span>${latestHistory.comment && latestHistory.comment.trim() ? latestHistory.comment : 'No comment provided.'}</span>`;
    }

    // Highlights the row of a recently updated computer
    function highlightUpdatedRow(computerId) {
        const row = document.querySelector(`tr[data-id="${computerId}"]`);
        if (row) {
            if (lastUpdatedRow) lastUpdatedRow.classList.remove('highlight-update');
            row.classList.add('highlight-update');
            lastUpdatedRow = row;
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Fetches and refreshes the table data based on filters and pagination
    function refreshTable() {
        const searchValue = searchInput.value;
        const departmentValue = departmentFilter.value;
        const fromDate = lastUpdatedFrom.value;
        const toDate = lastUpdatedTo.value;
        let bodyData = `action=list&page=${currentPage}&limit=10&search=${encodeURIComponent(searchValue)}&department=${encodeURIComponent(departmentValue)}`;
        if (fromDate) bodyData += `&last_updated_from=${encodeURIComponent(fromDate)}`;
        if (toDate) bodyData += `&last_updated_to=${encodeURIComponent(toDate)}`;
        fetch('listbackend.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: bodyData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateTable(data.computers);
                totalPages = data.pages || 1;
                renderPagination(currentPage, totalPages, data.total || 0);
            }
        });
    }

    // Updates the table body with new computer data
    // Only renders rows with a valid computer_No (ID)
    function updateTable(computers) {
        const tbody = computersTable.querySelector('tbody');
        tbody.innerHTML = '';
        if (computers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="12" class="no-data">No computers found</td></tr>';
            return;
        }
        const fields = ['department','user','computer_name','ip','processor','MOBO','power_supply','ram','SSD','OS'];
        computers.forEach(computer => {
            if (!computer.computer_No) return; // Skip if no valid ID
            const row = document.createElement('tr');
            row.dataset.id = computer.computer_No;
            let html = `<td>${computer.computer_No}</td>`;
            const prev = computer.history_previous || {};
            const curr = computer.history_new || computer;
            fields.forEach(field => {
                let changed = false;
                if (computer.history_previous && computer.history_new) {
                    changed = String(prev[field] ?? '') !== String(curr[field] ?? '');
                }
                html += `<td${changed ? ' class="highlight-history"' : ''}>${curr[field] !== undefined && curr[field] !== null ? curr[field] : (computer[field] || '')}</td>`;
            });
            html += `<td>${computer.formatted_date || computer.last_updated || 'N/A'}</td>`;
            row.innerHTML = html;
            tbody.appendChild(row);
        });
    }

    // Formats a field name from snake_case to Title Case for display
    function formatFieldName(field) {
        return field.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    }

    // Renders the pagination controls and summary below the table
    function renderPagination(page, pages, total = 0) {
        paginationBar.innerHTML = '';
        if (pages <= 1) return;
        const pagContainer = document.createElement('div');
        pagContainer.className = 'pagination-modern';
        const prevBtn = document.createElement('button');
        prevBtn.textContent = '⟨';
        prevBtn.className = 'pag-btn prev';
        prevBtn.disabled = page === 1;
        prevBtn.onclick = () => goToPage(page - 1);
        pagContainer.appendChild(prevBtn);
        let start = Math.max(1, page - 2);
        let end = Math.min(pages, page + 2);
        if (page <= 3) { start = 1; end = Math.min(5, pages); }
        if (page >= pages - 2) { end = pages; start = Math.max(1, pages - 4); }
        if (start > 1) {
            const firstBtn = document.createElement('button');
            firstBtn.textContent = '1';
            firstBtn.className = 'pag-btn';
            firstBtn.onclick = () => goToPage(1);
            pagContainer.appendChild(firstBtn);
            if (start > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'pag-ellipsis';
                pagContainer.appendChild(ellipsis);
            }
        }
        for (let i = start; i <= end; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = 'pag-btn' + (i === page ? ' active' : '');
            btn.onclick = () => goToPage(i);
            pagContainer.appendChild(btn);
        }
        if (end < pages) {
            if (end < pages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'pag-ellipsis';
                pagContainer.appendChild(ellipsis);
            }
            const lastBtn = document.createElement('button');
            lastBtn.textContent = pages;
            lastBtn.className = 'pag-btn';
            lastBtn.onclick = () => goToPage(pages);
            pagContainer.appendChild(lastBtn);
        }
        const nextBtn = document.createElement('button');
        nextBtn.textContent = '⟩';
        nextBtn.className = 'pag-btn next';
        nextBtn.disabled = page === pages;
        nextBtn.onclick = () => goToPage(page + 1);
        pagContainer.appendChild(nextBtn);
        paginationBar.appendChild(pagContainer);
        let summary = document.getElementById('pagination-summary');
        if (!summary) {
            summary = document.createElement('div');
            summary.id = 'pagination-summary';
            summary.className = 'pagination-summary';
            paginationBar.parentNode.insertBefore(summary, paginationBar.nextSibling);
        }
        const startIdx = total === 0 ? 0 : (page - 1) * 10 + 1;
        const endIdx = total === 0 ? 0 : Math.min(page * 10, total);
        summary.textContent = `Showing ${startIdx}-${endIdx} of ${total}`;
    }

    // Changes the current page and refreshes the table
    function goToPage(page) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        refreshTable();
    }

    // Event listeners for search and filter inputs to refresh the table
    searchInput.addEventListener('input', debounce(refreshTable, 300));
    departmentFilter.addEventListener('change', refreshTable);
    lastUpdatedFrom.addEventListener('change', refreshTable);
    lastUpdatedTo.addEventListener('change', refreshTable);

    // Closes modals when close/cancel buttons are clicked
    document.querySelectorAll('.close, .btn-cancel, #cancelBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });

    // Handles printing the table only
    const printBtn = document.getElementById('printTableBtn');
    if (printBtn) printBtn.onclick = e => { e.preventDefault(); window.print(); };

    // Initial UI setup
    updateButtonStates();
    renderPagination(currentPage, totalPages);
    refreshTable(); // Fetch and display the first 10 records on page load

    // Injects custom styles for highlights and pagination
    const style = document.createElement('style');
    style.innerHTML = `.highlight-history { background: #e6ffe6 !important; border-left: 4px solid #4CAF50; }\n.pagination-modern { display: flex; gap: 4px; align-items: center; justify-content: center; margin: 10px 0; flex-wrap: wrap; }\n.pag-btn { background: #fff; border: 1px solid #ccc; border-radius: 4px; padding: 4px 10px; margin: 0 2px; cursor: pointer; transition: background 0.2s, color 0.2s; font-size: 1rem; }\n.pag-btn.active, .pag-btn:hover:not(:disabled) { background: #1976d2; color: #fff; border-color: #1976d2; }\n.pag-btn:disabled { opacity: 0.5; cursor: not-allowed; }\n.pag-ellipsis { padding: 0 6px; color: #888; font-size: 1.1em; }\n.pagination-summary { text-align: center; color: #555; font-size: 0.95em; margin-bottom: 10px; }`;
    document.head.appendChild(style);
});