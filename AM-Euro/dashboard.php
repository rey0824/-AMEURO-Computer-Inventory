<?php
// Dashboard page for AmEuro System
// Shows summary stats and recent computer activity
require_once 'dashbackend.php';

// Fetch dashboard stats and recent activity
$recentComputers = getRecentlyUpdatedComputers($conn); // 10 most recent updates
$totalComputers = getTotalComputerCount($conn);
$totalUsers = getTotalUserCount($conn);

// Fetch the most recent update from both tblcomputer and tblcomputer_history
$recentActivityUser = '-';
$recentActivityTime = 'No updates yet';
$latestHistory = $conn->query("SELECT updated_by, timestamp FROM tblcomputer_history ORDER BY timestamp DESC LIMIT 1");
$latestComputer = $conn->query("SELECT user, last_updated FROM tblcomputer WHERE last_updated IS NOT NULL ORDER BY last_updated DESC LIMIT 1");

$historyRow = $latestHistory && $latestHistory->num_rows > 0 ? $latestHistory->fetch_assoc() : null;
$computerRow = $latestComputer && $latestComputer->num_rows > 0 ? $latestComputer->fetch_assoc() : null;

$historyTime = $historyRow && $historyRow['timestamp'] ? strtotime($historyRow['timestamp']) : 0;
$computerTime = $computerRow && $computerRow['last_updated'] ? strtotime($computerRow['last_updated']) : 0;

if ($historyTime >= $computerTime && $historyTime > 0) {
    $recentActivityUser = $historyRow['updated_by'] ? htmlspecialchars($historyRow['updated_by']) : '-';
    $recentActivityTime = date('Y-m-d H:i:s', $historyTime);
} elseif ($computerTime > 0) {
    $recentActivityUser = $computerRow['user'] ? htmlspecialchars($computerRow['user']) : '-';
    $recentActivityTime = date('Y-m-d H:i:s', $computerTime);
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
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <div class="welcome-message">
                    Welcome, <?php echo htmlspecialchars(isset($_SESSION['name']) ? $_SESSION['name'] : 'User'); ?>
                </div>
            </div>
            
            <div class="stats-container">
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

            <div class="updates-section">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Recent Computer Updates</h2>
                </div>
                <div class="table-responsive">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department</th>
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
                                        $fields = ['department','user','computer_name','ip','processor','MOBO','power_supply','ram','SSD','OS'];
                                        ?>
                                        <td><?php echo htmlspecialchars($computer['computer_No']); ?></td>
                                        <?php foreach ($fields as $field): ?>
                                            <?php $changed = isset($prev[$field]) && isset($curr[$field]) && $prev[$field] !== $curr[$field]; ?>
                                            <td<?php if ($changed): ?> class="highlight-history"<?php endif; ?>><?php echo htmlspecialchars($curr[$field] ?? $computer[$field]); ?></td>
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