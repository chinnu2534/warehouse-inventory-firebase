<?php
require "vendor/autoload.php";
require_once __DIR__ . '/includes/database.php';

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\GenerativeModel;

$data = json_decode(file_get_contents("php://input"));
$text = $data->text;

$client = new Client("AIzaSyDJSBE_tC_BGA08G-t22VrzJzZjfNX9dx4");
$model = new GenerativeModel($client, 'gemini-2.0-flash');

// 1. Improved inventory report check
$reportPrompt = "Is this explicitly requesting a FULL inventory report? 
Respond ONLY 'full_report' for these exact matches:
- Generate full inventory report
- Show complete stock status
- Display all product quantities
- Create comprehensive inventory overview
- Stock report
-INVENTORY REPORT
Respond 'no' for anything else. Request: $text";

$reportResponse = $model->generateContent(new TextPart($reportPrompt));
$reportCheck = trim(strtolower($reportResponse->text()));

// Check for multiple possible confirmations
$reportTriggers = ['full_report', 'complete_report', 'full inventory'];
if (in_array($reportCheck, $reportTriggers)) {
    echo generate_stock_report();
    exit;
}

// 2. Check for SALE requests
$salePrompt = "Extract product and quantity from sales request. Format as 'product:quantity'. Respond 'no' if not sales. Example: 'sell 5 rice' → 'rice:5'. Request: $text";
$saleResponse = $model->generateContent(new TextPart($salePrompt));
$saleParts = trim($saleResponse->text());

if (strtolower($saleParts) !== 'no' && strpos($saleParts, ':') !== false) {
    list($saleProduct, $saleQty) = explode(':', $saleParts, 2);
    $saleProduct = trim($saleProduct);
    $saleQty = preg_replace('/[^0-9]/', '', trim($saleQty));

    if (!is_numeric($saleQty) || (int)$saleQty <= 0) {
        echo "Invalid quantity. Please provide a positive whole number.";
        exit;
    }

    $db = new MySqli_DB();
    $safeProduct = $db->escape($saleProduct);

    $productQuery = "SELECT id, name, quantity, sale_price FROM products WHERE name LIKE '%$safeProduct%'";
    $productResult = $db->query($productQuery);

    if ($db->num_rows($productResult) === 0) {
        echo "Product '$saleProduct' not found.";
        exit;
    } elseif ($db->num_rows($productResult) > 1) {
        $products = $db->while_loop($productResult);
        $productList = implode(', ', array_column($products, 'name'));
        echo "Multiple products found: $productList. Please specify.";
        exit;
    }

    $product = $db->while_loop($productResult)[0];
    $currentQty = $product['quantity'];
    $saleQty = (int)$saleQty;
    $unitPrice = (float)$product['sale_price'];
    $totalPrice = $unitPrice * $saleQty;

    if ($currentQty < $saleQty) {
        echo "Insufficient stock. Only $currentQty units available.";
        exit;
    }

    $newQty = $currentQty - $saleQty;
    $updateSql = "UPDATE products SET quantity = $newQty WHERE id = {$product['id']}";
    $db->query($updateSql);

    $insertSql = "INSERT INTO sales (product_id, qty, price, date) 
                  VALUES ({$product['id']}, $saleQty, $totalPrice, NOW())";
    $db->query($insertSql);

    echo "Sale successful: $saleQty units of {$product['name']} @ $$unitPrice each. Total: $$totalPrice. Remaining stock: $newQty.";
}
else {
    // 3. Check for UPDATE requests
    $updatePrompt = "Is this a quantity update? Extract product and new quantity as 'product:quantity' or 'no'. Request: $text";
    $updateResponse = $model->generateContent(new TextPart($updatePrompt));
    $updateParts = trim($updateResponse->text());

    if (strtolower($updateParts) !== 'no' && strpos($updateParts, ':') !== false) {
        list($updateProduct, $newQuantity) = explode(':', $updateParts, 2);
        $updateProduct = trim($updateProduct);
        $newQuantity = trim($newQuantity);

        if (!is_numeric($newQuantity) || $newQuantity < 0) {
            echo "Invalid quantity. Please provide a positive number.";
            exit;
        }

        $db = new MySqli_DB();
        $safeProduct = $db->escape($updateProduct);
        $sql = "SELECT name FROM products WHERE name LIKE '%$safeProduct%'";
        $result = $db->query($sql);

        if ($db->num_rows($result) === 0) {
            echo "Product '$updateProduct' not found.";
        } elseif ($db->num_rows($result) > 1) {
            $products = $db->while_loop($result);
            $productList = implode(', ', array_column($products, 'name'));
            echo "Multiple products found: $productList. Please specify.";
        } else {
            $newQuantity = (int)$newQuantity;
            $updateSql = "UPDATE products SET quantity = $newQuantity WHERE name LIKE '%$safeProduct%'";
            $db->query($updateSql);
            
            echo $db->affected_rows() > 0 
                ? "Quantity of $updateProduct updated to $newQuantity successfully."
                : "No changes made or failed to update quantity.";
        }
    }
    else {
        // 4. Check for QUANTITY INQUIRIES
        $parsePrompt = "Extract product name if asking about available quantity. Respond ONLY with product name or 'no'. Question: $text";
        $parseResponse = $model->generateContent(new TextPart($parsePrompt));
        $productName = trim($parseResponse->text());

        if (strtolower($productName) !== 'no' && !empty($productName)) {
            $db = new MySqli_DB();
            $safeProduct = $db->escape($productName);
            $sql = "SELECT name, quantity FROM products WHERE name LIKE '%$safeProduct%'";
            $result = $db->query($sql);
            
            if ($db->num_rows($result) > 0) {
                $products = $db->while_loop($result);
                if (count($products) === 1) {
                    echo "The quantity of {$products[0]['name']} is {$products[0]['quantity']}.";
                } else {
                    $productList = implode(', ', array_column($products, 'name'));
                    echo "Multiple products found: $productList. Please specify.";
                }
            } else {
                echo "Product '$productName' not found.";
            }
        }
        else {
            // 5. General response for other queries
            $genResponse = $model->generateContent(new TextPart($text));
            echo $genResponse->text();
        }
    }
}

function generate_stock_report() {
    $db = new MySqli_DB();
    
    $sql = "SELECT p.name, p.quantity AS current_stock,
            COALESCE(SUM(s.qty), 0) AS recent_sales,
            CASE 
                WHEN COALESCE(SUM(s.qty), 0) = 0 THEN 10
                ELSE CEIL(COALESCE(SUM(s.qty), 0) * 1.5)
            END AS reorder_point
            FROM products p
            LEFT JOIN sales s ON p.id = s.product_id 
                AND s.date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            GROUP BY p.id
            ORDER BY p.name";

    $result = $db->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        return '<div class="stock-error">No inventory data available</div>';
    }

    $html = '<div class="stock-report">
        <h3>📊 Inventory Report</h3>
        <div class="table-container">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>90-Day Sales</th>
                    <th>Reorder Point</th>
                    <th>Status</th>
                </tr>';

    while($row = $result->fetch_assoc()) {
        $status = $row['current_stock'] > $row['reorder_point'] 
            ? '<span class="status-ok">✔️ Adequate</span>'
            : '<span class="status-alert">⚠️ Low Stock</span>';

        $html .= '
            <tr>
                <td>'.htmlspecialchars($row['name']).'</td>
                <td>'.$row['current_stock'].'</td>
                <td>'.$row['recent_sales'].'</td>
                <td>'.$row['reorder_point'].'</td>
                <td>'.$status.'</td>
            </tr>';
    }

    $html .= '</table></div></div>';
    return $html;
}