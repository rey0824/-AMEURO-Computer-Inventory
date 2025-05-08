<?php
// Backend logic for dashboard statistics and computer data
//
// Functions in this file provide:
// - Fetching a single computer record by ID
// - Fetching the 10 most recently updated computers (with history)
// - Getting total computer and user counts for dashboard stats
// - (AJAX) endpoint for fetching a single computer as JSON
//
// These functions are used by dashboard.php to display summary stats and recent activity.

require_once(__DIR__ . '/DB.php');

// AJAX handler for getting a single computer record
if (isset($_GET['action']) && $_GET['action'] === 'get_computer' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $computer = getComputerById($conn, $id);
    header('Content-Type: application/json');
    echo json_encode($computer ? $computer : ['error' => 'Computer not found']);
    exit;
}

/**
 * Get a single computer by ID
 * @param mysqli $conn_mysqli - Database connection
 * @param int $id - Computer ID to fetch
 * @return array|null - Computer data or null if not found
 */
function getComputerById($conn_mysqli, $id) {
    $id = intval($id);
    if ($id <= 0) {
        return null;
    }

    $sql = "SELECT computer_No, department, user, computer_name, ip, processor, MOBO, power_supply, ram, SSD, OS, deployment_date, last_updated
            FROM tblcomputer
            WHERE computer_No = ?";

    try {
        $stmt = $conn_mysqli->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return null;
        }
        
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    } catch (mysqli_sql_exception $e) {
        error_log("Database error in getComputerById (mysqli): " . $e->getMessage());
        return null;
    }
}

/**
 * Get recently updated computers with formatted time and history data
 * @param mysqli $conn_mysqli - Database connection
 * @return array - List of recently updated computers
 */
function getRecentlyUpdatedComputers($conn_mysqli) {
    // Get the 10 most recently updated computers
    $sql = "SELECT computer_No, department, user, computer_name, ip, processor, MOBO, power_supply, ram, SSD, OS, deployment_date, last_updated,
            DATE_FORMAT(last_updated, '%Y-%m-%d %H:%i:%s') as formatted_time
            FROM tblcomputer 
            WHERE last_updated IS NOT NULL 
            ORDER BY last_updated DESC 
            LIMIT 10";
    $computers = [];
    try {
        $result = $conn_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Fetch the latest history for this computer
                $historySql = "SELECT previous_data, new_data FROM tblcomputer_history WHERE computer_No = ? ORDER BY timestamp DESC LIMIT 1";
                $historyStmt = $conn_mysqli->prepare($historySql);
                $historyStmt->bind_param('i', $row['computer_No']);
                $historyStmt->execute();
                $historyResult = $historyStmt->get_result();
                $history = $historyResult->fetch_assoc();
                $row['history_previous'] = $history ? json_decode($history['previous_data'], true) : null;
                $row['history_new'] = $history ? json_decode($history['new_data'], true) : null;
                $computers[] = $row;
            }
        }
        return $computers;
    } catch (mysqli_sql_exception $e) {
        error_log("Database error in getRecentlyUpdatedComputers (mysqli): " . $e->getMessage());
        return [];
    }
}

/**
 * Get the total number of computers in inventory
 * @param mysqli $conn_mysqli - Database connection
 * @return int - Total computer count
 */
function getTotalComputerCount($conn_mysqli) {
    $sql = "SELECT COUNT(*) FROM tblcomputer";
    try {
        $result = $conn_mysqli->query($sql);
        $row = $result->fetch_row(); 
        $result->free(); 
        return $row[0] ?? 0; 
    } catch (mysqli_sql_exception $e) {
        error_log("Database error in getTotalComputerCount (mysqli): " . $e->getMessage());
        return 0; 
    }
}

/**
 * Get the total number of users in the system
 * @param mysqli $conn_mysqli - Database connection
 * @return int - Total user count
 */
function getTotalUserCount($conn_mysqli) {
    $sql = "SELECT COUNT(*) FROM tbemployee"; 
     try {
        $result = $conn_mysqli->query($sql);
        $row = $result->fetch_row();
        $result->free();
        return $row[0] ?? 0;
    } catch (mysqli_sql_exception $e) {
      error_log("Database error in getTotalUserCount (mysqli): " . $e->getMessage());
      return 0;
    }
}

/**
 * Get the most recent computer update information
 * @param mysqli $conn_mysqli - Database connection
 * @return array|null - Most recent update info or null if none
 */
function getMostRecentUpdates($conn_mysqli) {
    $sql = "SELECT computer_No, last_update, 
            (SELECT username FROM tbemployee WHERE id = tblcomputer.updated_by LIMIT 1) as updated_by_name
            FROM tblcomputer 
            WHERE last_update IS NOT NULL 
            ORDER BY last_update DESC 
            LIMIT 10";
    
    $updates = [];
    
    try {
        $result = $conn_mysqli->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $updates[] = $row;
            }
        }
        
        return $updates;
    } catch (mysqli_sql_exception $e) {
        error_log("Database error in getMostRecentUpdates (mysqli): " . $e->getMessage());
        return [];
    }
}
?>