<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

// Set content type to JSON
header('Content-Type: application/json');

// Include database connection
require_once 'DB.php';

// For testing/development, temporarily bypass login check
// Comment this out in production
$_SESSION['logged_in'] = true;

// Global error handler for exceptions
set_exception_handler(function($e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
    exit;
});

// Ensure username is set in session for update logging
if (!isset($_SESSION['username'])) {
    if (isset($_SESSION['name'])) {
        $_SESSION['username'] = $_SESSION['name'];
    } elseif (isset($_SESSION['emp_ID'])) {
        // If we have emp_ID but no username, set a default
        $_SESSION['username'] = 'User_' . $_SESSION['emp_ID'];
    }
}

// Global error handler for exceptions
set_exception_handler(function($e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
});

// Sanitize all input data in an array
function sanitize_all($data) {
    $sanitized = [];
    foreach ($data as $key => $value) {
        $sanitized[$key] = sanitize_input($value);
    }
    return $sanitized;
}

// Use sanitize_input from DB.php (do not redeclare here)

// Validate required input fields
function validateInput($data, $required = []) {
    $errors = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = $field;
        }
    }
    return $errors;
}

// We're using the require_login function from DB.php
// No need to redefine it here

// Get the requested action from POST
$action = isset($_POST['action']) ? sanitize_input($_POST['action']) : '';

// Main action handler
switch ($action) {
    case 'list':
        getComputersList();
        break;
    case 'get':
        getComputerDetails();
        break;
    case 'add':
        addComputer();
        break;
    case 'update':
        updateComputer();
        break;
    case 'deactivate':
        deactivateComputer();
        break;
    case 'delete':
        deleteComputer();
        break;
    case 'getHistory':
        getComputerHistory();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Recommended database indexes for better search performance:
// ALTER TABLE tblcomputer ADD INDEX idx_department (department);
// ALTER TABLE tblcomputer ADD INDEX idx_user (user);
// ALTER TABLE tblcomputer ADD INDEX idx_computer_name (computer_name);
// ALTER TABLE tblcomputer ADD INDEX idx_ip (ip);
// ALTER TABLE tblcomputer ADD INDEX idx_last_updated (last_updated);
// ALTER TABLE tblcomputer_history ADD INDEX idx_computer_timestamp (computer_No, timestamp);

// Get paginated and filtered list of computers
function getComputersList() {
    global $conn;
    
    try {
        // Get pagination parameters
        $page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? max(1, (int)$_POST['limit']) : 10;
        $offset = ($page - 1) * $limit;
        
        // Get filter parameters
        $search = isset($_POST['search']) && $_POST['search'] !== '' ? '%' . sanitize_input($_POST['search']) . '%' : null;
        $department = isset($_POST['department']) && $_POST['department'] !== '' ? sanitize_input($_POST['department']) : null;
        $lastUpdatedFrom = isset($_POST['last_updated_from']) && $_POST['last_updated_from'] !== '' ? sanitize_input($_POST['last_updated_from']) : null;
        $lastUpdatedTo = isset($_POST['last_updated_to']) && $_POST['last_updated_to'] !== '' ? sanitize_input($_POST['last_updated_to']) : null;
        
        // Build query conditions
        $conditions = [];
        $params = [];
        $types = '';
        
        if ($search !== null) {
            $conditions[] = "(department LIKE ? OR user LIKE ? OR computer_name LIKE ? OR ip LIKE ?)";
            for ($i = 0; $i < 4; $i++) {
                $params[] = $search;
                $types .= 's';
            }
        }
        
        if ($department !== null) {
            $conditions[] = "department = ?";
            $params[] = $department;
            $types .= 's';
        }
        
        if ($lastUpdatedFrom !== null && $lastUpdatedTo !== null) {
            $conditions[] = "DATE(last_updated) BETWEEN ? AND ?";
            $params[] = $lastUpdatedFrom;
            $params[] = $lastUpdatedTo;
            $types .= 'ss';
        }
        
        // Create WHERE clause
        $whereClause = empty($conditions) ? '' : ' WHERE ' . implode(' AND ', $conditions);
        
        // Build the main query
        if ($allRecords) {
            // If all records are requested, don't use LIMIT and OFFSET
            $sql = "SELECT 
                    computer_No, department, Machine_type, user, computer_name, 
                    ip, processor, MOBO, power_supply, ram, SSD, OS, MAC_Address, 
                    deployment_date, last_updated, is_active
                    FROM tblcomputer 
                    $whereClause 
                    ORDER BY computer_No ASC";
        } else {
            // Normal paginated query
            $sql = "SELECT 
                    computer_No, department, Machine_type, user, computer_name, 
                    ip, processor, MOBO, power_supply, ram, SSD, OS, MAC_Address, 
                    deployment_date, last_updated, is_active
                    FROM tblcomputer 
                    $whereClause 
                    ORDER BY computer_No ASC 
                    LIMIT ? OFFSET ?";
            
            // Add pagination parameters
            $types .= 'ii';
            $params[] = $limit;
            $params[] = $offset;
        }
        
        // Execute the query
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Count total rows for pagination
        $countSql = "SELECT COUNT(*) as total FROM tblcomputer $whereClause";
        $countStmt = $conn->prepare($countSql);
        
        if (!empty($params)) {
            // Remove the last two parameters (limit and offset)
            array_pop($params);
            array_pop($params);
            $countTypes = substr($types, 0, -2);
            
            if (!empty($params)) {
                $countStmt->bind_param($countTypes, ...$params);
            }
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRows = $countResult->fetch_assoc()['total'];
        
        // Fetch and format the results
        $computers = [];
        while ($row = $result->fetch_assoc()) {
            // Format the date
            if (isset($row['last_updated'])) {
                $row['formatted_date'] = date('Y-m-d H:i:s', strtotime($row['last_updated']));
            } else {
                $row['formatted_date'] = 'N/A';
            }
            $computers[] = $row;
        }
        
        // Return the results as JSON
        echo json_encode([
            'success' => true,
            'computers' => $computers,
            'total' => $totalRows,
            'pages' => ceil($totalRows / $limit)
        ]);
    } catch (Exception $e) {
        error_log("Error in getComputersList: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching computer list: ' . $e->getMessage()
        ]);
    }
}

// Get details of a single computer by ID
// Returns a JSON response with the computer's data or an error if not found
function getComputerDetails() {
    global $conn;
    
    try {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid computer ID']);
            return;
        }
        
        $sql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Computer not found']);
            return;
        }
        
        $computer = $result->fetch_assoc();
        
        // Format the date if it exists
        if (isset($computer['last_updated'])) {
            $computer['formatted_date'] = date('Y-m-d H:i:s', strtotime($computer['last_updated']));
        }
        
        echo json_encode(['success' => true, 'computer' => $computer]);
    } catch (Exception $e) {
        error_log("Error in getComputerDetails: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching computer details: ' . $e->getMessage()
        ]);
    }
}

