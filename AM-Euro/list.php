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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        
        /* Date picker styling */
        .date-inputs {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .date-input-wrapper {
            position: relative;
            flex: 1;
        }
        
        .datepicker {
            padding: 8px 30px 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            font-size: 14px;
            transition: border-color 0.2s;
            background-color: white;
        }
        
        .datepicker:focus {
            outline: none;
            border-color: #33ccff;
            box-shadow: 0 0 0 3px rgba(51, 204, 255, 0.15);
        }
        
        .date-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }
        
        .date-separator {
            font-weight: 500;
            color: #495057;
        }
        
        /* Flatpickr theme overrides */
        .flatpickr-calendar {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 6px;
        }
        
        .flatpickr-day.selected {
            background: #33ccff;
            border-color: #33ccff;
        }
        
        .flatpickr-day.selected:hover {
            background: #00bfff;
            border-color: #00bfff;
        }
        
        .flatpickr-clear-button {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 6px 10px;
            margin: 10px;
            color: #dc3545;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            float: right;
        }
        
        .flatpickr-clear-button:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        
        /* Modern filter styling */
        .toolbar {
            background: #f8f9ff;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .filter-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .search-box {
            position: relative;
            max-width: 100%;
            margin-bottom: 8px;
        }
        
        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
        }
        
        #searchInput {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #d7dce4;
            border-radius: 10px;
            font-size: 15px;
            background-color: #fff;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }
        
        #searchInput:focus {
            outline: none;
            border-color: #33ccff;
            box-shadow: 0 0 0 3px rgba(51, 204, 255, 0.15);
        }
        
        .search-hint {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
            margin-left: 4px;
        }
        
        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-start;
        }
        
        .filter-item {
            min-width: 200px;
            flex: 1;
        }
        
        .filter-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
            font-size: 14px;
        }
        
        .filter-item label i {
            margin-right: 6px;
            color: #33ccff;
        }
        
        .select-wrapper {
            position: relative;
        }
        
        .modern-select {
            width: 100%;
            padding: 12px 14px;
            appearance: none;
            border: 1px solid #d7dce4;
            border-radius: 10px;
            background-color: #fff;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }
        
        .modern-select:focus {
            outline: none;
            border-color: #33ccff;
            box-shadow: 0 0 0 3px rgba(51, 204, 255, 0.15);
        }
        
        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
            transition: transform 0.2s ease;
        }
        
        .modern-select:focus + .select-arrow {
            transform: translateY(-50%) rotate(180deg);
            color: #33ccff;
        }
        
        /* Date picker styling */
        .date-inputs {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .date-input-wrapper {
            position: relative;
            flex: 1;
        }
        
        .datepicker {
            width: 100%;
            padding: 12px 14px 12px 36px;
            border: 1px solid #d7dce4;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }
        
        .datepicker:focus {
            outline: none;
            border-color: #33ccff;
            box-shadow: 0 0 0 3px rgba(51, 204, 255, 0.15);
        }
        
        .date-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .date-input-wrapper:hover .date-icon,
        .datepicker:focus + .date-icon {
            color: #33ccff;
        }
        
        .date-separator {
            font-weight: 500;
            color: #495057;
            padding: 0 4px;
        }
        
        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        
        .action-buttons button {
            background-color: #fff;
            border: 1px solid #d7dce4;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #495057;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }
        
        .action-buttons button:not(:disabled):hover {
            background-color: #f8f9ff;
            border-color: #33ccff;
            color: #33ccff;
        }
        
        .action-buttons button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #33ccff 0%, #00bfff 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(51, 204, 255, 0.25);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #00bfff 0%, #0099cc 100%);
            box-shadow: 0 6px 16px rgba(51, 204, 255, 0.35);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: #e0e7ff;
            color: #4e73df;
            border-color: #d6e0ff;
        }
        
        .btn-secondary:hover {
            background-color: #d6e0ff;
            color: #375ad3;
        }
        
        .btn-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 10px;
            background-color: #fff;
            color: #495057;
            border: 1px solid #d7dce4;
            transition: all 0.2s ease;
        }
        
        .btn-icon i {
            font-size: 18px;
        }
        
        .btn-icon:not(:disabled):hover {
            background-color: #f8f9ff;
            border-color: #33ccff;
            color: #33ccff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        
        /* Responsive adjustments */
        @media (min-width: 768px) {
            .toolbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: flex-start;
            }
            
            .filter-container {
                flex: 1;
            }
            
            .action-buttons {
                margin-top: 0;
                align-items: flex-end;
                padding-top: 28px;
            }
            
            .search-box {
                max-width: 400px;
            }
        }
        
        /* Flatpickr theme overrides */
        .flatpickr-calendar {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            border-radius: 10px;
            border: none;
            overflow: hidden;
        }
        
        .flatpickr-month {
            background: linear-gradient(135deg, #33ccff 0%, #00bfff 100%);
            color: white;
        }
        
        .flatpickr-weekday {
            background: #f8f9ff;
            color: #495057;
        }
        
        .flatpickr-day.selected {
            background: #33ccff;
            border-color: #33ccff;
            box-shadow: 0 2px 6px rgba(51, 204, 255, 0.25);
        }
        
        .flatpickr-day.selected:hover {
            background: #00bfff;
            border-color: #00bfff;
        }
        
        .flatpickr-day:hover {
            background: #f0f8ff;
        }
        
        .flatpickr-clear-button {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px 12px;
            margin: 8px;
            color: #dc3545;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            float: right;
        }
        
        .flatpickr-clear-button:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: white;
            font-weight: 500;
        }
        
        .numInputWrapper span.arrowUp:after,
        .numInputWrapper span.arrowDown:after {
            border-color: rgba(255, 255, 255, 0.7);
        }
        
        /* Table styling improvements */
        #computersTable {
            border-radius: 10px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }
        
        #computersTable thead {
            background: linear-gradient(135deg, #f0f8ff 0%, #e6f7ff 100%);
        }
        
        #computersTable th {
            padding: 14px 12px;
            font-weight: 600;
            text-align: left;
            color: #495057;
            border-bottom: 2px solid #e0e7ff;
            white-space: nowrap;
        }
        
        #computersTable td {
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }
        
        #computersTable tbody tr {
            transition: background-color 0.2s ease;
        }
        
        #computersTable tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        #computersTable tbody tr.selected {
            background-color: #e6f7ff;
        }
        
        .highlight-cell-change {
            animation: highlight-pulse 2s ease-in-out;
        }
        
        @keyframes highlight-pulse {
            0% { background-color: rgba(51, 204, 255, 0.1); }
            50% { background-color: rgba(51, 204, 255, 0.2); }
            100% { background-color: transparent; }
        }
        
        /* Pagination improvements */
        .pagination {
            margin-top: 20px;
        }
        
        .pagination-modern {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .pag-btn {
            background: #fff;
            border: 1px solid #d7dce4;
            border-radius: 6px;
            padding: 8px 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            min-width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .pag-btn.active, 
        .pag-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #33ccff 0%, #00bfff 100%);
            color: #fff;
            border-color: #33ccff;
            box-shadow: 0 2px 8px rgba(51, 204, 255, 0.25);
        }
        
        .pagination-summary {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 10px;
        }
        
        /* Header and Logo Styles */
        header {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        
        header h1 {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 0;
            font-size: 1.8rem;
            color: #2c3e50;
        }
        
        header h1 img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        header p {
            margin: 5px 0 0 55px;
            color: #7f8c8d;
            font-size: 1rem;
        }
        
        /* Add Computer button enhancements */
        #addComputerBtn {
            padding: 12px 18px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="content-wrapper">
            <header>
                <h1><img src="IMG/logo.png" alt="Logo"> Computer Inventory Management System</h1>
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
                            <input type="text" id="searchInput" placeholder="Search computers..." class="modern-input">
                            <div class="search-hint">Search by name, IP, department, user...</div>
                        </div>
                        
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="departmentFilter">
                                    <i class="bi bi-building"></i> Department
                                </label>
                                <div class="select-wrapper">
                                    <select id="departmentFilter" class="modern-select">
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
                                    <i class="bi bi-chevron-down select-arrow"></i>
                                </div>
                            </div>
                            
                            <div class="filter-item">
                                <label for="statusFilter">
                                    <i class="bi bi-toggle-on"></i> Status
                                </label>
                                <div class="select-wrapper">
                                    <select id="statusFilter" class="modern-select">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <i class="bi bi-chevron-down select-arrow"></i>
                                </div>
                            </div>
                            
                            <div class="filter-item date-range-filter">
                                <label>
                                    <i class="bi bi-calendar-range"></i> Date Range (Updates)
                                </label>
                                <div class="date-inputs">
                                    <div class="date-input-wrapper">
                                        <input type="text" id="lastUpdatedFrom" placeholder="MM-DD-YY" class="datepicker" title="From Date (MM-DD-YY)">
                                        <i class="bi bi-calendar-event date-icon"></i>
                                    </div>
                                    <span class="date-separator">to</span>
                                    <div class="date-input-wrapper">
                                        <input type="text" id="lastUpdatedTo" placeholder="MM-DD-YY" class="datepicker" title="To Date (MM-DD-YY)">
                                        <i class="bi bi-calendar-event date-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button id="printTableBtn" class="btn btn-secondary" title="Export table as PDF">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                        <button class="btn btn-icon edit-btn" title="Edit Selected" disabled>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-icon deactivate-btn" title="Change Status" disabled>
                            <i class="bi bi-power"></i>
                        </button>
                        <button class="btn btn-icon history-btn" title="View History" disabled>
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
    <!-- Include flatpickr datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="JS/list.js"></script>
</body>
</html>