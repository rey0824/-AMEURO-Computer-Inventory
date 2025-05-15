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

// Set the content type to HTML for PDF generation
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
        /* Loading indicator */
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
            <?php if ($status_filter): ?>
                <span class="status-filter-info">Filter: <?php echo ucfirst($status_filter); ?></span>
            <?php endif; ?>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Dept</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>Comp. Name</th>
                    <th>IP</th>
                    <th>Processor</th>
                    <th>MOBO</th>
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
                            <td>
                                <span class="<?php echo $computer['is_active'] === 'Y' ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $computer['is_active'] === 'Y' ? 'Active' : 'Inactive'; ?>
                                </span>
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
