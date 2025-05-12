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
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            toggleInputType(passwordInput, togglePassword.querySelector('i'));
        });
    }
    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', function() {
            toggleInputType(confirmPasswordInput, toggleConfirmPassword.querySelector('i'));
        });
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
                        <td>${user.Role}</td>
                        <td style="display: flex; gap: 8px; align-items: center;">
                            <button class="btn btn-action btn-edit edit-btn" data-id="${user.emp_ID}" title="Edit User"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-action btn-delete delete-btn" data-id="${user.emp_ID}" title="Delete User"><i class="bi bi-trash"></i></button>
                        </td>
                    `;
                    usersTableBody.appendChild(tr);
                });
            } else {
                usersTableBody.innerHTML = '<tr><td colspan="5">No users found.</td></tr>';
            }
        });
    }

    // Show modal
    function showModal(modal) {
        modal.classList.add('show');
        modalOverlay.style.display = 'block';
        // Always show modal header/title
        const header = modal.querySelector('.modal-header');
        if (header) header.style.display = '';
    }
    function hideModal(modal) {
        modal.classList.remove('show');
        modalOverlay.style.display = 'none';
    }

    // Add User
    addUserBtn.addEventListener('click', function() {
        editingUserId = null;
        userForm.reset();
        modalTitle.innerHTML = '<i class="bi bi-person-plus"></i> Add ICT User';
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
                        userForm.role.value = user.Role;
                        modalTitle.innerHTML = '<i class="bi bi-pencil-square"></i> Edit User';
                        showModal(userModal);
                    }
                }
            });
        }
        // Delete User
        if (e.target.closest('.delete-btn')) {
            deleteUserId = e.target.closest('.delete-btn').dataset.id;
            showModal(deleteModal);
        }
    });

    // Close modals
    document.querySelectorAll('.modal .close, #cancelBtn, .btn-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            hideModal(userModal);
            hideModal(deleteModal);
        });
    });
    modalOverlay.addEventListener('click', function() {
        hideModal(userModal);
        hideModal(deleteModal);
    });

    // Add/Edit submit with password confirmation validation
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(userForm);
        let action = editingUserId ? 'edit' : 'add';
        formData.append('action', action);
        // Password match validation
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        // Remove previous error
        let errorMsg = userForm.querySelector('.password-error');
        if (errorMsg) errorMsg.remove();
        if (password !== confirmPassword) {
            e.preventDefault();
            const error = document.createElement('div');
            error.className = 'password-error';
            error.style.color = 'red';
            error.style.marginTop = '5px';
            error.textContent = 'Passwords do not match!';
            confirmPasswordInput.parentNode.appendChild(error);
            confirmPasswordInput.focus();
            return;
        }
        fetch('Userbackend.php', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                hideModal(userModal);
                fetchUsers();
            } else {
                alert(data.message || 'Error');
            }
        });
    });

    // Confirm delete
    deleteModal.querySelector('.btn-confirm').addEventListener('click', function() {
        if (!deleteUserId) return;
        fetch('Userbackend.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=delete&emp_ID=${deleteUserId}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                hideModal(deleteModal);
                fetchUsers();
            } else {
                alert(data.message || 'Error');
            }
        });
    });

    // Initial load
    fetchUsers();
});
