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

    $sql = "SELECT computer_No, department, machine_type, user, computer_name, ip, processor, MOBO, power_supply, ram, SSD, OS, deployment_date, last_updated
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
    // Optimized query to get computers and their latest history in a single query
    $sql = "SELECT c.*, 
            DATE_FORMAT(c.last_updated, '%m-%d-%y %H:%i:%s') as formatted_time,
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
            WHERE c.last_updated IS NOT NULL 
            ORDER BY c.last_updated DESC 
            LIMIT 10";
            
    $computers = [];
    try {
        $result = $conn_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['history_previous'] = $row['previous_data'] ? json_decode($row['previous_data'], true) : null;
                $row['history_new'] = $row['new_data'] ? json_decode($row['new_data'], true) : null;
                unset($row['previous_data'], $row['new_data']);
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

// Optimized function to get all dashboard stats in a single query
function getDashboardStats($conn_mysqli) {
    $sql = "SELECT 
            (SELECT COUNT(*) FROM tblcomputer) as total_computers,
            (SELECT COUNT(*) FROM tbemployee) as total_users,
            (SELECT MAX(last_updated) FROM tblcomputer) as latest_computer_update,
            (SELECT MAX(timestamp) FROM tblcomputer_history) as latest_history_update";
            
    try {
        $result = $conn_mysqli->query($sql);
        if ($result) {
            $stats = $result->fetch_assoc();
            return [
                'total_computers' => (int)$stats['total_computers'],
                'total_users' => (int)$stats['total_users'],
                'latest_computer_update' => $stats['latest_computer_update'],
                'latest_history_update' => $stats['latest_history_update']
            ];
        }
        return [
            'total_computers' => 0,
            'total_users' => 0,
            'latest_computer_update' => null,
            'latest_history_update' => null
        ];
    } catch (mysqli_sql_exception $e) {
        error_log("Database error in getDashboardStats (mysqli): " . $e->getMessage());
        return [
            'total_computers' => 0,
            'total_users' => 0,
            'latest_computer_update' => null,
            'latest_history_update' => null
        ];
    }
}
?>