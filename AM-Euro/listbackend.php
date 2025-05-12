<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
header('Content-Type: application/json');
require_once 'DB.php'; // Include database connection

// Ensure user is logged in
require_login();

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
    
    $page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
    $limit = isset($_POST['limit']) ? max(1, (int)$_POST['limit']) : 10;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_POST['search']) ? '%' . sanitize_input($_POST['search']) . '%' : '';
    $department = isset($_POST['department']) ? sanitize_input($_POST['department']) : '';
    $lastUpdated = isset($_POST['last_updated']) ? sanitize_input($_POST['last_updated']) : '';
    $lastUpdatedFrom = isset($_POST['last_updated_from']) ? sanitize_input($_POST['last_updated_from']) : '';
    $lastUpdatedTo = isset($_POST['last_updated_to']) ? sanitize_input($_POST['last_updated_to']) : '';
    
    $rowsPerPage = $limit;
    
    $conditions = [];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $searchCondition = "(
            department LIKE ? OR
            user LIKE ? OR
            computer_name LIKE ? OR
            ip LIKE ? OR
            processor LIKE ? OR
            MOBO LIKE ? OR
            power_supply LIKE ? OR
            ram LIKE ? OR
            SSD LIKE ? OR
            OS LIKE ?
        )";
        $conditions[] = $searchCondition;
        
        $searchParam = $search;
        for ($i = 0; $i < 10; $i++) {
            $params[] = $searchParam;
            $types .= 's';
        }
    }
    
    if (!empty($department)) {
        $conditions[] = "department = ?";
        $params[] = $department;
        $types .= 's';
    }
    
    if (!empty($lastUpdatedFrom) && !empty($lastUpdatedTo)) {
        $conditions[] = "DATE(last_updated) BETWEEN ? AND ?";
        $params[] = $lastUpdatedFrom;
        $params[] = $lastUpdatedTo;
        $types .= 'ss';
    } elseif (!empty($lastUpdatedFrom)) {
        $conditions[] = "DATE(last_updated) >= ?";
        $params[] = $lastUpdatedFrom;
        $types .= 's';
    } elseif (!empty($lastUpdatedTo)) {
        $conditions[] = "DATE(last_updated) <= ?";
        $params[] = $lastUpdatedTo;
        $types .= 's';
    } elseif (!empty($lastUpdated)) {
        $conditions[] = "DATE(last_updated) = ?";
        $params[] = $lastUpdated;
        $types .= 's';
    }
    
    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = "WHERE " . implode(" AND ", $conditions);
    }
    
    // Optimized query to get both count and data in a single query
    $sql = "SELECT SQL_CALC_FOUND_ROWS c.*, 
            DATE_FORMAT(c.last_updated, '%m-%d-%y %H:%i:%s') as formatted_date,
            h.previous_data, h.new_data
            FROM tblcomputer c
            LEFT JOIN (
                SELECT computer_No, previous_data, new_data
                FROM tblcomputer_history h1
                WHERE timestamp = (
                    SELECT MAX(timestamp)
                    FROM tblcomputer_history h2
                    WHERE h2.computer_No = h1.computer_No
                )
            ) h ON c.computer_No = h.computer_No
            $whereClause 
            ORDER BY c.computer_No ASC 
            LIMIT ? OFFSET ?";
            
    $stmt = $conn->prepare($sql);
    
    $types .= 'ii';
    $params[] = $rowsPerPage;
    $params[] = $offset;
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get total count using FOUND_ROWS()
    $totalRows = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
    
    $computers = [];
    while ($row = $result->fetch_assoc()) {
        $row['history_previous'] = $row['previous_data'] ? json_decode($row['previous_data'], true) : null;
        $row['history_new'] = $row['new_data'] ? json_decode($row['new_data'], true) : null;
        unset($row['previous_data'], $row['new_data']);
        $computers[] = $row;
    }
    
    header('X-Total-Count: ' . $totalRows);
    header('X-Total-Pages: ' . ceil($totalRows / $rowsPerPage));
    
    echo json_encode([
        'success' => true, 
        'computers' => $computers, 
        'total' => $totalRows,
        'page' => $page,
        'pages' => ceil($totalRows / $rowsPerPage)
    ]);
}

// Get details of a single computer by ID
// Returns a JSON response with the computer's data or an error if not found
function getComputerDetails() {
    global $conn;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid computer ID']);
        return;
    }
    $sql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Computer not found']);
        return;
    }
    $computer = $result->fetch_assoc();
    echo json_encode(['success' => true, 'computer' => $computer]);
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
            echo json_encode([
                'success' => true, 
                'message' => 'Computer updated successfully',
                'id' => $id
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

// Delete a computer and log the deletion in history
// Removes the record and logs the deletion with an optional comment
function deleteComputer() {
    global $conn;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid computer ID']);
        return;
    }
    $currentSql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
    $currentStmt = $conn->prepare($currentSql);
    $currentStmt->bind_param('i', $id);
    $currentStmt->execute();
    $currentData = $currentStmt->get_result()->fetch_assoc();
    if (!$currentData) {
        echo json_encode(['success' => false, 'message' => 'Computer not found']);
        return;
    }
    $changes = [
        'previous' => $currentData,
        'new' => null,
        'action' => 'delete'
    ];
    $historySql = "INSERT INTO tblcomputer_history (computer_No, previous_data, new_data, updated_by) VALUES (?, ?, ?, ?)";
    $historyStmt = $conn->prepare($historySql);
    $jsonPrevious = json_encode($changes['previous']);
    $jsonNew = json_encode($changes['new']);
    $updatedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
    $historyStmt->bind_param('isss', $id, $jsonPrevious, $jsonNew, $updatedBy);
    $conn->begin_transaction();
    try {
        $historyStmt->execute();
        $history_id = $conn->insert_id;
        if (isset($_POST['comment']) && !empty($_POST['comment'])) {
            $comment = sanitize_input($_POST['comment']);
            $commentSql = "INSERT INTO tblchange_comments (history_id, comment) VALUES (?, ?)";
            $commentStmt = $conn->prepare($commentSql);
            $commentStmt->bind_param('is', $history_id, $comment);
            $commentStmt->execute();
        }
        $deleteSql = "DELETE FROM tblcomputer WHERE computer_No = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param('i', $id);
        $deleteStmt->execute();
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Computer deleted successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete computer: ' . $e->getMessage()
        ]);
    }
}

