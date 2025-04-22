<?php
// PHP Backend (chatbot logic)
require_once('includes/load.php'); // Include your database connection

// Fetch all product names from the database
function get_products() {
    global $db;
    $sql = "SELECT name FROM products"; // Change this to your table structure
    $result = $db->query($sql);
    $products = [];
    while ($row = $db->fetch_assoc($result)) {
        $products[] = $row['name'];
    }
    return $products;
}

// Function to get product price
function get_product_price($product_name) {
    global $db;

    // Sanitize the input to prevent SQL injection
    $product_name = $db->escape($product_name); // Use the escape method for safety

    // Query to get the sale price of the product
    $sql = "SELECT sale_price FROM products WHERE name = '$product_name' LIMIT 1";
    $result = $db->query($sql);

    if ($db->num_rows($result) > 0) {
        $row = $db->fetch_assoc($result);
        return $row['sale_price']; // Return the sale_price of the product
    } else {
        return null; // Return null if the product is not found
    }
}

// Chatbot responses
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_input = strtolower(trim($_POST['user_input']));

    if (strpos($user_input, 'products') !== false) {
        // Get products from DB
        $products = get_products();
        if (count($products) > 0) {
            $response = "SSDI PROJ Inventory has the following products: " . implode(', ', $products);
        } else {
            $response = "Sorry, no products are available at the moment.";
        }
    } elseif (strpos($user_input, 'price of') !== false) {
        // Extract the product name from the user's input after "price of"
        $product_name = str_replace("price of", "", $user_input);
        $product_name = trim($product_name); // Remove leading/trailing spaces

        if (!empty($product_name)) {
            // Get the price of the product
            $price = get_product_price($product_name);

            if ($price !== null) {
                $response = "The price of $product_name is $price.";
            } else {
                $response = "Sorry, the product '$product_name' is not available in the inventory.";
            }
        } else {
            $response = "Sorry, I couldn't understand the product name.";
        }
    } else {
        $response = "Sorry, I didn't understand that. Can you please ask about the products?";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSDI CHATBOT</title>
    <style>
        /* Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .chatbox {
            width: 100%;
            max-width: 600px;
            height: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .messages {
            list-style-type: none;
            padding: 0;
            margin: 0;
            overflow-y: scroll;
            flex-grow: 1;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        .message.user {
            background-color: #e1f5fe;
            margin-left: auto;
            text-align: right;
        }

        .message.bot {
            background-color: #f1f1f1;
            margin-right: auto;
            text-align: left;
        }

        #user_input {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border: 2px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="chatbox">
    <h2 style="text-align: center;">SSDI PROJ 2025 CHATBOT</h2>
    <ul class="messages">
        <!-- Display User's message -->
        <?php if (isset($user_input)) { ?>
            <li class="message user">
                <?php echo htmlspecialchars($user_input); ?>
            </li>
        <?php } ?>

        <!-- Display Bot's response -->
        <?php if (isset($response)) { ?>
            <li class="message bot">
                <?php echo htmlspecialchars($response); ?>
            </li>
        <?php } ?>
    </ul>

    <form action="" method="POST">
        <input type="text" id="user_input" name="user_input" placeholder="Ask about the products..." required>
        <button type="submit">Ask</button>
    </form>
</div>

</body>
</html>
