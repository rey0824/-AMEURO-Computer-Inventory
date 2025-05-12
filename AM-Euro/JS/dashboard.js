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
 * @param {Object} previousData - Previous computer data (optional)
 */
function updateDashboardActivity(computerData, updatedBy, updateTime, previousData = null) {
    const statsContainer = document.querySelector('.stats-container');
    if (!statsContainer) return;
    
    const formattedDate = updateTime ? 
        new Date(updateTime).toLocaleString() : 
        new Date().toLocaleString();
    
    updateActivityCard(statsContainer, formattedDate, updatedBy);
    updateActivityTable(computerData, formattedDate, previousData);
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
 * @param {Object} previousData - Previous computer data (optional)
 */
function updateActivityTable(computerData, formattedDate, previousData) {
    const tbody = document.getElementById('inventoryTableBody');
    if (!tbody) return;
    
    // Remove "no updates" row if present
    const noUpdatesRow = tbody.querySelector('.no-updates');
    if (noUpdatesRow) {
        noUpdatesRow.closest('tr')?.remove();
    }
    
    // Create and insert new row
    const newRow = createTableRow(computerData, formattedDate, previousData);
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
 * @param {Object} previousData - Previous computer data (optional)
 * @returns {Element} New table row element
 */
function createTableRow(computerData, formattedDate, previousData) {
    const fields = [
        'department', 'machine_type', 'user', 'computer_name', 'ip', 'processor',
        'MOBO', 'power_supply', 'ram', 'SSD', 'OS'
    ];
    
    const newRow = document.createElement('tr');
    let rowHtml = `<td>${computerData.computer_No || computerData.id}</td>`;
    
    fields.forEach(field => {
        let value = computerData[field] || '';
        let highlight = '';
        
        if (previousData && typeof previousData === 'object') {
            const prevValue = previousData[field] || '';
            if (field === 'machine_type') {
                if (prevValue.toLowerCase() !== value.toLowerCase()) {
                    highlight = ' class="highlight-history"';
                }
                value = value.charAt(0).toUpperCase() + value.slice(1);
            } else if (prevValue !== value) {
                highlight = ' class="highlight-history"';
            }
        } else if (field === 'machine_type') {
            value = value.charAt(0).toUpperCase() + value.slice(1);
        }
        
        rowHtml += `<td${highlight}>${value}</td>`;
    });
    
    rowHtml += `<td class="timestamp">${formattedDate}</td>`;
    newRow.innerHTML = rowHtml;
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
    
    // Initialize department filter if present
    if (document.getElementById('departmentFilter')) {
        populateDepartmentFilter();
    }
});