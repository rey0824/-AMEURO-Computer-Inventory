<?php
// Header/navigation bar for AmEuro System
// Shows navigation and user info
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get username from session if available
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
?>
<div class="header">
    <div class="header-content">
        <div class="logo">
            <h2><i class="fas fa-laptop-code"></i> AMEURO Computer-Inventory</h2>
        </div>
        <div class="header-right">
            <nav class="menu">
                <ul>
                    <li><a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="list.php" <?php echo basename($_SERVER['PHP_SELF']) == 'list.php' ? 'class="active"' : ''; ?>><i class="fas fa-desktop"></i> Computer Inventory</a></li>
                    <li><a href="User.php" <?php echo basename($_SERVER['PHP_SELF']) == 'User.php' ? 'class="active"' : ''; ?>><i class="fas fa-circle-user "></i> User</a></li>
                </ul>
            </nav>
            <div class="user-info">
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>