<?php
// Dashboard page for AmEuro System
// Shows summary stats and recent computer activity
require_once 'dashbackend.php';

// Fetch dashboard stats and recent activity
$recentComputers = getRecentlyUpdatedComputers($conn);

// Debug: Check the structure of the first computer to ensure machine_type exists
if (!empty($recentComputers)) {
    // Force machine_type to be set for all computers
    foreach ($recentComputers as &$computer) {
        // Check if machine_type exists in any case variation
        $machineTypeFound = false;
        foreach ($computer as $key => $value) {
            if (strtolower($key) === 'machine_type') {
                $computer['machine_type'] = $value;
                $machineTypeFound = true;
                break;
            }
        }
        
        // If machine_type wasn't found, set a default value
        if (!$machineTypeFound) {
            $computer['machine_type'] = 'Unknown';
        }
    }
    unset($computer); // Break the reference
}

$stats = getDashboardStats($conn);

$totalComputers = $stats['total_computers'];
$totalUsers = $stats['total_users'];

// Determine most recent activity
$historyTime = $stats['latest_history_update'] ? strtotime($stats['latest_history_update']) : 0;
$computerTime = $stats['latest_computer_update'] ? strtotime($stats['latest_computer_update']) : 0;

if ($historyTime >= $computerTime && $historyTime > 0) {
    // Get the user who made the most recent history update
    $historyUserQuery = "SELECT updated_by FROM tblcomputer_history WHERE timestamp = ? LIMIT 1";
    $stmt = $conn->prepare($historyUserQuery);
    $stmt->bind_param('s', $stats['latest_history_update']);
    $stmt->execute();
    $result = $stmt->get_result();
    $historyRow = $result->fetch_assoc();
    $recentActivityUser = $historyRow['updated_by'] ? htmlspecialchars($historyRow['updated_by']) : '-';
    $recentActivityTime = date('Y-m-d H:i:s', $historyTime);
} elseif ($computerTime > 0) {
    // Get the user who made the most recent computer update
    $computerUserQuery = "SELECT user FROM tblcomputer WHERE last_updated = ? LIMIT 1";
    $stmt = $conn->prepare($computerUserQuery);
    $stmt->bind_param('s', $stats['latest_computer_update']);
    $stmt->execute();
    $result = $stmt->get_result();
    $computerRow = $result->fetch_assoc();
    $recentActivityUser = $computerRow['user'] ? htmlspecialchars($computerRow['user']) : '-';
    $recentActivityTime = date('Y-m-d H:i:s', $computerTime);
} else {
    $recentActivityUser = '-';
    $recentActivityTime = 'No updates yet';
}

