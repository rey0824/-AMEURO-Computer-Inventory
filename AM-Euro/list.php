<?php
require_once 'DB.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Inventory Management</title>
    <link rel="stylesheet" href="CSS/list.css">
    <link rel="stylesheet" href="CSS/nav.css">
    <link rel="stylesheet" href="CSS/modal-item-id.css">
    <link rel="stylesheet" href="CSS/alert-modal.css">
    <link rel="stylesheet" href="CSS/history-modal.css">
    <link rel="stylesheet" href="CSS/table-optimization.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        /* ... existing styles ... */
        
        .history-changes {
            margin-top: 15px;
        }
        
        .change-item {
            margin-bottom: 8px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .change-item strong {
            color: #495057;
            margin-right: 8px;
        }
        
        .old-value {
            color: #dc3545;
            text-decoration: line-through;
            margin-right: 8px;
        }
        
        .new-value {
            color: #28a745;
            font-weight: 500;
        }
        
        .fa-arrow-right {
            color: #6c757d;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="content-wrapper">
            <header>
                <h1><i class="bi bi-pc-display"></i> Computer Inventory Management System</h1>
                <p>Track and manage all computer assets across departments</p>
            </header>

            <div class="table-container">
                <div class="section-header">
                    <div class="header-left">
                        <h2><i class="bi bi-table"></i> Computer Inventory List</h2>
                    </div>
                    <div class="header-right">
                        <button id="addComputerBtn" class="btn btn-primary" title="Add New Computer">
                            <i class="bi bi-plus-lg"></i> Add Computer
                        </button>
                    </div>
                </div>
            
                <div class="toolbar">
                    <div class="filter-container">
                        <div class="search-box">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" id="searchInput" placeholder="Search...">
                        </div>
                        
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="departmentFilter">Department</label>
                                <select id="departmentFilter">
                                    <option value="">All Departments</option>
                                    <?php
                                    $departmentSql = "SELECT DISTINCT department FROM tblcomputer ORDER BY department";
                                    $departmentResult = $conn->query($departmentSql);
                                    if ($departmentResult->num_rows > 0) {
                                        while($deptRow = $departmentResult->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($deptRow["department"]) . "'>" . 
                                                htmlspecialchars($deptRow["department"]) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="filter-item">
                                <label for="statusFilter">Status</label>
                                <select id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            
                            <div class="filter-item date-range-filter">
                                <label>Date Range</label>
                                <div class="date-inputs">
                                    <input type="date" id="lastUpdatedFrom" title="From Date">
                                    <span class="date-separator">to</span>
                                    <input type="date" id="lastUpdatedTo" title="To Date">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button id="printTableBtn" class="btn btn-secondary">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                        <button class="edit-btn" title="Edit Selected" disabled>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="deactivate-btn" title="Deactivate Selected" disabled>
                            <i class="bi bi-power"></i>
                        </button>
                        <button class="history-btn" title="View History" disabled>
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </div>
                </div>
            
                <table id="computersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Dept</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Comp. Name</th>
                            <th>IP</th>
                            <th>Processor</th>
                            <th>MOBO</th>
                            <th>Power Supply</th>
                            <th>RAM</th>
                            <th>SSD</th>
                            <th>OS</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table body will be populated by JS via AJAX pagination -->
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="pagination justify-content-center"></div>
        </div>
    </div>

    <!-- Computer Modal -->
    <div id="computerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="modalTitle">Add New Computer</h2>
                <span id="modalItemId" class="item-id-display"></span>
            </div>
            <div class="modal-body">
                <form id="computerForm">
                    <input type="hidden" id="computerId" name="computer_No">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department">
                        </div>
                        
                        <div class="form-group">
                            <label for="machineType">Machine Type</label>
                            <select id="machineType" name="Machine_type">
                                <option value="">Select Type</option>
                                <option value="desktop">Desktop</option>
                                <option value="laptop">Laptop</option>
                                <option value="server">Server</option>
                                <option value="switch">Switch</option>
                                <option value="router">Router</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="user">User</label>
                            <input type="text" id="user" name="user">
                        </div>
                        
                        <div class="form-group">
                            <label for="computerName">Computer Name</label>
                            <input type="text" id="computerName" name="computer_name">
                        </div>
                        
                        <div class="form-group">
                            <label for="ip">IP Address</label>
                            <input type="text" id="ip" name="ip">
                        </div>
                        
                        <div class="form-group">
                            <label for="processor">Processor</label>
                            <input type="text" id="processor" name="processor">
                        </div>
                        
                        <div class="form-group">
                            <label for="mobo">Motherboard</label>
                            <input type="text" id="mobo" name="MOBO">
                        </div>
                        
                        <div class="form-group">
                            <label for="powerSupply">Power Supply</label>
                            <input type="text" id="powerSupply" name="power_supply">
                        </div>
                        
                        <div class="form-group">
                            <label for="ram">RAM</label>
                            <input type="text" id="ram" name="ram">
                        </div>
                        
                        <div class="form-group">
                            <label for="ssd">SSD</label>
                            <input type="text" id="ssd" name="SSD">
                        </div>
                        
                        <div class="form-group">
                            <label for="os">Operating System</label>
                            <input type="text" id="os" name="OS">
                        </div>

                        <div class="form-group">
                            <label for="macAddress">MAC Address</label>
                            <input type="text" id="macAddress" name="MAC_Address" placeholder="XX:XX:XX:XX:XX:XX" value="">
                        </div>

                        <div class="form-group">
                            <label for="assetNo">Asset No</label>
                            <input type="text" id="assetNo" name="Asset_no" placeholder="Enter asset number">
                        </div>

                        <div class="form-group">
                            <label for="deploymentDate">Deployment Date</label>
                            <input type="date" id="deploymentDate" name="deployment_date">
                        </div>
                    </div>
                    <div class="form-group" id="editCommentGroup" style="display:none;">
                        <label for="editComment">Comment</label>
                        <textarea id="editComment" name="comment" placeholder="Enter reason for update..."></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content history-modal-content">
            <div class="modal-header history-modal-header">
                <h2><i class="bi bi-clock-history"></i> Computer History</h2>
                <span id="historyTimestamp" class="history-timestamp-header"></span>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body history-modal-body">
                <div class="history-layout">
                    <!-- Timeline sidebar -->
                    <div class="history-sidebar">
                        <h3 class="sidebar-title">Change Timeline</h3>
                        <div class="timeline-container" id="historyTimeline">
                            <!-- Timeline entries will be dynamically added here -->
                        </div>
                    </div>
                    
                    <!-- Version comparison main content -->
                    <div class="history-main-content">
                        <h3 class="content-title">Version Comparison</h3>
                        <div class="version-comparison">
                            <div class="version-card previous-version">
                                <div class="version-header">
                                    <h4>Previous Version</h4>
                                </div>
                                <div class="version-details"></div>
                            </div>
                            
                            <div class="version-card current-version">
                                <div class="version-header">
                                    <h4>Current Version</h4>
                                </div>
                                <div class="version-details"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivate Modal -->
    <div id="deactivateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h3>Confirm Status Change</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change the status of this computer? This action will be recorded in the history.</p>
                <div class="form-group">
                    <label for="deactivateComment">Reason for Status Change</label>
                    <textarea id="deactivateComment" name="comment" placeholder="Enter reason for status change..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Cancel</button>
                    <button type="button" class="btn-confirm">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <?php if (isset($_SESSION['username']) || isset($_SESSION['name'])): ?>
        <script>
            <?php $username = isset($_SESSION['username']) ? $_SESSION['username'] : $_SESSION['name']; ?>
            sessionStorage.setItem('username', <?php echo json_encode($username); ?>);
            window.username = <?php echo json_encode($username); ?>;
        </script>
        <?php endif; ?>


    <!-- Custom Alert Modal -->
    <div id="alertModal" class="modal">
        <div class="modal-content alert-modal-content">
            <div class="alert-header">
                <h3 id="alertTitle">Notification</h3>
                <span class="close-alert">&times;</span>
            </div>
            <div class="alert-body">
                <div class="alert-icon">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <p id="alertMessage"></p>
            </div>
            <div class="alert-footer">
                <button id="alertOkBtn" class="btn btn-primary">OK</button>
            </div>
        </div>
    </div>
    
    <div class="modal-overlay"></div>
    <!-- Include html2pdf.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="JS/list.js"></script>
</body>
</html>