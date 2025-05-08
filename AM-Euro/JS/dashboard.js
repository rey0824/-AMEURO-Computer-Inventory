// This file contains all client-side logic for the Dashboard page.
//
// Functions in this file handle:
// - Displaying and updating dashboard statistics and recent activity
// - Filtering and searching the recently updated computers table
// - Populating department filters
// - UI enhancements for dashboard cards and tables
// - Checking for and displaying recent updates from localStorage
//
// All user interactions and dynamic updates for the dashboard page are managed here.

// Wait until the entire HTML document is loaded and parsed
// Initializes all dashboard UI logic and event handlers
// Handles stats, recent activity, table filtering, and department filter
document.addEventListener('DOMContentLoaded', function() {
    if (!Element.prototype.querySelectorAll) {
        Element.prototype.querySelectorAll = function(selector) {
            return document.querySelectorAll(selector);
        };
    }

    // Adds a custom selector to find elements containing specific text (utility for legacy :contains usage)
    // Used for finding elements by text content
    Element.prototype.findElementsWithText = function(targetText) {
        const elements = this.querySelectorAll('*');
        const matches = [];
        
        elements.forEach(element => {
            if (element.textContent.includes(targetText)) {
                matches.push(element);
            }
        });
        
        return matches;
    };
    
    /**
     * Updates the dashboard's Recent Activity section and table with the latest computer update
     * @param {Object} computerData - The computer data that was updated
     * @param {string} updatedBy - Username of the person who made the update
     * @param {string} updateTime - Timestamp of the update
     */
    function updateDashboardActivity(computerData, updatedBy, updateTime) {
        const statsContainer = document.querySelector('.stats-container');
        if (!statsContainer) return;
        
        // Use the actual update time if provided, else fallback to now
        const formattedDate = updateTime ? new Date(updateTime).toLocaleString() : new Date().toLocaleString();
        
        // Update the Recent Activity card if it exists
        const activityCards = statsContainer.querySelectorAll('.stat-card');
        let activityCard = null;
        
        // Find the Recent Activity card
        activityCards.forEach(card => {
            const titleElement = card.querySelector('.stat-card-title');
            if (titleElement && titleElement.textContent.includes('Recent Activity')) {
                activityCard = card;
            }
        });
        
        if (activityCard) {
            const descElements = activityCard.querySelectorAll('.stat-card-desc');
            
            if (descElements.length >= 1) {
                descElements[0].textContent = `Last update: ${formattedDate}`;
            }
            
            if (descElements.length >= 2) {
                descElements[1].textContent = `By: ${updatedBy}`;
            } else if (descElements.length === 1) {
                // If only one desc, append the user info
                const userInfo = document.createElement('div');
                userInfo.className = 'stat-card-desc';
                userInfo.textContent = `By: ${updatedBy}`;
                activityCard.appendChild(userInfo);
            }
        }
        
        // Update the Recently Updated table with the latest computer
        const tbody = document.getElementById('inventoryTableBody');
        if (tbody) {
            // Check if there's a "no updates" row to remove
            const noUpdatesRow = tbody.querySelector('.no-updates');
            if (noUpdatesRow) {
                const row = noUpdatesRow.closest('tr');
                if (row) {
                    row.remove();
                }
            }
            
            // Create a new row with the updated computer info
            const newRow = document.createElement('tr');
            
            newRow.innerHTML = `
                <td>${computerData.computer_No || computerData.id}</td>
                <td>${computerData.department || ''}</td>
                <td>${computerData.user || ''}</td>
                <td>${computerData.computer_name || computerData.computerName || ''}</td>
                <td>${computerData.ip || ''}</td>
                <td>${computerData.processor || ''}</td>
                <td>${computerData.MOBO || ''}</td>
                <td>${computerData.power_supply || ''}</td>
                <td>${computerData.ram || ''}</td>
                <td>${computerData.SSD || ''}</td>
                <td>${computerData.OS || ''}</td>
                <td class="timestamp">${formattedDate}</td>
            `;
            
            // Add the new row at the top of the table
            if (tbody.firstChild) {
                tbody.insertBefore(newRow, tbody.firstChild);
            } else {
                tbody.appendChild(newRow);
            }
            
            // Limit to showing only the most recent updates (e.g., top 10)
            const maxRows = 10;
            const rows = tbody.querySelectorAll('tr');
            
            if (rows.length > maxRows) {
                for (let i = maxRows; i < rows.length; i++) {
                    rows[i].remove();
                }
            }
        }
    }

    // --- Select Elements Needed ---
    const inventoryTableBody = document.getElementById('inventoryTableBody');
    
    // --- Basic Check: Ensure essential elements were found ---
    if (!inventoryTableBody) {
        console.log("Element with ID 'inventoryTableBody' found. This is normal if not on dashboard page.");
    }

    // Event handler: Filters the recently updated computers table by search input (updateSearch)
    document.getElementById('updateSearch')?.addEventListener('input', function(e) {
        const searchText = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#inventoryTableBody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Event handler: Filters the inventory table by search input (inventorySearch)
    document.getElementById('inventorySearch')?.addEventListener('input', function(e) {
        const searchText = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#inventoryTableBody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Populates the department filter dropdown with unique department names from the table
    // Called on page load if departmentFilter exists
    function populateDepartmentFilter() {
        const departments = new Set();
        document.querySelectorAll('#inventoryTableBody tr td:nth-child(2)').forEach(cell => {
            departments.add(cell.textContent);
        });
        
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

    // Initialize department filter on page load if present
    if (document.getElementById('departmentFilter')) {
        populateDepartmentFilter();
    }

}); // End DOMContentLoaded