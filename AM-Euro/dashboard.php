<?php
// Dashboard page for AmEuro System
// Shows summary stats and recent computer activity
require_once 'dashbackend.php';

// Fetch dashboard stats and recent activity
$recentComputers = getRecentlyUpdatedComputers($conn);
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
                <div class="stat-card">
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

            <div class="updates-section" style="margin-top: 36px;">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Recent Computer Updates</h2>
                </div>
                <div class="table-responsive">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department</th>
                                <th>Machine Type</th>
                                <th>User</th>
                                <th>Comp. Name</th>
                                <th>IP</th>
                                <th>Processor</th>
                                <th>MOBO</th>
                                <th>Power Supply</th>
                                <th>RAM</th>
                                <th>SSD</th>
                                <th>OS</th>
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
                                        $fields = ['department','machine_type','user','computer_name','ip','processor','MOBO','power_supply','ram','SSD','OS'];
                                        ?>
                                        <td><?php echo htmlspecialchars($computer['computer_No']); ?></td>
                                        <?php foreach ($fields as $field): ?>
                                            <?php 
                                            if ($field === 'machine_type') {
                                                $prevVal = isset($prev[$field]) ? strtolower($prev[$field]) : null;
                                                $currVal = isset($curr[$field]) ? strtolower($curr[$field]) : null;
                                                $changed = isset($prevVal, $currVal) && $prevVal !== $currVal;
                                                $displayVal = isset($curr[$field]) ? ucfirst(strtolower($curr[$field])) : (isset($computer[$field]) ? ucfirst(strtolower($computer[$field])) : '');
                                            } else {
                                                $changed = isset($prev[$field]) && isset($curr[$field]) && $prev[$field] !== $curr[$field];
                                                $displayVal = $curr[$field] ?? $computer[$field];
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

    <script src="JS/dashboard.js"></script>
    <script>
    // Remove JS that tries to fill recent-activity-user/time from localStorage/sessionStorage
    </script>
</body>
</html>