// Add a new computer to the inventory
// Validates input, inserts into database, and returns success or error
function addComputer() {
    global $conn;
    $data = sanitize_all($_POST);
    
    // Set default values for empty fields
    $fields = ['department', 'user', 'Machine_type', 'computer_name', 'ip', 'processor', 'MOBO', 'power_supply', 'ram', 'SSD', 'OS', 'MAC_Address', 'deployment_date'];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            $data[$field] = null;
        }
    }
    
    // Ensure minimum required data is present
    if (empty($data['department']) || empty($data['computer_name']) || empty($data['ip'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Department, Computer Name, and IP Address are required fields'
        ]);
        return;
    }
    
    try {
        $sql = "INSERT INTO tblcomputer (department, user, Machine_type, computer_name, ip, processor, MOBO, power_supply, ram, SSD, OS, MAC_Address, deployment_date, last_updated) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssssssssss',
            $data['department'],
            $data['user'],
            $data['Machine_type'],
            $data['computer_name'],
            $data['ip'],
            $data['processor'],
            $data['MOBO'],
            $data['power_supply'],
            $data['ram'],
            $data['SSD'],
            $data['OS'],
            $data['MAC_Address'],
            $data['deployment_date']
        );

        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            echo json_encode([
                'success' => true, 
                'message' => 'Computer added successfully',
                'id' => $newId
            ]);
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to add computer: ' . $e->getMessage()
        ]);
    }
}