// Get the full change history for a computer
// Returns an array of all changes (previous/new data, comment, timestamp)
function getComputerHistory() {
    global $conn;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid computer ID']);
        return;
    }
    $sql = "SELECT h.*, c.computer_name as current_name, c.deployment_date as current_deployment_date FROM tblcomputer_history h LEFT JOIN tblcomputer c ON h.computer_No = c.computer_No WHERE h.computer_No = ? ORDER BY h.timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    $latestTimestamp = null;
    $previousVersion = null;
    while($row = $result->fetch_assoc()) {
        $changes = [
            'previous' => json_decode($row['previous_data'], true),
            'new' => json_decode($row['new_data'], true)
        ];
        // Add deployment_date to previous/new if missing
        if (!isset($changes['previous']['deployment_date']) && isset($row['deployment_date'])) {
            $changes['previous']['deployment_date'] = $row['deployment_date'];
        }
        if (!isset($changes['new']['deployment_date']) && isset($row['current_deployment_date'])) {
            $changes['new']['deployment_date'] = $row['current_deployment_date'];
        }
        $history[] = [
            'timestamp' => $row['timestamp'],
            'changes' => $changes,
            'comment' => $row['comment'],
            'updated_by' => $row['updated_by']
        ];
        if ($latestTimestamp === null) {
            $latestTimestamp = $row['timestamp'];
            $previousVersion = $changes['previous'];
        }
    }
    $currentSql = "SELECT department, Machine_type, user, computer_name, ip, processor, MOBO, power_supply, ram, SSD, OS, MAC_Address, deployment_date FROM tblcomputer WHERE computer_No = ?";
    $currentStmt = $conn->prepare($currentSql);
    $currentStmt->bind_param('i', $id);
    $currentStmt->execute();
    $currentResult = $currentStmt->get_result();
    $current = $currentResult->fetch_assoc();
    echo json_encode([
        'success' => true,
        'history' => $history,
        'current' => $current,
        'previous' => $previousVersion,
        'timestamp' => $latestTimestamp
    ]);
}

// Compare two versions of a computer record and return changed fields
function compareVersions($old, $new) {
    if (!$old || !$new) return [];
    
    $changes = [];
    $fields = [
        'department', 'Machine_type', 'user', 'computer_name', 'ip', 'processor', 
        'MOBO', 'power_supply', 'ram', 'SSD', 'OS', 'MAC_Address', 'deployment_date'
    ];
    
    foreach ($fields as $field) {
        if (isset($old[$field]) && isset($new[$field]) && $old[$field] !== $new[$field]) {
            $changes[$field] = [
                'old' => $old[$field],
                'new' => $new[$field]
            ];
        }
    }
    
    return $changes;
}

// Add a comment to a specific change history record
function addChangeComment() {
    global $conn;
    
    $history_id = isset($_POST['history_id']) ? (int)$_POST['history_id'] : 0;
    $comment = isset($_POST['comment']) ? sanitize_input($_POST['comment']) : '';
    
    if ($history_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid history ID']);
        return;
    }
    
    $sql = "INSERT INTO tblchange_comments (history_id, comment, change_date) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $history_id, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
    }
}

// Deactivate a computer and log the status change
function deactivateComputer() {
    global $conn;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid computer ID']);
        return;
    }

    try {
        $conn->begin_transaction();

        // Get current data
        $currentSql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
        $currentStmt = $conn->prepare($currentSql);
        $currentStmt->bind_param('i', $id);
        $currentStmt->execute();
        $currentData = $currentStmt->get_result()->fetch_assoc();

        if (!$currentData) {
            throw new Exception('Computer not found');
        }

        // Toggle active status
        $newStatus = $currentData['is_active'] === 'Y' ? 'N' : 'Y';
        $updatedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
        $comment = isset($_POST['comment']) ? sanitize_input($_POST['comment']) : '';

        // Update computer status
        $updateSql = "UPDATE tblcomputer SET is_active = ?, status_changed_by = ?, status_changed_date = NOW() WHERE computer_No = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param('ssi', $newStatus, $updatedBy, $id);

        // Log the change in history
        $historySql = "INSERT INTO tblcomputer_history (computer_No, previous_data, new_data, updated_by, comment) VALUES (?, ?, ?, ?, ?)";
        $historyStmt = $conn->prepare($historySql);
        
        $previousData = $currentData;
        $newData = array_merge($currentData, [
            'is_active' => $newStatus,
            'status_changed_by' => $updatedBy,
            'status_changed_date' => date('Y-m-d H:i:s')
        ]);

        $jsonPrevious = json_encode($previousData);
        $jsonNew = json_encode($newData);
        $historyStmt->bind_param('issss', $id, $jsonPrevious, $jsonNew, $updatedBy, $comment);

        if ($updateStmt->execute() && $historyStmt->execute()) {
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Computer ' . ($newStatus === 'Y' ? 'activated' : 'deactivated') . ' successfully'
            ]);
        } else {
            throw new Exception('Failed to update computer status');
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update computer status: ' . $e->getMessage()
        ]);
    }
}
?>