/**
 * Dashboard JavaScript
 * Handles all client-side functionality for the dashboard interface
 */

// ===== Utility Functions =====

/**
 * Custom selector to find elements containing specific text
 * @param {string} targetText - Text to search for
 * @returns {Array} Array of matching elements
 */
Element.prototype.findElementsWithText = function(targetText) {
    const elements = this.querySelectorAll('*');
    return Array.from(elements).filter(element => 
        element.textContent.includes(targetText)
    );
};

// ===== Dashboard Activity Management =====

/**
 * Updates the dashboard's Recent Activity section with latest computer update
 * @param {Object} computerData - Updated computer data
 * @param {string} updatedBy - Username of updater
 * @param {string} updateTime - Timestamp of update
 */
function updateDashboardActivity(computerData, updatedBy, updateTime) {
    const statsContainer = document.querySelector('.stats-container');
    if (!statsContainer) return;
    
    const formattedDate = updateTime ? 
        new Date(updateTime).toLocaleString() : 
        new Date().toLocaleString();
    
    updateActivityCard(statsContainer, formattedDate, updatedBy);
    updateActivityTable(computerData, formattedDate);
}

/**
 * Updates the activity card with latest update information
 * @param {Element} container - Stats container element
 * @param {string} date - Formatted date string
 * @param {string} user - Username
 */
function updateActivityCard(container, date, user) {
    const activityCard = Array.from(container.querySelectorAll('.stat-card'))
        .find(card => card.querySelector('.stat-card-title')?.textContent.includes('Recent Activity'));
    
    if (!activityCard) return;
    
    const descElements = activityCard.querySelectorAll('.stat-card-desc');
    
    if (descElements.length >= 1) {
        descElements[0].textContent = `Last update: ${date}`;
    }
    
    if (descElements.length >= 2) {
        descElements[1].textContent = `By: ${user}`;
    } else if (descElements.length === 1) {
        const userInfo = document.createElement('div');
        userInfo.className = 'stat-card-desc';
        userInfo.textContent = `By: ${user}`;
        activityCard.appendChild(userInfo);
    }
}

/**
 * Updates the activity table with latest computer data
 * @param {Object} computerData - Updated computer data
 * @param {string} formattedDate - Formatted date string
 */
function updateActivityTable(computerData, formattedDate) {
    const tbody = document.getElementById('inventoryTableBody');
    if (!tbody) return;
    
    // Remove "no updates" row if present
    const noUpdatesRow = tbody.querySelector('.no-updates');
    if (noUpdatesRow) {
        noUpdatesRow.closest('tr')?.remove();
    }
    
    // Create and insert new row
    const newRow = createTableRow(computerData, formattedDate);
    if (tbody.firstChild) {
        tbody.insertBefore(newRow, tbody.firstChild);
    } else {
        tbody.appendChild(newRow);
    }
    
    // Limit to most recent updates
    const maxRows = 10;
    const rows = tbody.querySelectorAll('tr');
    if (rows.length > maxRows) {
        for (let i = maxRows; i < rows.length; i++) {
            rows[i].remove();
        }
    }
}

/**
 * Creates a new table row for the activity table
 * @param {Object} computerData - Computer data
 * @param {string} formattedDate - Formatted date string
 * @returns {Element} New table row element
 */
function createTableRow(computerData, formattedDate) {
    const fields = [
        'department', 'machine_type', 'user', 'computer_name', 'ip', 'processor',
        'MOBO', 'power_supply', 'ram', 'SSD', 'OS'
    ];
    
    const newRow = document.createElement('tr');
    newRow.dataset.id = computerData.computer_No || computerData.id;
    
    // Add ID cell
    const idCell = document.createElement('td');
    idCell.textContent = computerData.computer_No || computerData.id;
    newRow.appendChild(idCell);
    
    // Check for changed fields if history data is available
    const changedFields = new Set();
    if (computerData.history_previous && computerData.history_new) {
        const prev = computerData.history_previous;
        const curr = computerData.history_new;
        
        // Identify which fields have changed
        for (const field of fields) {
            let prevValue, currValue;
            
            // Handle case variations
            if (field === 'machine_type') {
                prevValue = prev['Machine_type'] || prev['machine_type'] || '';
                currValue = curr['Machine_type'] || curr['machine_type'] || '';
            } else if (field === 'MOBO') {
                prevValue = prev['MOBO'] || prev['mobo'] || '';
                currValue = curr['MOBO'] || curr['mobo'] || '';
            } else if (field === 'SSD') {
                prevValue = prev['SSD'] || prev['ssd'] || '';
                currValue = curr['SSD'] || curr['ssd'] || '';
            } else if (field === 'OS') {
                prevValue = prev['OS'] || prev['os'] || '';
                currValue = curr['OS'] || curr['os'] || '';
            } else {
                prevValue = prev[field] || '';
                currValue = curr[field] || '';
            }
            
            // If values differ, mark as changed
            if (prevValue !== currValue) {
                changedFields.add(field);
            }
        }
    }
    
    // Add all other cells
    fields.forEach(field => {
        let value = '';
        
        // Handle different field name casing between backend responses
        if (field === 'machine_type') {
            // Check for all possible case variations of machine_type
            value = computerData[field] || computerData['Machine_type'] || computerData['MACHINE_TYPE'] || computerData['machine_Type'] || '';
            // If still empty, try to find it by iterating through all keys case-insensitively
            if (!value) {
                for (let key in computerData) {
                    if (key.toLowerCase() === 'machine_type') {
                        value = computerData[key];
                        break;
                    }
                }
            }
            // Make sure to capitalize the first letter of machine type
            value = value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
        } else if (field === 'MOBO') {
            value = computerData[field] || computerData['mobo'] || '';
        } else if (field === 'SSD') {
            value = computerData[field] || computerData['ssd'] || '';
        } else if (field === 'OS') {
            value = computerData[field] || computerData['os'] || '';
        } else {
            value = computerData[field] || '';
        }
        
        const cell = document.createElement('td');
        cell.textContent = value;
        
        // Apply highlight to changed cells
        if (changedFields.has(field)) {
            cell.classList.add('highlight-history');
            
            // Add tooltip if we have previous and new values
            if (computerData.history_previous && computerData.history_new) {
                const prev = computerData.history_previous;
                const curr = computerData.history_new;
                
                let prevValue, currValue;
                if (field === 'machine_type') {
                    prevValue = prev['Machine_type'] || prev['machine_type'] || '(empty)';
                    currValue = curr['Machine_type'] || curr['machine_type'] || '(empty)';
                } else if (field === 'MOBO') {
                    prevValue = prev['MOBO'] || prev['mobo'] || '(empty)';
                    currValue = curr['MOBO'] || curr['mobo'] || '(empty)';
                } else if (field === 'SSD') {
                    prevValue = prev['SSD'] || prev['ssd'] || '(empty)';
                    currValue = curr['SSD'] || curr['ssd'] || '(empty)';
                } else if (field === 'OS') {
                    prevValue = prev['OS'] || prev['os'] || '(empty)';
                    currValue = curr['OS'] || curr['os'] || '(empty)';
                } else {
                    prevValue = prev[field] || '(empty)';
                    currValue = curr[field] || '(empty)';
                }
                
                cell.title = `Changed from: ${prevValue} → ${currValue}`;
            }
        }
        
        newRow.appendChild(cell);
    });
    
    // Add timestamp cell
    const timeCell = document.createElement('td');
    timeCell.className = 'timestamp';
    timeCell.textContent = formattedDate;
    newRow.appendChild(timeCell);
    
    return newRow;
}