// Update an existing computer and log the change in history
// Validates input, updates the record, and logs the change
function updateComputer() {
    global $conn;
    try {
        $conn->begin_transaction();
        $id = isset($_POST['computer_No']) ? (int)$_POST['computer_No'] : 0;
        $comment = isset($_POST['comment']) ? sanitize_input($_POST['comment']) : '';
        
        if ($id <= 0) {
            throw new Exception('Invalid computer ID');
        }
        
        // Set default values for empty fields and handle required fields
        $data = sanitize_all($_POST);
        $fields = ['department', 'user', 'Machine_type', 'computer_name', 'ip', 'processor', 'MOBO', 'power_supply', 'ram', 'SSD', 'OS', 'MAC_Address', 'deployment_date'];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $data[$field] = null;
            }
        }
        
        // Ensure minimum required data is present
        if (empty($data['department']) || empty($data['computer_name']) || empty($data['ip'])) {
            throw new Exception('Department, Computer Name, and IP Address are required fields');
        }
        
        // Get current data for history logging
        $currentSql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
        $currentStmt = $conn->prepare($currentSql);
        $currentStmt->bind_param('i', $id);
        $currentStmt->execute();
        $result = $currentStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Computer not found with ID: ' . $id);
        }
        
        $currentData = $result->fetch_assoc();
        
        $changes = [
            'previous' => $currentData,
            'new' => [
                'department' => $data['department'],
                'user' => $data['user'],
                'Machine_type' => $data['Machine_type'],
                'computer_name' => $data['computer_name'],
                'ip' => $data['ip'],
                'processor' => $data['processor'],
                'MOBO' => $data['MOBO'],
                'power_supply' => $data['power_supply'],
                'ram' => $data['ram'],
                'SSD' => $data['SSD'],
                'OS' => $data['OS'],
                'MAC_Address' => $data['MAC_Address'],
                'deployment_date' => $data['deployment_date']
            ]
        ];
        
        // Only log history if there are actual changes
        $hasChanges = false;
        foreach ($fields as $field) {
            if ($changes['previous'][$field] != $changes['new'][$field]) {
                $hasChanges = true;
                break;
            }
        }
        
        // Log history only if there are changes or a comment is provided
        if ($hasChanges || !empty($comment)) {
            $historySql = "INSERT INTO tblcomputer_history (computer_No, previous_data, new_data, updated_by, comment) VALUES (?, ?, ?, ?, ?)";
            $historyStmt = $conn->prepare($historySql);
            $jsonPrevious = json_encode($changes['previous']);
            $jsonNew = json_encode($changes['new']);
            $updatedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
            $historyStmt->bind_param('issss', $id, $jsonPrevious, $jsonNew, $updatedBy, $comment);
            $historyStmt->execute();
        }
        
        $sql = "UPDATE tblcomputer SET department = ?, user = ?, Machine_type = ?, computer_name = ?, ip = ?, processor = ?, MOBO = ?, power_supply = ?, ram = ?, SSD = ?, OS = ?, MAC_Address = ?, deployment_date = ?, last_updated = NOW() WHERE computer_No = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssssssssssi',
            $data['department'],
            $data['user'],
            $data['Machine_type'],
            $data['computer_name'],
            $data['ip'],
            $data['processor'],
            $data['MOBO'],
            $data['power_supply'],
            $data['ram'],
            $data['SSD'],
            $data['OS'],
            $data['MAC_Address'],
            $data['deployment_date'],
            $id
        );
        
        if ($stmt->execute()) {
            $conn->commit();
            // Identify changed fields for frontend highlighting
            $changedFields = [];
            foreach ($fields as $field) {
                if ($changes['previous'][$field] != $changes['new'][$field]) {
                    $changedFields[] = $field;
                }
            }
            echo json_encode([
                'success' => true,
                'message' => 'Computer updated successfully',
                'id' => $id,
                'changedFields' => $changedFields
            ]);
        } else {
            throw new Exception('Failed to update computer: ' . $conn->error);
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Toggle computer active status (between 'Y' and 'N')
function deactivateComputer() {
    global $conn;
    try {
        $conn->begin_transaction();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $comment = isset($_POST['comment']) ? sanitize_input($_POST['comment']) : '';
        
        if ($id <= 0) {
            throw new Exception('Invalid computer ID');
        }
        
        // Get current data for history logging
        $currentSql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
        $currentStmt = $conn->prepare($currentSql);
        $currentStmt->bind_param('i', $id);
        $currentStmt->execute();
        $result = $currentStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Computer not found');
        }
        
        $currentData = $result->fetch_assoc();
        
        // Toggle is_active status (Y -> N or N -> Y)
        $newStatus = ($currentData['is_active'] === 'Y') ? 'N' : 'Y';
        
        // Create new data with toggled is_active status
        $newData = $currentData;
        $newData['is_active'] = $newStatus;
        $newData['status_changed_by'] = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
        $newData['status_changed_date'] = date('Y-m-d H:i:s');
        
        $changes = [
            'previous' => $currentData,
            'new' => $newData
        ];
        
        // Log the deactivation in history
        $historySql = "INSERT INTO tblcomputer_history (computer_No, previous_data, new_data, updated_by, comment) VALUES (?, ?, ?, ?, ?)";
        $historyStmt = $conn->prepare($historySql);
        $jsonPrevious = json_encode($changes['previous']);
        $jsonNew = json_encode($changes['new']);
        $updatedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
        $historyStmt->bind_param('issss', $id, $jsonPrevious, $jsonNew, $updatedBy, $comment);
        $historyStmt->execute();
        
        // Update the computer record with the new status
        $sql = "UPDATE tblcomputer SET is_active = ?, status_changed_by = ?, status_changed_date = NOW(), last_updated = NOW() WHERE computer_No = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $newStatus, $updatedBy, $id);
        
        if ($stmt->execute()) {
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => ($newStatus === 'Y') ? 'Computer activated successfully' : 'Computer deactivated successfully',
                'new_status' => $newStatus
            ]);
        } else {
            throw new Exception('Failed to deactivate computer: ' . $conn->error);
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Delete a computer and log the deletion in history
function deleteComputer() {
    global $conn;
    try {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $comment = isset($_POST['comment']) ? sanitize_input($_POST['comment']) : '';
        
        if ($id <= 0) {
            throw new Exception('Invalid computer ID');
        }
        
        $conn->begin_transaction();
        
        // Get current data for history logging
        $currentSql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
        $currentStmt = $conn->prepare($currentSql);
        $currentStmt->bind_param('i', $id);
        $currentStmt->execute();
        $result = $currentStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Computer not found');
        }
        
        $currentData = $result->fetch_assoc();
        
        // Log the deletion in history
        $historySql = "INSERT INTO tblcomputer_history (computer_No, previous_data, new_data, updated_by, comment, action) VALUES (?, ?, NULL, ?, ?, 'delete')";
        $historyStmt = $conn->prepare($historySql);
        $jsonPrevious = json_encode($currentData);
        $updatedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
        $historyStmt->bind_param('isss', $id, $jsonPrevious, $updatedBy, $comment);
        $historyStmt->execute();
        
        // Delete the computer record
        $deleteSql = "DELETE FROM tblcomputer WHERE computer_No = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param('i', $id);
        
        if ($deleteStmt->execute()) {
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Computer deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete computer: ' . $conn->error);
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Get computer history for the given ID
function getComputerHistory() {
    global $conn;
    try {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if ($id <= 0) {
            throw new Exception('Invalid computer ID');
        }
        
        $sql = "SELECT h.history_id, h.computer_No, h.previous_data, h.new_data, h.updated_by, h.comment, h.timestamp
                FROM tblcomputer_history h
                WHERE h.computer_No = ?
                ORDER BY h.timestamp DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $previous = json_decode($row['previous_data'], true);
            $new = json_decode($row['new_data'], true);
            
            $history[] = [
                'history_id' => $row['history_id'],
                'computer_No' => $row['computer_No'],
                'updated_by' => $row['updated_by'],
                'comment' => $row['comment'],
                'timestamp' => $row['timestamp'],
                'changes' => [
                    'previous' => $previous,
                    'new' => $new
                ]
            ];
        }
        
        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>