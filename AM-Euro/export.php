<?php
// This file is intentionally left empty as it's being replaced by the code below
?>
<?php
// Include database connection
require_once 'DB.php';
require_login();

// Get status filter if provided
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build the SQL query with optional status filter
$sql = "SELECT 
        computer_No, department, Machine_type, user, computer_name, 
        ip, processor, MOBO, power_supply, ram, SSD, OS, MAC_Address, 
        Asset_no, deployment_date, is_active
        FROM tblcomputer";

// Add WHERE clause for status filtering
if ($status_filter === 'active') {
    $sql .= " WHERE is_active = 'Y'";
} elseif ($status_filter === 'inactive') {
    $sql .= " WHERE is_active = 'N'";
}

// Add ORDER BY clause
$sql .= " ORDER BY computer_No ASC";

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
    <link rel="stylesheet" href="CSS/export-pdf.css">
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
            overflow-x: hidden;
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
                    scale: 1.5,  // Reduced scale for better quality/size balance
                    useCORS: true,
                    logging: false,
                    width: element.offsetWidth,  // Ensure proper width
                    height: element.offsetHeight  // Ensure proper height
                });
                
                // Calculate proper dimensions to maintain aspect ratio
                const imgWidth = 277; // A4 landscape width (mm) minus margins
                const pageHeight = 190; // A4 landscape height (mm) minus margins
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 10; // Initial position
                
                // Add the canvas image to the PDF (first page)
                const imgData = canvas.toDataURL('image/jpeg', 1.0);
                pdf.addImage(imgData, 'JPEG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
                
                // Add new pages if the content is too tall for one page
                while (heightLeft > 0) {
                    position = 10;
                    pdf.addPage();
                    pdf.addImage(imgData, 'JPEG', 10, position - imgHeight + pageHeight, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                
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
            font-size: 11px;
            table-layout: fixed;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            padding: 6px 4px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Column widths */
        th:nth-child(1), td:nth-child(1) { width: 3%; } /* ID */
        th:nth-child(2), td:nth-child(2) { width: 7%; } /* Department */
        th:nth-child(3), td:nth-child(3) { width: 6%; } /* Type */
        th:nth-child(4), td:nth-child(4) { width: 7%; } /* User */
        th:nth-child(5), td:nth-child(5) { width: 10%; } /* Computer Name */
        th:nth-child(6), td:nth-child(6) { width: 8%; } /* IP */
        th:nth-child(7), td:nth-child(7) { width: 12%; } /* Processor */
        th:nth-child(8), td:nth-child(8) { width: 8%; } /* Motherboard */
        th:nth-child(9), td:nth-child(9) { width: 8%; } /* Power Supply */
        th:nth-child(10), td:nth-child(10) { width: 6%; } /* RAM */
        th:nth-child(11), td:nth-child(11) { width: 8%; } /* SSD */
        th:nth-child(12), td:nth-child(12) { width: 8%; } /* OS */
        th:nth-child(13), td:nth-child(13) { width: 5%; } /* Asset No */
        th:nth-child(14), td:nth-child(14) { width: 4%; } /* Status */
        tr:hover {
            background-color: #f5f5f5;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        /* Status indicators */
        .status-active {
            display: inline-block;
            padding: 3px 6px;
            background-color: #34c759;
            color: white;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            min-width: 50px;
        }
        
        .status-inactive {
            display: inline-block;
            padding: 3px 6px;
            background-color: #ff3b30;
            color: white;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            min-width: 50px;
        }
        
        /* For backward compatibility */
        .active {
            background-color: #dff0d8;
        }
        .inactive {
            background-color: #f2dede;
        }
        td:nth-child(14) {
            text-align: center;
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
