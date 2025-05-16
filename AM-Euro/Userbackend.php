<?php
require_once 'DB.php';
require_login();

header('Content-Type: application/json');

function sanitize_all($data) {
    $sanitized = [];
    foreach ($data as $key => $value) {
        $sanitized[$key] = sanitize_input($value);
    }
    return $sanitized;
}

$action = isset($_POST['action']) ? sanitize_input($_POST['action']) : '';

switch ($action) {
    case 'list':
        listUsers();
        break;
    case 'add':
        addUser();
        break;
    case 'edit':
        editUser();
        break;
    case 'toggleStatus':
        toggleUserStatus();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function listUsers() {
    global $conn;
    $sql = "SELECT emp_ID, Name, Password, Role, status FROM tbemployee ORDER BY emp_ID ASC";
    $result = $conn->query($sql);
    $users = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    echo json_encode(['success' => true, 'users' => $users]);
}

function addUser() {
    global $conn;
    $data = sanitize_all($_POST);
    if (empty($data['Name']) || empty($data['Password']) || empty($data['Role'])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    $status = 'active'; // New users are active by default
    $stmt = $conn->prepare("INSERT INTO tbemployee (Name, Password, Role, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $data['Name'], $data['Password'], $data['Role'], $status);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user: ' . $conn->error]);
    }
}

function editUser() {
    global $conn;
    $data = sanitize_all($_POST);
    if (empty($data['emp_ID']) || empty($data['Name']) || empty($data['Password']) || empty($data['Role'])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    $stmt = $conn->prepare("UPDATE tbemployee SET Name=?, Password=?, Role=? WHERE emp_ID=?");
    $stmt->bind_param('sssi', $data['Name'], $data['Password'], $data['Role'], $data['emp_ID']);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . $conn->error]);
    }
}

function toggleUserStatus() {
    global $conn;
    $id = isset($_POST['emp_ID']) ? (int)$_POST['emp_ID'] : 0;
    $status = isset($_POST['status']) ? sanitize_input($_POST['status']) : '';
    
    if ($id <= 0 || !in_array($status, ['active', 'inactive'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        return;
    }
    
    $stmt = $conn->prepare("UPDATE tbemployee SET status = ? WHERE emp_ID = ?");
    $stmt->bind_param('si', $status, $id);
    
    if ($stmt->execute()) {
        $action = $status === 'active' ? 'activated' : 'deactivated';
        echo json_encode(['success' => true, 'message' => "User {$action} successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user status: ' . $conn->error]);
    }
}
