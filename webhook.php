<?php
// Get raw POST data from Dialogflow
$data = json_decode(file_get_contents('php://input'), true);

// Log the incoming request to a file for debugging purposes
file_put_contents('webhook_log.txt', print_r($data, true), FILE_APPEND);

// Check if the request contains the required parameters
if (isset($data['queryResult']['parameters']['product'])) {
    $product = $data['queryResult']['parameters']['product']; // Get the product name
    
    // Product quantities (this is just a sample, you can use a database or other storage)
    $productQuantities = [
        'rice' => '10 kilos',
        'wheat' => '15 kilos',
        'flour' => '20 kilos',
        'sugar' => '25 kilos',
        // Add more products as needed
    ];
    
    // Check if the product is in our list, if not return "not available"
    if (array_key_exists($product, $productQuantities)) {
        $quantity = $productQuantities[$product]; // Get quantity
        $responseText = "We have $quantity of $product.";
    } else {
        $responseText = "Sorry, I couldn't find information about $product.";
    }
} else {
    // If the product parameter is not present in the request, provide a fallback response
    $responseText = "Sorry, I didn't understand the product you asked about.";
}

// Send the response back to Dialogflow
header('Content-Type: application/json');
echo json_encode([
    'fulfillmentText' => $responseText
]);

?>
