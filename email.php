<?php
// email.php - Stock Alert System with Test Features
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();

// Track execution steps
$debug = [];
$debug[] = "Script started at: " . date('Y-m-d H:i:s');



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. Database connection test
try {
    $debug[] = "Testing database connection...";
    $db->query("SELECT 1");
    $debug[] = "Database connection successful";
} catch (Exception $e) {
    $debug[] = "Database connection failed";
    die(implode("<br>", $debug) . "<br>Error: " . $e->getMessage());
}

// 3. Handle test email request
if (isset($_GET['test_email'])) {
    try {
        $debug[] = "Starting SMTP test...";
        $mail = new PHPMailer(true);
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Email content
        $mail->setFrom(SMTP_USER, 'Test Sender');
        $mail->addAddress(ADMIN_EMAIL);
        $mail->Subject = 'Test Email from Inventory System';
        $mail->Body = 'This is a test email sent from the inventory system.';

        $mail->send();
        $debug[] = "Test email sent successfully!";
        $_SESSION['debug'] = $debug;
        header("Location: email.php?test_success=1");
        exit;
    } catch (Exception $e) {
        $debug[] = "SMTP Error: " . $e->getMessage();
        $_SESSION['debug'] = $debug;
        header("Location: email.php?test_error=1");
        exit;
    }
}

// 4. Handle stock check request
if (isset($_GET['check_stock'])) {
    try {
        $debug[] = "Checking low stock items...";
        $sql = "SELECT name, quantity FROM products WHERE quantity < " . STOCK_THRESHOLD;
        $result = $db->query($sql);
        $low_stock = $db->while_loop($result);
        
        if (!empty($low_stock)) {
            $debug[] = "Found " . count($low_stock) . " low stock items";
            
            try {
                $mail = new PHPMailer(true);
                // ... (same SMTP configuration as test email)
                
                $mail->setFrom(SMTP_USER, 'Inventory Alert');
                $mail->addAddress(ADMIN_EMAIL);
                $mail->Subject = 'Low Stock Alert (' . count($low_stock) . ' Items)';
                
                // Build HTML email
                $html = "<h2>Low Stock Alert</h2>";
                $html .= "<table border='1'><tr><th>Product</th><th>Stock</th></tr>";
                foreach ($low_stock as $item) {
                    $html .= "<tr><td>{$item['name']}</td><td>{$item['quantity']}</td></tr>";
                }
                $html .= "</table>";
                
                $mail->Body = $html;
                $mail->send();
                $debug[] = "Alert email sent successfully";
            } catch (Exception $e) {
                $debug[] = "Alert email failed: " . $e->getMessage();
            }
            
        } else {
            $debug[] = "No low stock items found";
        }
        
        $_SESSION['debug'] = $debug;
        header("Location: email.php");
        exit;
        
    } catch (Exception $e) {
        $debug[] = "Stock check failed: " . $e->getMessage();
        $_SESSION['debug'] = $debug;
        header("Location: email.php");
        exit;
    }
}

// Retrieve debug messages from session
$debug = array_merge($debug, $_SESSION['debug'] ?? []);
unset($_SESSION['debug']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Alert System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .debug-box {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
            font-family: monospace;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Inventory Alert System</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="email.php?test_email=1" class="btn btn-success w-100">
                            <i class="bi bi-envelope-check"></i> Send Test Email
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="email.php?check_stock=1" class="btn btn-warning w-100">
                            <i class="bi bi-clipboard-pulse"></i> Check Stock Now
                        </a>
                    </div>
                </div>

                <?php if(!empty($debug)): ?>
                <div class="debug-box">
                    <h5>Execution Debug:</h5>
                    <ul class="list-unstyled">
                        <?php foreach($debug as $msg): ?>
                        <li class="<?= strpos($msg, 'failed') ? 'error' : '' ?>">
                            <?= htmlspecialchars($msg) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="mt-4 bg-light p-3 rounded">
                    <h5>System Configuration</h5>
                    <ul class="list-unstyled">
                        <li>SMTP User: <?= SMTP_USER ?></li>
                        <li>Alert Threshold: <?= STOCK_THRESHOLD ?> units</li>
                        <li>Admin Email: <?= ADMIN_EMAIL ?></li>
                        <li>Last Check: <?= date('Y-m-d H:i:s') ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>