// ===== Search and Filter Functions =====

/**
 * Filters table rows based on search input
 * @param {string} searchText - Text to search for
 * @param {string} tableId - ID of table body to filter
 */
function filterTableRows(searchText, tableId) {
    const rows = document.querySelectorAll(`#${tableId} tr`);
    const searchLower = searchText.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchLower) ? '' : 'none';
    });
}

/**
 * Populates department filter dropdown with unique departments
 */
function populateDepartmentFilter() {
    const departments = new Set();
    document.querySelectorAll('#inventoryTableBody tr td:nth-child(2)')
        .forEach(cell => departments.add(cell.textContent));
    
    const filter = document.getElementById('departmentFilter');
    if (filter) {
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            filter.appendChild(option);
        });
    }
}

// ===== Event Listeners =====

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    const updateSearch = document.getElementById('updateSearch');
    const inventorySearch = document.getElementById('inventorySearch');
    
    if (updateSearch) {
        updateSearch.addEventListener('input', (e) => 
            filterTableRows(e.target.value, 'inventoryTableBody')
        );
    }
    
    if (inventorySearch) {
        inventorySearch.addEventListener('input', (e) => 
            filterTableRows(e.target.value, 'inventoryTableBody')
        );
    }
    
    // Check for recent updates from list page
    const lastUpdatedComputer = localStorage.getItem('lastUpdatedComputer');
    const lastUpdateTime = localStorage.getItem('lastUpdateTime');
    
    if (lastUpdatedComputer && lastUpdateTime) {
        // Only show updates that happened in the last 5 minutes
        const updateTime = new Date(lastUpdateTime);
        const now = new Date();
        const timeDiff = (now - updateTime) / 1000 / 60; // difference in minutes
        
        if (timeDiff < 5) {
            fetchAndDisplayRecentUpdate(lastUpdatedComputer);
        }
    }
    
    // Listen for real-time updates from the list page
    window.addEventListener('computer-updated', function(event) {
        if (event.detail && event.detail.computerId) {
            fetchAndDisplayRecentUpdate(event.detail.computerId);
        }
    });
    
    // Initialize department filter if present
    if (document.getElementById('departmentFilter')) {
        populateDepartmentFilter();
    }
});

/**
 * Fetches and displays the most recent update in the dashboard
 * @param {number} computerId - ID of the computer that was updated
 */
function fetchAndDisplayRecentUpdate(computerId) {
    // Fetch the updated computer data with history information
    fetch(`dashbackend.php?action=get_computer&id=${computerId}&include_history=1`)
        .then(response => response.json())
        .then(data => {
            if (data && !data.error) {
                // Get the user who made the update from localStorage
                const updatedBy = localStorage.getItem('lastUpdateUser') || 'Unknown';
                const updateTime = localStorage.getItem('lastUpdateTime') 
                    ? new Date(localStorage.getItem('lastUpdateTime')).toLocaleString() 
                    : new Date().toLocaleString();
                
                // Check if we have stored highlight information
                const storedHighlights = JSON.parse(localStorage.getItem('highlightedChanges') || '{}');
                if (storedHighlights[computerId] && storedHighlights[computerId].changes) {
                    // Add history data from stored highlights if not already present
                    if (!data.history_previous || !data.history_new) {
                        data.history_previous = storedHighlights[computerId].changes.previous || {};
                        data.history_new = storedHighlights[computerId].changes.new || {};
                    }
                }
                
                // Update the dashboard activity section
                updateDashboardActivity(data, updatedBy, updateTime);
            }
        })
        .catch(error => console.error('Error fetching updated computer:', error));
}