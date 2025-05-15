<?php
// Include database connection
require_once 'DB.php';
require_login();

// Simple query to get all computers
$sql = "SELECT 
        computer_No, department, Machine_type, user, computer_name, 
        ip, processor, MOBO, power_supply, ram, SSD, OS, MAC_Address, 
        Asset_no, deployment_date, is_active
        FROM tblcomputer 
        ORDER BY computer_No ASC";

$result = $conn->query($sql);
$computers = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $computers[] = $row;
    }
}
?>
<?php
// Include database connection
require_once 'DB.php';
require_login();

// Query to get all computers
$sql = "SELECT 
        computer_No, department, Machine_type, user, computer_name, 
        ip, processor, MOBO, power_supply, ram, SSD, OS, MAC_Address, 
        Asset_no, deployment_date, is_active
        FROM tblcomputer 
        ORDER BY computer_No ASC";

$result = $conn->query($sql);
$computers = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $computers[] = $row;
    }
}

// Check if direct PDF download is requested
if (isset($_GET['download']) && $_GET['download'] == '1') {
    // Include jsPDF library for client-side PDF generation
    header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AM-Euro Computer Inventory</title>
    <!-- Include jsPDF and html2canvas libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            background-color: #f5f5f5;
        }
        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #pdf-content {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 1100px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 5px;
        }
        h1 {
            font-size: 24px;
            margin: 10px 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .active {
            background-color: #dff0d8;
        }
        .inactive {
            background-color: #f2dede;
        }
    </style>
</head>
<body>
    <div id="loading">
        <div class="spinner"></div>
        <p>Generating PDF... Please wait</p>
    </div>

    <div id="pdf-content">
        <div class="header">
            <img src="IMG/ameuro.png" class="logo" alt="AM-Euro Logo">
            <h1>Computer Inventory</h1>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>Computer Name</th>
                    <th>IP Address</th>
                    <th>Processor</th>
                    <th>Motherboard</th>
                    <th>Power Supply</th>
                    <th>RAM</th>
                    <th>SSD</th>
                    <th>OS</th>
                    <th>Asset No</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($computers)): ?>
                    <tr>
                        <td colspan="14">No computers found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($computers as $computer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($computer['computer_No']); ?></td>
                            <td><?php echo htmlspecialchars($computer['department']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($computer['Machine_type'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($computer['user']); ?></td>
                            <td><?php echo htmlspecialchars($computer['computer_name']); ?></td>
                            <td><?php echo htmlspecialchars($computer['ip']); ?></td>
                            <td><?php echo htmlspecialchars($computer['processor']); ?></td>
                            <td><?php echo htmlspecialchars($computer['MOBO']); ?></td>
                            <td><?php echo htmlspecialchars($computer['power_supply']); ?></td>
                            <td><?php echo htmlspecialchars($computer['ram']); ?></td>
                            <td><?php echo htmlspecialchars($computer['SSD']); ?></td>
                            <td><?php echo htmlspecialchars($computer['OS']); ?></td>
                            <td><?php echo htmlspecialchars($computer['Asset_no']); ?></td>
                            <td class="<?php echo $computer['is_active'] === 'Y' ? 'active' : 'inactive'; ?>">
                                <?php echo $computer['is_active'] === 'Y' ? 'Active' : 'Inactive'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Wait for everything to load
    window.onload = function() {
        // Import jsPDF
        const { jsPDF } = window.jspdf;

        // Function to generate PDF
        async function generatePDF() {
            try {
                // Create a new PDF document in landscape orientation
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4'
                });
                
                // Get the content element
                const element = document.getElementById('pdf-content');
                
                // Use html2canvas to render the element
                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    logging: false
                });
                
                // Add the canvas image to the PDF
                const imgData = canvas.toDataURL('image/jpeg', 1.0);
                pdf.addImage(imgData, 'JPEG', 10, 10, 277, 190);
                
                // Save the PDF
                pdf.save('AM-Euro-Computer-Inventory.pdf');
                
                // Close the window after a short delay
                setTimeout(() => {
                    window.close();
                }, 1000);
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('There was an error generating the PDF. Please try again.');
                document.getElementById('loading').style.display = 'none';
            }
        }
        
        // Generate the PDF
        generatePDF();
    };
    </script>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AM-Euro Computer Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #f9f9f9;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-top: 0;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            margin: 25px 0;
            gap: 15px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #4a89dc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn:hover {
            background-color: #3a79cc;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn i {
            margin-right: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .active {
            background-color: #dff0d8;
        }
        .inactive {
            background-color: #f2dede;
        }
    </style>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="IMG/ameuro.png" height="80" alt="AM-Euro Logo">
        </div>
        <h1>Computer Inventory</h1>
        
        <div class="btn-container">
            <a href="export.php?download=1" class="btn" target="_blank">
                <i class="fas fa-file-pdf"></i> Export as PDF
            </a>
            <a href="list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>Computer Name</th>
                    <th>IP Address</th>
                    <th>Processor</th>
                    <th>Motherboard</th>
                    <th>Power Supply</th>
                    <th>RAM</th>
                    <th>SSD</th>
                    <th>OS</th>
                    <th>Asset No</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($computers)): ?>
                    <tr>
                        <td colspan="14">No computers found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($computers as $computer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($computer['computer_No']); ?></td>
                            <td><?php echo htmlspecialchars($computer['department']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($computer['Machine_type'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($computer['user']); ?></td>
                            <td><?php echo htmlspecialchars($computer['computer_name']); ?></td>
                            <td><?php echo htmlspecialchars($computer['ip']); ?></td>
                            <td><?php echo htmlspecialchars($computer['processor']); ?></td>
                            <td><?php echo htmlspecialchars($computer['MOBO']); ?></td>
                            <td><?php echo htmlspecialchars($computer['power_supply']); ?></td>
                            <td><?php echo htmlspecialchars($computer['ram']); ?></td>
                            <td><?php echo htmlspecialchars($computer['SSD']); ?></td>
                            <td><?php echo htmlspecialchars($computer['OS']); ?></td>
                            <td><?php echo htmlspecialchars($computer['Asset_no']); ?></td>
                            <td class="<?php echo $computer['is_active'] === 'Y' ? 'active' : 'inactive'; ?>">
                                <?php echo $computer['is_active'] === 'Y' ? 'Active' : 'Inactive'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
