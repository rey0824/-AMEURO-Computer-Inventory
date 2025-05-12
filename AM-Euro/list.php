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
                <div class="section-header" style="display: flex; align-items: center; justify-content: space-between;">
                    <h2 style="margin: 10px;"><i class="bi bi-table"></i> Computer Inventory List</h2>
                </div>
            
            <div class="toolbar">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search...">
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
                    <!-- Date Range Filter -->
                    <div class="date-range-filter">
                        <label for="lastUpdatedFrom">From</label>
                        <input type="date" id="lastUpdatedFrom" title="From Date">
                        <label for="lastUpdatedTo">To</label>
                        <input type="date" id="lastUpdatedTo" title="To Date">
                    </div>
                </div>
                <div class="action-buttons" style="margin-left:auto;">
                    <button id="printTableBtn" class="btn btn-secondary"><i class="bi bi-printer"></i> Print</button>
                    <button class="edit-btn" title="Edit Selected" disabled>
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="deactivate-btn" title="Deactivate Selected" disabled>
                        <i class="bi bi-power"></i>
                    </button>
                    <button class="history-btn" title="View History" disabled>
                        <i class="bi bi-clock-history"></i>
                    </button>
                    <button id="addComputerBtn" title="Add New Computer">
                        <i class="bi bi-plus-lg"></i>
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
            </div>
            <div class="modal-body">
                <form id="computerForm">
                    <input type="hidden" id="computerId" name="computer_No">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select id="department" name="department">
                                <option value="">Select Department</option>
                                <?php
                                $departmentSql = "SELECT DISTINCT department FROM tblcomputer ORDER BY department";
                                $departmentResult = $conn->query($departmentSql);
                                if ($departmentResult && $departmentResult->num_rows > 0) {
                                    while($deptRow = $departmentResult->fetch_assoc()) {
                                        $dept = htmlspecialchars($deptRow["department"]);
                                        echo "<option value='$dept'>$dept</option>";
                                    }
                                }
                                ?>
                            </select>
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
                            <input type="text" id="macAddress" name="MAC_Address" placeholder="XX:XX:XX:XX:XX:XX">
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
        <div class="modal-content">
            <div class="modal-header" style="position: relative;">
                <span class="close">&times;</span>
                <h2>Computer History</h2>
                <span id="historyTimestamp" class="history-timestamp-header"></span>
            </div>
            <div class="history-content">
                <div class="history-timeline">
                    <div class="timeline-container" id="historyTimeline">
                        <!-- Timeline entries will be dynamically added here -->
                    </div>
                </div>
                <div class="version-comparison">
                    <div class="previous-version">
                        <h3>Previous Version</h3>
                        <div class="version-details"></div>
                    </div>
                    <div class="current-version">
                        <h3>Current Version</h3>
                        <div class="version-details"></div>
                    </div>
                </div>
                <div class="history-changes">
                    <?php foreach ($history['changes'] as $field => $change): ?>
                        <div class="change-item">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $field)); ?>:</strong>
                            <span class="old-value"><?php echo htmlspecialchars($change['old']); ?></span>
                            <i class="fas fa-arrow-right"></i>
                            <span class="new-value"><?php echo htmlspecialchars($change['new']); ?></span>
                        </div>
                    <?php endforeach; ?>
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


    <div class="modal-overlay"></div>
    <script src="JS/list.js"></script>
</body>
</html>