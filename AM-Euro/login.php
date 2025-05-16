<?php
// Login page for AmEuro System
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ameuro";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Start session
session_start();

// Sanitize a string for safe input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['emp_ID']);
}
// Redirect to a different URL
function redirect($url) {
    header("Location: " . $url);
    exit();
}

if (is_logged_in() && basename($_SERVER['PHP_SELF']) == 'index.php') {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <link rel="stylesheet" href="CSS/login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="IMG/logo.png" alt="Company Logo">
        </div>
        <h1 class="title">ICT COMPUTER INVENTORY SYSTEM</h1>
        <p>Please login to continue</p>
        <?php
        // Process login form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = sanitize_input($_POST["name"]);
            $password = $_POST["password"];
            $error = "";
            if (empty($name) || empty($password)) {
                $error = "name and password are required";
            } else {
                $stmt = $conn->prepare("SELECT emp_ID, Name, Password, status FROM tbemployee WHERE Name = ?");
                $stmt->bind_param("s", $name);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $employee = $result->fetch_assoc();
                    // Check if user is active
                    if ($employee["status"] !== 'active') {
                        $error = "This account is inactive. Please contact the administrator.";
                    } 
                    // Check if password matches
                    else if ($password == $employee["Password"]) {
                        $_SESSION["emp_ID"] = $employee["emp_ID"];
                        $_SESSION["name"] = $employee["Name"];
                        redirect('dashboard.php');
                    } else {
                        $error = "Invalid name or password";
                    }
                } else {
                    $error = "Invalid name or password";
                }
                $stmt->close();
            }
        }
        ?>
        <?php if (isset($error) && !empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <input type="text" name="name" placeholder="Name" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="button" class="password-toggle" onclick="togglePassword()">Show</button>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
    <?php if (isset($_SESSION['name'])): ?>
    <script>
        // Store the logged-in username in sessionStorage for JS access
        sessionStorage.setItem('username', <?php echo json_encode($_SESSION['name']); ?>);
    </script>
    <?php endif; ?>
    <script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.password-toggle');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = 'Show';
        }
    }
    </script>
</body>
</html>