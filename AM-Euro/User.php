<?php
require_once 'DB.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/user.css">
    <link rel="stylesheet" href="CSS/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="content-wrapper">
            <header>
                <h1><i class="bi bi-people"></i> User Maintenance</h1>
                <p>Manage user accounts for the AmEuro System</p>
            </header>
            <div class="table-container">
                <div class="section-header">
                    <h2><i class="bi bi-person-lines-fill"></i> User List</h2>
                    <button id="addUserBtn" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add User</button>
                </div>
                <div class="table-responsive">
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th><i class="bi bi-hash"></i> ID</th>
                                <th><i class="bi bi-person"></i> Name</th>
                                <th><i class="bi bi-key"></i> Password</th>
                                <th><i class="bi bi-person-badge"></i> Role</th>
                                <th><i class="bi bi-info-circle"></i> Status</th>
                                <th><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="modalTitle"><i class="bi bi-person-plus"></i> Add ICT User</h2>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="emp_ID">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="name"><i class="bi bi-person"></i> Name</label>
                            <input type="text" id="name" name="Name" required placeholder="Enter full name">
                        </div>
                        <div class="form-group">
                            <label for="password"><i class="bi bi-key"></i> Password</label>
                            <div class="input-icon-group">
                                <input type="password" id="password" name="Password" required placeholder="Enter password">
                                <button type="button" id="togglePassword" tabindex="-1"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword"><i class="bi bi-key-fill"></i> Confirm Password</label>
                            <div class="input-icon-group">
                                <input type="password" id="confirmPassword" name="ConfirmPassword" required placeholder="Confirm password">
                                <button type="button" id="toggleConfirmPassword" tabindex="-1"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label for="role"><i class="bi bi-person-badge"></i> Role</label>
                            <select id="role" name="Role" required>
                                <option value="">Select Role</option>
                                <option value="Manager">Manager</option>
                                <option value="Supervisor">Supervisor</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelBtn"><i class="bi bi-x-circle"></i> Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn"><i class="bi bi-check-circle"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay"></div>
    <script src="JS/user.js"></script>
</body>
</html>
