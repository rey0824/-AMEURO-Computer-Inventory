<?php
require_once 'DB.php'; // Include database connection

// Ensure user is logged in
require_login();

// Ensure username is set in session for update logging
if (!isset($_SESSION['username']) && isset($_SESSION['name'])) {
    $_SESSION['username'] = $_SESSION['name'];
}

// Set headers for JSON response
header('Content-Type: application/json');

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
    
    $countSql = "SELECT COUNT(*) as total FROM tblcomputer $whereClause";
    $stmt = $conn->prepare($countSql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $totalRows = $result->fetch_assoc()['total'];
    
    $sql = "SELECT *, DATE_FORMAT(last_updated, '%Y-%m-%d %H:%i:%s') as formatted_date FROM tblcomputer $whereClause ORDER BY computer_No ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    
    $types .= 'ii';
    $params[] = $rowsPerPage;
    $params[] = $offset;
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $computers = [];
    while ($row = $result->fetch_assoc()) {
        $historySql = "SELECT previous_data, new_data FROM tblcomputer_history WHERE computer_No = ? ORDER BY timestamp DESC LIMIT 1";
        $historyStmt = $conn->prepare($historySql);
        $historyStmt->bind_param('i', $row['computer_No']);
        $historyStmt->execute();
        $historyResult = $historyStmt->get_result();
        $history = $historyResult->fetch_assoc();
        $row['history_previous'] = $history ? json_decode($history['previous_data'], true) : null;
        $row['history_new'] = $history ? json_decode($history['new_data'], true) : null;
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
    $sql = "INSERT INTO tblcomputer (department, user, computer_name, ip, processor, MOBO, power_supply, ram, SSD, OS, deployment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssssss',
        $data['department'],
        $data['user'],
        $data['computer_name'],
        $data['ip'],
        $data['processor'],
        $data['MOBO'],
        $data['power_supply'],
        $data['ram'],
        $data['SSD'],
        $data['OS'],
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
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to add computer: ' . $conn->error
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
        $currentSql = "SELECT * FROM tblcomputer WHERE computer_No = ?";
        $currentStmt = $conn->prepare($currentSql);
        $currentStmt->bind_param('i', $id);
        $currentStmt->execute();
        $currentData = $currentStmt->get_result()->fetch_assoc();
        $data = sanitize_all($_POST);
        $changes = [
            'previous' => $currentData,
            'new' => [
                'department' => $data['department'],
                'user' => $data['user'],
                'computer_name' => $data['computer_name'],
                'ip' => $data['ip'],
                'processor' => $data['processor'],
                'MOBO' => $data['MOBO'],
                'power_supply' => $data['power_supply'],
                'ram' => $data['ram'],
                'SSD' => $data['SSD'],
                'OS' => $data['OS'],
                'deployment_date' => $data['deployment_date']
            ]
        ];
        $historySql = "INSERT INTO tblcomputer_history (computer_No, previous_data, new_data, updated_by, comment) VALUES (?, ?, ?, ?, ?)";
        $historyStmt = $conn->prepare($historySql);
        $jsonPrevious = json_encode($changes['previous']);
        $jsonNew = json_encode($changes['new']);
        $updatedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
        $historyStmt->bind_param('issss', $id, $jsonPrevious, $jsonNew, $updatedBy, $comment);
        $historyStmt->execute();
        $sql = "UPDATE tblcomputer SET department = ?, user = ?, computer_name = ?, ip = ?, processor = ?, MOBO = ?, power_supply = ?, ram = ?, SSD = ?, OS = ?, deployment_date = ?, last_updated = NOW() WHERE computer_No = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssssssssssssi',
            $data['department'],
            $data['user'],
            $data['computer_name'],
            $data['ip'],
            $data['processor'],
            $data['MOBO'],
            $data['power_supply'],
            $data['ram'],
            $data['SSD'],
            $data['OS'],
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
        $conn->rollback();
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
    $currentSql = "SELECT department, user, computer_name, ip, processor, MOBO, power_supply, ram, SSD, OS, deployment_date FROM tblcomputer WHERE computer_No = ?";
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
        'department', 'user', 'computer_name', 'ip', 'processor', 
        'MOBO', 'power_supply', 'ram', 'SSD', 'OS'
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
?>