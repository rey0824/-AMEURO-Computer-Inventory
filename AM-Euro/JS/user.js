document.addEventListener('DOMContentLoaded', function() {
    const usersTableBody = document.querySelector('#usersTable tbody');
    const addUserBtn = document.getElementById('addUserBtn');
    const userModal = document.getElementById('userModal');
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle');
    const deleteModal = document.getElementById('deleteModal');
    const modalOverlay = document.querySelector('.modal-overlay');
    let editingUserId = null;
    let deleteUserId = null;

    // Show/hide password fields
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    function toggleInputType(input, icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            toggleInputType(passwordInput, this.querySelector('i'));
        });
    }
    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', function() {
            toggleInputType(confirmPasswordInput, this.querySelector('i'));
        });
    }

    // Role badge generator
    function getRoleBadge(role) {
        let className = '';
        switch (role.toLowerCase()) {
            case 'manager':
                className = 'role-manager';
                break;
            case 'supervisor':
                className = 'role-supervisor';
                break;
            case 'staff':
                className = 'role-staff';
                break;
            default:
                className = '';
        }
        return `<span class="role-badge ${className}">${role}</span>`;
    }

    // Fetch and display users
    function fetchUsers() {
        fetch('Userbackend.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=list'
        })
        .then(r => r.json())
        .then(data => {
            usersTableBody.innerHTML = '';
            if (data.success && data.users.length) {
                data.users.forEach(user => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${user.emp_ID}</td>
                        <td>${user.Name}</td>
                        <td>${user.Password}</td>
                        <td>${getRoleBadge(user.Role)}</td>
                        <td>
                            <span class="status-badge ${user.status === 'active' ? 'status-active' : 'status-inactive'}">
                                ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-action btn-edit edit-btn" data-id="${user.emp_ID}" title="Edit User">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-action btn-toggle-status" 
                                    data-id="${user.emp_ID}" 
                                    data-status="${user.status === 'active' ? 'inactive' : 'active'}"
                                    title="${user.status === 'active' ? 'Deactivate' : 'Activate'} User">
                                <i class="bi ${user.status === 'active' ? 'bi-person-x' : 'bi-person-check'}"></i>
                            </button>
                        </td>
                    `;
                    usersTableBody.appendChild(tr);
                });
            } else {
                usersTableBody.innerHTML = '<tr><td colspan="5" class="empty-message">No users found.</td></tr>';
            }
            
            // Add animation to new rows
            const rows = usersTableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            showNotification('Error loading users', 'error');
        });
    }

    // Notification system
    function showNotification(message, type = 'success') {
        // Create notification element if it doesn't exist
        let notification = document.getElementById('notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'notification';
            document.body.appendChild(notification);
        }
        
        // Set notification style and content
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Show and then auto-hide notification
        notification.style.display = 'block';
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 10);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }, 3000);
    }

    // Show modal with animation
    function showModal(modal) {
        modal.classList.add('show');
        modalOverlay.style.display = 'block';
        
        // Animate modal
        setTimeout(() => {
            modal.style.transform = 'translate(-50%, -50%) scale(1)';
            modal.style.opacity = '1';
        }, 50);
    }
    
    function hideModal(modal) {
        modal.style.transform = 'translate(-50%, -50%) scale(0.9)';
        modal.style.opacity = '0';
        
        setTimeout(() => {
            modal.classList.remove('show');
            modalOverlay.style.display = 'none';
        }, 200);
    }

    // Add User
    addUserBtn.addEventListener('click', function() {
        editingUserId = null;
        userForm.reset();
        modalTitle.innerHTML = '<i class="bi bi-person-plus"></i> Add ICT User';
        
        // Reset password fields visibility
        if (passwordInput) passwordInput.type = 'password';
        if (confirmPasswordInput) confirmPasswordInput.type = 'password';
        
        // Reset password toggle icons
        const passwordIcon = togglePassword.querySelector('i');
        const confirmIcon = toggleConfirmPassword.querySelector('i');
        if (passwordIcon) {
            passwordIcon.classList.remove('bi-eye-slash');
            passwordIcon.classList.add('bi-eye');
        }
        if (confirmIcon) {
            confirmIcon.classList.remove('bi-eye-slash');
            confirmIcon.classList.add('bi-eye');
        }
        
        // Clear any error messages
        const errorMsg = userForm.querySelector('.password-error');
        if (errorMsg) errorMsg.remove();
        
        showModal(userModal);
    });

    // Edit User
    usersTableBody.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            const id = e.target.closest('.edit-btn').dataset.id;
            fetch('Userbackend.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=list`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const user = data.users.find(u => u.emp_ID == id);
                    if (user) {
                        editingUserId = id;
                        userForm.userId.value = user.emp_ID;
                        userForm.name.value = user.Name;
                        userForm.password.value = user.Password;
                        userForm.confirmPassword.value = user.Password; // Auto-populate confirm password
                        userForm.role.value = user.Role;
                        modalTitle.innerHTML = '<i class="bi bi-pencil-square"></i> Edit User';
                        
                        // Clear any error messages
                        const errorMsg = userForm.querySelector('.password-error');
                        if (errorMsg) errorMsg.remove();
                        
                        showModal(userModal);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
                showNotification('Error loading user data', 'error');
            });
        }
    });

    // Close modals
    document.querySelectorAll('.modal .close, #cancelBtn, .btn-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            hideModal(userModal);
        });
    });
    
    modalOverlay.addEventListener('click', function() {
        hideModal(userModal);
    });

    // Add/Edit submit with password confirmation validation
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Remove previous error
        let errorMsg = userForm.querySelector('.password-error');
        if (errorMsg) errorMsg.remove();
        
        // Password match validation
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (password !== confirmPassword) {
            const error = document.createElement('div');
            error.className = 'password-error';
            error.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Passwords do not match!';
            confirmPasswordInput.parentNode.appendChild(error);
            confirmPasswordInput.focus();
            
            // Shake effect for validation error
            confirmPasswordInput.style.borderColor = 'var(--danger-color)';
            confirmPasswordInput.parentNode.classList.add('shake');
            setTimeout(() => {
                confirmPasswordInput.parentNode.classList.remove('shake');
            }, 500);
            
            return;
        }
        
        // Form data
        const formData = new FormData(userForm);
        let action = editingUserId ? 'edit' : 'add';
        formData.append('action', action);
        
        // Submit button loading state
        const saveBtn = document.getElementById('saveBtn');
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';
        saveBtn.disabled = true;
        
        fetch('Userbackend.php', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                hideModal(userModal);
                fetchUsers();
                showNotification(action === 'add' ? 'User added successfully' : 'User updated successfully');
            } else {
                showNotification(data.message || 'Error saving user', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving user:', error);
            showNotification('Error saving user data', 'error');
        })
        .finally(() => {
            // Reset button state
            saveBtn.innerHTML = originalBtnText;
            saveBtn.disabled = false;
        });
    });

    // Toggle User Status
    usersTableBody.addEventListener('click', async function(e) {
        const toggleBtn = e.target.closest('.btn-toggle-status');
        if (toggleBtn) {
            const userId = toggleBtn.getAttribute('data-id');
            const newStatus = toggleBtn.getAttribute('data-status');
            const action = newStatus === 'active' ? 'activate' : 'deactivate';
            
            try {
                const response = await fetch('Userbackend.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=toggleStatus&emp_ID=${userId}&status=${newStatus}`
                });
                
                const data = await response.json();
                if (data.success) {
                    showNotification(`User ${action}d successfully`, 'success');
                    fetchUsers();
                } else {
                    showNotification(data.message || `Failed to ${action} user`, 'error');
                }
            } catch (error) {
                console.error(`Error ${action}ing user:`, error);
                showNotification(`Error ${action}ing user`, 'error');
            }
        }
    });

    // Add additional styles
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 400px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            transition: all 0.3s ease;
        }
        
        .notification.success {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
        }
        
        .notification.error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .notification i {
            font-size: 1.4rem;
        }
        
        .notification.success i {
            color: #10b981;
        }
        
        .notification.error i {
            color: #ef4444;
        }
        
        .empty-message {
            text-align: center;
            color: #6b7280;
            padding: 2rem;
        }
        
        .shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        
        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-4px); }
            40%, 60% { transform: translateX(4px); }
        }
        
        .modal {
            transform: translate(-50%, -50%) scale(0.9);
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        
        /* Toggle status button styles */
        .btn-toggle-status {
            margin-left: 0.5rem;
            border: 1px solid #d1d3e2;
            transition: all 0.3s ease;
        }
        
        /* Active state (will show red for deactivate action) */
        .btn-toggle-status[data-status="inactive"] {
            background-color: #e74a3b;
            color: white;
        }
        
        .btn-toggle-status[data-status="inactive"]:hover {
            background-color: #d32f2f;
            border-color: #c62828;
        }
        
        /* Inactive state (will show green for activate action) */
        .btn-toggle-status[data-status="active"] {
            background-color: #1cc88a;
            color: white;
        }
        
        .btn-toggle-status[data-status="active"]:hover {
            background-color: #17a673;
            border-color: #169b6b;
        }
        
        /* Icons color */
        .btn-toggle-status .bi {
            color: white;
        }
    `;
    document.head.appendChild(style);

    // Initial load
    fetchUsers();
});