// Get current page filename for active navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AmEuro System</title>
    <link rel="stylesheet" href="CSS/nav.css">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/machine-type-popup.css">
    <link rel="stylesheet" href="CSS/table-highlight.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <div class="dashboard-container">
            <div class="dashboard-header" style="margin-bottom: 40px; padding-bottom: 24px; border-bottom: 2px solid #e3e6f0;">
                <div class="welcome-message">
                    Welcome, <?php echo htmlspecialchars(isset($_SESSION['name']) ? $_SESSION['name'] : 'User'); ?>
                </div>
            </div>
            
            <div class="stats-container" style="margin-bottom: 48px;">
                <div class="stat-card" id="total-computers-card">
                    <div class="stat-card-title">
                        <i class="fas fa-desktop"></i>Total Computers
                    </div>
                    <div class="stat-card-number"><?php echo htmlspecialchars($totalComputers); ?></div>
                    <div class="stat-card-desc">Active devices in inventory</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-title">
                        <i class="fa-solid fa-users"></i> Total Users
                    </div>
                    <div class="stat-card-number"><?php echo htmlspecialchars($totalUsers); ?></div>
                    <div class="stat-card-desc">Registered system users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-title">
                        <i class="fa-solid fa-clock"></i> Recent Activity
                    </div>
                    <div class="stat-card-desc">
                        Last update: <span id="recent-activity-time"><?php echo $recentActivityTime; ?></span><br>
                        By: <span id="recent-activity-user"><?php echo $recentActivityUser; ?></span>
                    </div>
                </div>
            </div>

            <div class="updates-section" style="margin-top: 10px;">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Recent Computer Updates</h2>
                </div>
                <div class="table-responsive">
                    <table class="inventory-table">
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
                        <tbody id="inventoryTableBody">
                            <?php if (empty($recentComputers)): ?>
                                <tr>
                                    <td colspan="12" class="no-updates">No recent computer updates. Updates will appear here when computers are added or modified.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentComputers as $computer): ?>
                                    <tr>
                                        <?php
                                        $prev = $computer['history_previous'] ?? [];
                                        $curr = $computer['history_new'] ?? $computer;
                                        $fields = ['department','machine_type','user','computer_name','ip','processor','MOBO','power_supply','ram','SSD','OS','status'];
                                        ?>
                                        <td><?php echo htmlspecialchars($computer['computer_No']); ?></td>
                                        <?php foreach ($fields as $field): ?>
                                            <?php 
                                            if ($field === 'machine_type') {
                                                // First try to get the value from Machine_type (capital M) as this might be the actual column name
                                                if (isset($computer['Machine_type']) && !empty($computer['Machine_type'])) {
                                                    $displayVal = ucfirst(strtolower($computer['Machine_type']));
                                                }
                                                // Then try lowercase machine_type
                                                elseif (isset($computer['machine_type']) && !empty($computer['machine_type'])) {
                                                    $displayVal = ucfirst(strtolower($computer['machine_type']));
                                                }
                                                // Check history data if available
                                                elseif (isset($curr['Machine_type']) && !empty($curr['Machine_type'])) {
                                                    $displayVal = ucfirst(strtolower($curr['Machine_type']));
                                                }
                                                elseif (isset($curr['machine_type']) && !empty($curr['machine_type'])) {
                                                    $displayVal = ucfirst(strtolower($curr['machine_type']));
                                                }
                                                // If still not found, set a default value
                                                else {
                                                    $displayVal = 'Unknown';
                                                }
                                                
                                                // For comparison with previous data
                                                $prevVal = null;
                                                if (isset($prev['Machine_type'])) {
                                                    $prevVal = strtolower($prev['Machine_type']);
                                                } elseif (isset($prev['machine_type'])) {
                                                    $prevVal = strtolower($prev['machine_type']);
                                                }
                                                
                                                $currVal = strtolower($displayVal);
                                                $changed = $prevVal !== null && $currVal !== $prevVal;
                                            } elseif ($field === 'status') {
                                                // Handle status field with special styling
                                                if (isset($computer['status']) && !empty($computer['status'])) {
                                                    $statusVal = strtolower($computer['status']);
                                                } elseif (isset($curr['status']) && !empty($curr['status'])) {
                                                    $statusVal = strtolower($curr['status']);
                                                } else {
                                                    $statusVal = 'active'; // Default to active if not specified
                                                }
                                                
                                                $displayVal = ucfirst($statusVal);
                                                $statusClass = ($statusVal == 'active') ? 'status-active' : 'status-inactive';
                                                
                                                // For comparison with previous data
                                                $prevVal = null;
                                                if (isset($prev['status'])) {
                                                    $prevVal = strtolower($prev['status']);
                                                }
                                                
                                                $changed = $prevVal !== null && $statusVal !== $prevVal;
                                                $class = $changed ? 'highlight-history' : '';
                                                
                                                echo '<td class="' . $class . '">';
                                                echo '<span class="' . $statusClass . '">' . htmlspecialchars($displayVal) . '</span>';
                                                echo '</td>';
                                                continue; // Skip the regular output for status field
                                            } else {
                                                $changed = isset($prev[$field]) && isset($curr[$field]) && $prev[$field] !== $curr[$field];
                                                $displayVal = $curr[$field] ?? $computer[$field] ?? '';
                                            }
                                            $titleAttr = (!empty($displayVal)) ? ' title="' . htmlspecialchars($displayVal) . '"' : '';
                                            ?>
                                            <td<?php if ($changed): ?> class="highlight-history"<?php endif; echo $titleAttr; ?>><?php echo htmlspecialchars($displayVal); ?></td>
                                        <?php endforeach; ?>
                                        <td class="timestamp"><?php echo htmlspecialchars($computer['formatted_time']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Machine Type Counts Popup Container -->
    <div id="machine-type-popup" class="machine-type-popup">
        <div class="machine-type-content">
            <span class="close-popup">&times;</span>
            <h2>Computer Types</h2>
            <div class="machine-type-grid">
                <div class="machine-type-item">
                    <i class="fas fa-desktop"></i>
                    <div class="machine-type-name">Desktop</div>
                    <div class="machine-type-count" id="desktop-count">0</div>
                </div>
                <div class="machine-type-item">
                    <i class="fas fa-laptop"></i>
                    <div class="machine-type-name">Laptop</div>
                    <div class="machine-type-count" id="laptop-count">0</div>
                </div>
                <div class="machine-type-item">
                    <i class="fas fa-server"></i>
                    <div class="machine-type-name">Server</div>
                    <div class="machine-type-count" id="server-count">0</div>
                </div>
                <div class="machine-type-item">
                    <i class="fas fa-network-wired"></i>
                    <div class="machine-type-name">Router</div>
                    <div class="machine-type-count" id="router-count">0</div>
                </div>
                <div class="machine-type-item">
                    <i class="fas fa-print"></i>
                    <div class="machine-type-name">Printer</div>
                    <div class="machine-type-count" id="printer-count">0</div>
                </div>
                <div class="machine-type-item">
                    <i class="fas fa-random"></i>
                    <div class="machine-type-name">Switch</div>
                    <div class="machine-type-count" id="switch-count">0</div>
                </div>
                <div class="machine-type-item">
                    <i class="fas fa-question-circle"></i>
                    <div class="machine-type-name">Other</div>
                    <div class="machine-type-count" id="other-count">0</div>
                </div>
            </div>
        </div>
    </div>

    <script src="JS/dashboard.js"></script>
    <script>
    // Remove JS that tries to fill recent-activity-user/time from localStorage/sessionStorage
    </script>
</body>
</html>