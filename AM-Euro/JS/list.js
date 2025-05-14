// This file contains all client-side logic for the Computer Inventory Management List page.

// Functions in this file handle:
// - Table filtering, searching, and row selection
// - Modal handling for add/edit/delete/history
// - AJAX requests to backend for CRUD operations
// - Table refresh and UI updates (pagination, highlighting, etc.)
// - Form validation and error handling
// - Modern pagination and summary display


document.addEventListener('DOMContentLoaded', function() {
    // Cache for storing computer data
    const computerCache = new Map();
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes in milliseconds
    
    // Function to get data from cache or fetch if expired
    async function getCachedData(key, fetchFn) {
        const cached = computerCache.get(key);
        if (cached && Date.now() - cached.timestamp < CACHE_DURATION) {
            return cached.data;
        }
        
        const data = await fetchFn();
        computerCache.set(key, {
            data: data,
            timestamp: Date.now()
        });
        return data;
    }
    
    // Function to clear cache for a specific key or all cache
    function clearCache(key = null) {
        if (key) {
            computerCache.delete(key);
        } else {
            computerCache.clear();
        }
    }

    // Sets loading state (disables/enables button and adds/removes loading class)
    const setLoading = (el, loading) => {
        el.classList.toggle('loading', loading);
        el.disabled = loading;
    };

    // Validates required fields in a form and displays error messages
    const validateForm = (form) => {
        // Required fields for a computer record
        const requiredFields = ['department', 'computer_name', 'ip'];
        let isValid = true;
        
        // Remove any existing error messages
        form.querySelectorAll('.form-error').forEach(el => el.remove());
        
        // Check each required field
        requiredFields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input && input.value.trim() === '') {
                isValid = false;
                const errorMsg = document.createElement('div');
                errorMsg.className = 'form-error';
                errorMsg.textContent = 'This field is required';
                input.parentNode.appendChild(errorMsg);
                input.classList.add('error');
            } else if (input) {
                input.classList.remove('error');
            }
        });
        
        return isValid;
    };

    // Returns a debounced version of a function (delays execution)
    const debounce = (fn, delay) => {
        let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), delay); };
    };

    // DOM
    const computerModal = document.getElementById('computerModal');
    const deleteModal = document.getElementById('deleteModal');
    const deactivateModal = document.getElementById('deactivateModal');
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
    let selectedRow = null, lastUpdatedRow = null, currentPage = 1, totalPages = 1, highlightTimeoutId = null;

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
        console.log('Opening modal:', modal.id);
        document.body.style.overflow = 'hidden';
        modal.style.display = 'block';
        modalOverlay.style.display = 'block';
        const body = modal.querySelector('.modal-body');
        if (body) body.scrollTop = 0;
        // Force reflow before adding the show class
        void modal.offsetHeight;
        setTimeout(() => { 
            modal.classList.add('show'); 
            modalOverlay.classList.add('show');
            console.log('Modal shown:', modal.id);
        }, 10);
    }
    
    // Hides a modal dialog and re-enables background scrolling
    function closeModal(modal) {
        console.log('Closing modal:', modal.id);
        modal.classList.remove('show');
        modalOverlay.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            modalOverlay.style.display = 'none';
            document.body.style.overflow = '';
            console.log('Modal hidden:', modal.id);
        }, 300);
    }
    
    // Closes all modals when overlay is clicked
    modalOverlay.addEventListener('click', () => {
        console.log('Overlay clicked, closing all modals');
        document.querySelectorAll('.modal').forEach(closeModal);
    });

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
                document.getElementById('machineType').value = c.Machine_type || '';
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
        console.log('Form submitted');
        
        if (!validateForm(this)) {
            console.log('Form validation failed');
            return;
        }
        
        const saveBtn = document.getElementById('saveBtn');
        setLoading(saveBtn, true);
        
        try {
            const formData = new FormData(this);
            
            // Log form data for debugging
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
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
            
            console.log('Sending request to backend...');
            const response = await fetch('listbackend.php', { 
                method: 'POST', 
                body: formData 
            });
            
            console.log('Response received');
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON from backend:', text);
                alert('Server error: ' + text);
                return;
            }
            console.log('Response data:', data);
            
            if (data.success) {
                console.log('Operation successful');
                closeModal(computerModal);
                // Pass the computer ID to refreshTable to highlight it after refresh
                refreshTable(data.id || computerId);
            } else {
                console.error('Operation failed:', data.message);
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Exception during form submission:', error);
            alert('An error occurred: ' + error.message);
        } finally {
            setLoading(saveBtn, false);
        }
    });

    // Opens the Deactivate Computer modal
    document.querySelector('.deactivate-btn').addEventListener('click', () => {
        if (!selectedRow) {
            console.log('No row selected for deactivation');
            return;
        }
        console.log('Opening deactivate modal for row:', selectedRow.dataset.id);
        showModal(deactivateModal);
    });
    
    // Handles confirmation of computer deactivation
    document.querySelector('.btn-confirm').addEventListener('click', async () => {
        if (!selectedRow) {
            console.log('No row selected for deactivation confirmation');
            return;
        }
        
        try {
            const computerId = selectedRow.dataset.id;
            const comment = document.getElementById('deactivateComment').value;
            console.log('Deactivating computer ID:', computerId, 'Comment:', comment);
            
            const response = await fetch('listbackend.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=deactivate&id=${computerId}&comment=${encodeURIComponent(comment)}`
            });
            
            const data = await response.json();
            console.log('Deactivate response:', data);
            
            if (data.success) {
                console.log('Deactivation successful');
                closeModal(deactivateModal);
                refreshTable();
                selectedRow = null;
                updateButtonStates();
            } else {
                console.error('Deactivation failed:', data.message);
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Exception during deactivation:', error);
            alert('An error occurred during deactivation: ' + error.message);
        }
    });

    // Opens the History modal for the selected computer
    document.querySelector('.history-btn').addEventListener('click', function() {
        const row = document.querySelector('#computersTable tr.selected');
        if (!row) {
            console.log('No row selected for history view');
            return;
        }
        console.log('Viewing history for computer ID:', row.dataset.id);
        showHistory(row.dataset.id);
    });
    
    // Fetches and displays the change history for a computer
    async function showHistory(computerId) {
        const cacheKey = `history_${computerId}`;
        
        try {
            const data = await getCachedData(cacheKey, async () => {
                const response = await fetch('listbackend.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=getHistory&id=${computerId}`
                });
                return await response.json();
            });
            
            if (data.success) {
                displayHistoryComparison(data);
                const historyModal = document.getElementById('historyModal');
                showModal(historyModal);
            } else {
                console.error('History fetch failed:', data.message);
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Exception during history fetch:', error);
            alert('An error occurred while retrieving history: ' + error.message);
        }
    }

    // Displays a side-by-side comparison of previous and current computer data in the history modal
    function displayHistoryComparison(data) {
        const timelineContainer = document.getElementById('historyTimeline');
        const prevDiv = document.querySelector('.previous-version .version-details');
        const currDiv = document.querySelector('.current-version .version-details');
        const timestampSpan = document.getElementById('historyTimestamp');

        
        // Clear previous content
        timelineContainer.innerHTML = '';
        prevDiv.innerHTML = '';
        currDiv.innerHTML = '';
        
        // Sort history by timestamp in descending order
        const sortedHistory = data.history.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
        
        // Create timeline entries
        sortedHistory.forEach((entry, index) => {
            const date = new Date(entry.timestamp);
            const formattedDate = date.toLocaleString('en-US', {month: '2-digit', day: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit'});
            
            const timelineEntry = document.createElement('div');
            timelineEntry.className = 'timeline-entry';
            timelineEntry.innerHTML = `
                <div class="timeline-date">${formattedDate}</div>
                <div class="timeline-user">Updated by: ${entry.updated_by}</div>
                ${entry.comment ? `<div class="timeline-comment">${entry.comment}</div>` : ''}
            `;
            
            // Add click handler to show this version
            timelineEntry.addEventListener('click', () => {
                // Remove active class from all entries
                document.querySelectorAll('.timeline-entry').forEach(el => el.classList.remove('active'));
                // Add active class to clicked entry
                timelineEntry.classList.add('active');
                
                // Show the selected version
                const changes = entry.changes;
                displayVersionComparison(changes.previous, changes.new);
                
                // Update timestamp
                timestampSpan.textContent = `Last Change: ${formattedDate}`;
            });
            
            timelineContainer.appendChild(timelineEntry);
        });
        
        // Show the most recent version by default
        if (sortedHistory.length > 0) {
            const latestEntry = timelineContainer.querySelector('.timeline-entry');
            latestEntry.classList.add('active');
            const latestChanges = sortedHistory[0].changes;
            displayVersionComparison(latestChanges.previous, latestChanges.new);
            
            // Update timestamp
            const latestDate = new Date(sortedHistory[0].timestamp);
            timestampSpan.textContent = `Last Change: ${latestDate.toLocaleString('en-US', {month: '2-digit', day: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit'})}`;
        }
    }
    
    // Helper function to display version comparison
    function displayVersionComparison(previousData, currentData) {
        const prevDiv = document.querySelector('.previous-version .version-details');
        const currDiv = document.querySelector('.current-version .version-details');
        
        let previousHtml = '', currentHtml = '';
        const fields = ['department', 'Machine_type', 'user', 'computer_name', 'ip', 'processor', 'MOBO', 'power_supply', 'ram', 'SSD', 'OS', 'MAC_Address', 'deployment_date', 'is_active', 'status_changed_by', 'status_changed_date'];
        
        fields.forEach(field => {
            const changed = previousData[field] !== currentData[field] ? 'highlight-history' : '';
            let prevValue = previousData[field] || 'N/A';
            let currValue = currentData[field] || 'N/A';
            if (field === 'deployment_date') {
                prevValue = prevValue && prevValue !== 'N/A' ? new Date(prevValue).toLocaleDateString('en-US', {month: '2-digit', day: '2-digit', year: '2-digit'}) : 'N/A';
                currValue = currValue && currValue !== 'N/A' ? new Date(currValue).toLocaleDateString('en-US', {month: '2-digit', day: '2-digit', year: '2-digit'}) : 'N/A';
            }
            if (field === 'Machine_type') {
                prevValue = prevValue.charAt(0).toUpperCase() + prevValue.slice(1);
                currValue = currValue.charAt(0).toUpperCase() + currValue.slice(1);
            }
            if (field === 'is_active') {
                prevValue = prevValue === 'Y' ? 'Active' : (prevValue === 'N' ? 'Inactive' : prevValue);
                currValue = currValue === 'Y' ? 'Active' : (currValue === 'N' ? 'Inactive' : currValue);
            }
            previousHtml += `<div class="change-field ${changed}"><span class="field-name">${formatFieldName(field)}:</span><span class="field-value">${prevValue}</span></div>`;
            currentHtml += `<div class="change-field ${changed}"><span class="field-name">${formatFieldName(field)}:</span><span class="field-value">${currValue}</span></div>`;
        });
        prevDiv.innerHTML = previousHtml;
        currDiv.innerHTML = currentHtml;
    }

    // Highlights only the specific cells that were changed in a recently updated computer
    async function highlightUpdatedRow(computerId) {
        const row = document.querySelector(`tr[data-id="${computerId}"]`);
        if (!row) return;
        
        // Clear previous highlights
        clearPreviousHighlights();
        
        // Store this row as the last updated row (but don't add highlight-update class)
        lastUpdatedRow = row;
        
        try {
            // Fetch the history data to identify changed fields
            const response = await fetch('listbackend.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=getHistory&id=${computerId}`
            });
            
            const data = await response.json();
            
            if (data.success && data.history && data.history.length > 0) {
                // Get the most recent history entry
                const latestChange = data.history[0]; // Already sorted by timestamp DESC from server
                const changes = latestChange.changes;
                
                // Highlight only the changed cells
                highlightChangedCells(row, changes);
                
                // Store the highlight information in localStorage
                saveHighlightToStorage(computerId, changes);
            }
        } catch (error) {
            console.error('Error fetching history for highlighting:', error);
        }
        
        // Scroll to the updated row
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Notify the dashboard about the update if it's open in another tab
        notifyDashboardOfUpdate(computerId);
    }
    
    // Helper function to clear previous highlights
    function clearPreviousHighlights() {
        if (highlightTimeoutId) {
            clearTimeout(highlightTimeoutId);
        }
        
        if (lastUpdatedRow) {
            // Only clear cell highlights, not the entire row
            lastUpdatedRow.querySelectorAll('.highlight-cell-change').forEach(cell => {
                cell.classList.remove('highlight-cell-change');
                cell.style.fontWeight = 'normal';
                // Remove the checkmark tooltip
                if (cell.dataset.changed) {
                    delete cell.dataset.changed;
                    delete cell.dataset.oldValue;
                    delete cell.dataset.newValue;
                }
            });
            lastUpdatedRow = null;
        }
    }
    
    // Save highlight information to localStorage
    function saveHighlightToStorage(computerId, changes) {
        // Get current highlights or initialize empty object
        const storedHighlights = JSON.parse(localStorage.getItem('highlightedChanges') || '{}');
        
        // Add this computer's changes
        storedHighlights[computerId] = {
            timestamp: Date.now(),
            changes: changes
        };
        
        // Save back to localStorage
        localStorage.setItem('highlightedChanges', JSON.stringify(storedHighlights));
    }
    
    // Load and apply highlights from localStorage
    function loadHighlightsFromStorage() {
        const storedHighlights = JSON.parse(localStorage.getItem('highlightedChanges') || '{}');
        const currentTime = Date.now();
        const ONE_WEEK = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds - extended from 1 day
        const updatedHighlights = {};
        
        // Apply highlights for each stored computer ID
        Object.keys(storedHighlights).forEach(computerId => {
            const highlightData = storedHighlights[computerId];
            
            // Only apply highlights that are less than one week old
            if (currentTime - highlightData.timestamp < ONE_WEEK) {
                const row = document.querySelector(`tr[data-id="${computerId}"]`);
                if (row) {
                    // Only apply cell highlights, not row highlight
                    highlightChangedCells(row, highlightData.changes);
                    
                    // Keep this highlight in storage
                    updatedHighlights[computerId] = highlightData;
                }
            }
        });
        
        // Update localStorage with only the valid highlights
        localStorage.setItem('highlightedChanges', JSON.stringify(updatedHighlights));
    }
    
    // Helper function to highlight changed cells
    function highlightChangedCells(row, changes) {
        const fieldToColumnMap = {
            'computer_No': 0,
            'department': 1,
            'Machine_type': 2,
            'user': 3,
            'computer_name': 4,
            'ip': 5,
            'processor': 6,
            'MOBO': 7,
            'power_supply': 8,
            'ram': 9,
            'SSD': 10,
            'OS': 11,
            'is_active': 12
        };
        
        // Find which fields were changed
        for (const field in fieldToColumnMap) {
            if (changes.previous && changes.new && 
                changes.previous[field] !== changes.new[field]) {
                
                const columnIndex = fieldToColumnMap[field];
                const cell = row.cells[columnIndex];
                
                if (cell) {
                    // Add highlight class
                    cell.classList.add('highlight-cell-change');
                    
                    // Add a tooltip to show the change
                    const oldValue = changes.previous[field] || '(empty)';
                    const newValue = changes.new[field] || '(empty)';
                    cell.title = `Changed from: ${oldValue} → ${newValue}`;
                    
                    // Add a data attribute to mark this cell as changed
                    cell.dataset.changed = 'true';
                    cell.dataset.oldValue = oldValue;
                    cell.dataset.newValue = newValue;
                }
            }
        }
    }
    
    // Notifies the dashboard about an update
    function notifyDashboardOfUpdate(computerId) {
        // Store the updated computer ID in localStorage so the dashboard can pick it up
        localStorage.setItem('lastUpdatedComputer', computerId);
        localStorage.setItem('lastUpdateTime', new Date().toISOString());
        
        // Also store the current user for the dashboard to display
        const currentUser = document.querySelector('.user-name')?.textContent || 'Unknown';
        localStorage.setItem('lastUpdateUser', currentUser);
        
        // Dispatch a custom event that the dashboard can listen for if it's open
        const updateEvent = new CustomEvent('computer-updated', { 
            detail: { computerId: computerId }
        });
        window.dispatchEvent(updateEvent);
    }

    // Updates the table body with new computer data
    function updateTable(computers, changedFields = {}) {
        const tbody = computersTable.querySelector('tbody');
        tbody.innerHTML = '';
        
        if (!computers || computers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="14" class="no-data">No computers found</td></tr>';
            return;
        }

        // Get current time to check for recent updates (within the last hour)
        const currentTime = new Date();
        const oneHourAgo = new Date(currentTime - 60 * 60 * 1000); // 1 hour in milliseconds
        
        computers.forEach(computer => {
            const row = document.createElement('tr');
            row.dataset.id = computer.computer_No;
            
            const status = computer.is_active === 'Y' ? 'Active' : 'Inactive';
            const formattedDate = computer.formatted_date || new Date(computer.last_updated).toLocaleString() || 'N/A';
            
            // Check if this is a recently updated record
            const lastUpdated = new Date(computer.last_updated);
            const isRecentlyUpdated = lastUpdated > oneHourAgo;
            
            // Apply highlight class if recently updated
            if (isRecentlyUpdated) {
                row.classList.add('highlight-update');
            }
            
            // Check if this is the computer with changed fields
            const hasChangedFields = changedFields.computerId === computer.computer_No && changedFields.fields && changedFields.fields.length > 0;
            
            // Map field names to column indices
            const fieldToColumnMap = {
                'computer_No': 0,
                'department': 1,
                'Machine_type': 2,
                'user': 3,
                'computer_name': 4,
                'ip': 5,
                'processor': 6,
                'MOBO': 7,
                'power_supply': 8,
                'ram': 9,
                'SSD': 10,
                'OS': 11,
                'is_active': 12
                // last_updated is not included as it always changes
            };
            
            row.innerHTML = `
                <td>${computer.computer_No}</td>
                <td>${computer.department || ''}</td>
                <td>${(computer.Machine_type || '').charAt(0).toUpperCase() + (computer.Machine_type || '').slice(1)}</td>
                <td>${computer.user || ''}</td>
                <td>${computer.computer_name || ''}</td>
                <td>${computer.ip || ''}</td>
                <td>${computer.processor || ''}</td>
                <td>${computer.MOBO || ''}</td>
                <td>${computer.power_supply || ''}</td>
                <td>${computer.ram || ''}</td>
                <td>${computer.SSD || ''}</td>
                <td>${computer.OS || ''}</td>
                <td><span class="status-${status.toLowerCase()}">${status}</span></td>
                <td>${formattedDate}</td>
            `;
            
            tbody.appendChild(row);
            
            // If this row has changed fields, highlight the specific cells
            if (hasChangedFields) {
                changedFields.fields.forEach(field => {
                    const columnIndex = fieldToColumnMap[field];
                    if (columnIndex !== undefined) {
                        const cell = row.cells[columnIndex];
                        if (cell) {
                            cell.classList.add('highlight-cell-change');
                        }
                    }
                });
            }
        });
        
        // Update button states after table refresh
        updateButtonStates();
    }

    // Fetches and displays computer data based on current filters
    async function refreshTable(highlightId = null) {
        const tbody = computersTable.querySelector('tbody');
        
        // Show loading indicator
        tbody.innerHTML = '<tr><td colspan="14" class="loading">Loading data...</td></tr>';
        
        try {
            const searchQuery = searchInput.value.trim();
            const department = departmentFilter.value;
            const fromDate = lastUpdatedFrom.value;
            const toDate = lastUpdatedTo.value;
            
            console.log('Fetching data with filters:', {
                search: searchQuery,
                department: department,
                fromDate: fromDate,
                toDate: toDate,
                page: currentPage
            });
            
            const formData = new FormData();
            formData.append('action', 'list');
            formData.append('page', currentPage);
            formData.append('limit', 10);
            formData.append('search', searchQuery);
            formData.append('department', department);
            formData.append('last_updated_from', fromDate);
            formData.append('last_updated_to', toDate);
            
            const response = await fetch('listbackend.php', {
                method: 'POST',
                body: formData
            });
            
            // Check for HTTP errors
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Get the raw text first for debugging
            const responseText = await response.text();
            
            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('Failed to parse JSON response:', responseText);
                throw new Error(`Invalid JSON response: ${parseError.message}`);
            }
            
            // Check for API success flag
            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch data');
            }
            
            // Make sure computers array exists
            if (!data.computers || !Array.isArray(data.computers)) {
                console.error('Invalid data structure:', data);
                throw new Error('Server returned invalid data structure');
            }
            
            console.log('Data fetched successfully:', data);
            
            // Update table with new data
            updateTable(data.computers);
            totalPages = data.pages || 1;
            renderPagination(currentPage, totalPages, data.total);
            
            // If a specific computer ID was provided to highlight, apply the highlighting
            if (highlightId) {
                // Since highlightUpdatedRow is now async, we need to await it
                await highlightUpdatedRow(highlightId);
            }
            
        } catch (error) {
            console.error('Error fetching data:', error);
            tbody.innerHTML = `<tr><td colspan="14" class="error">Error loading data: ${error.message}</td></tr>`;
        }
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
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            refreshTable();
        }, 300);
    });
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
    const exportBtn = document.getElementById('printTableBtn');
    if (exportBtn) {
        // Update button text to reflect new functionality (optional)
        exportBtn.textContent = 'Export as PDF';
        
        exportBtn.onclick = e => {
            e.preventDefault();
            
            // Get the table element to export
            const tableElement = document.querySelector('table'); // Adjust selector if needed
            
            // Configure PDF options for complete table display
            const options = {
                margin: [5, 5, 5, 5],  // Smaller margins [top, right, bottom, left]
                filename: 'computer-inventory.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    logging: true,
                    letterRendering: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a3', // Larger paper size
                    orientation: 'landscape',
                    compress: true,
                    precision: 2,
                    text: {
                        fontSize: 12,
                        font: 'helvetica',
                        lineHeight: 1.5,
                        textOverflow: 'ellipsis'
                    }
                }
            };
            
            // Show loading indicator
            const originalText = exportBtn.textContent;
            exportBtn.textContent = 'Generating PDF...';
            exportBtn.disabled = true;
            
            // Generate and download the PDF
            html2pdf()
                .from(tableElement)
                .set(options)
                .save()
                .then(() => {
                    // Restore button state
                    exportBtn.textContent = originalText;
                    exportBtn.disabled = false;
                })
                .catch(err => {
                    console.error('PDF generation error:', err);
                    exportBtn.textContent = originalText;
                    exportBtn.disabled = false;
                });
        };
    }

    // Initial UI setup
    updateButtonStates();
    renderPagination(currentPage, totalPages);
    refreshTable(); // Fetch and display the first 10 records on page load

    // Injects custom styles for highlights, pagination, form validation, and modals
    const style = document.createElement('style');
    style.innerHTML = `
/* Base styles */
.highlight-history { background: #e6ffe6 !important; border-left: 4px solid #4CAF50; }
.pagination-modern { display: flex; gap: 4px; align-items: center; justify-content: center; margin: 10px 0; flex-wrap: wrap; }
.pag-btn { background: #fff; border: 1px solid #ccc; border-radius: 4px; padding: 4px 10px; margin: 0 2px; cursor: pointer; transition: background 0.2s, color 0.2s; font-size: 1rem; }
.pag-btn.active, .pag-btn:hover:not(:disabled) { background: #1976d2; color: #fff; border-color: #1976d2; }
.pag-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pag-ellipsis { padding: 0 6px; color: #888; font-size: 1.1em; }
.pagination-summary { text-align: center; color: #555; font-size: 0.95em; margin-bottom: 10px; }
.form-error { color: #f44336; font-size: 0.85em; margin-top: 4px; }
input.error, select.error { border-color: #f44336 !important; background-color: #fff8f8; }

/* Modal fixes */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1050;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.modal.show {
    opacity: 1;
}

.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.modal-overlay.show {
    opacity: 1;
}

.modal-content {
    position: relative;
    background-color: #fff;
    margin: 30px auto;
    width: 98vw;
    max-width: 1000px;
    min-width: 300px;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(80, 120, 200, 0.13);
    overflow: hidden;
    z-index: 1051;
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
}

.btn {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #33ccff;
    color: white;
}

.btn-secondary {
    background: #e0e7ff;
    color: #4e73df;
}

.status-active,
.status-inactive {
    font-weight: 700;
    text-align: center;
    padding: 8px 20px;
    border-radius: 999px; /* pill shape */
    display: inline-block;
    min-width: 90px;
    border: none;
    box-shadow: 0 2px 8px rgba(80, 200, 120, 0.10);
    font-size: 1em;
    letter-spacing: 0.03em;
    cursor: default; /* not clickable */
    transition: background 0.2s, color 0.2s;
    user-select: none;
}

.status-active {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    color: #166534;
    border: 1.5px solid #43e97b;
    box-shadow: 0 2px 8px rgba(67, 233, 123, 0.15);
}

.status-inactive {
    background: linear-gradient(90deg, #ff6a6a 0%, #ffb86c 100%);
    color: #7f1d1d;
    border: 1.5px solid #ff6a6a;
    box-shadow: 0 2px 8px rgba(255, 106, 106, 0.15);
}

.modal-footer {
    background: #f8f9fc;
    border-radius: 0 0 14px 14px;
    box-shadow: 0 -2px 8px rgba(80, 120, 200, 0.04);
    padding: 16px 24px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    position: sticky;
    bottom: 0;
    z-index: 10;
    width: 100%;
    flex-wrap: wrap;
}
`;
    document.head.appendChild(style);
});