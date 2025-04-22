<?php
require_once __DIR__ . '/vendor/autoload.php'; // Load Google API Client and Guzzle

// Path to the Service Account JSON file
$googleCredentials = 'C:\\xampp\\htdocs\\warehouse-inventory-system\\GEMINI-SACC.json';

// Create Google Client
$client = new Google_Client();
$client->setAuthConfig($googleCredentials); // Load service account credentials
$client->addScope(Google_Service_GenAI::CLOUD_PLATFORM); // Add required scope

// Authenticate and obtain access token
$accessToken = $client->fetchAccessTokenWithAssertion();

// Check for authentication errors
if (isset($accessToken['error'])) {
    die('Authentication failed: ' . $accessToken['error']);
}

// Initialize Guzzle to make API requests
$guzzle = new \GuzzleHttp\Client();

$response = '';

// Handle user input and send request to Gemini API
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_input = strtolower(trim($_POST['user_input']));

    if (strpos($user_input, 'how does ai work') !== false) {
        $model = 'gemini-2.0-flash'; // Model name (ensure it's valid)
        $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        $data = [
            'prompt' => 'How does AI work?', // Prompt for the model
            'temperature' => 0.7,
            'maxOutputTokens' => 100
        ];

        try {
            // Make the request to the Gemini API
            $api_response = $guzzle->post($api_url, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken['access_token'], // Include the access token
                    'Content-Type' => 'application/json'
                ]
            ]);

            // Parse the response
            $body = json_decode($api_response->getBody()->getContents(), true);
            $response = $body['generatedContent'] ?? 'Sorry, I couldn\'t generate a response.';
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = 'Error: ' . $e->getMessage(); // If request fails
        }
    } else {
        $response = "Sorry, I didn't understand that. Can you ask again?";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot - Gemini Integration</title>
    <style>
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
            margin: 20px auto; 
            padding: 20px; 
            border: 1px solid #ddd; 
            border-radius: 10px;
            background-color: #fff;
        }
        .messages { 
            list-style-type: none; 
            padding: 0; 
            margin-bottom: 20px;
        }
        .message { 
            margin: 10px 0; 
        }
        .message.user { 
            text-align: right; 
        }
        .message.bot { 
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
    <h2>Chatbot</h2>
    <ul class="messages">
        <!-- Display User's message -->
        <li class="message user">
            <?php if (isset($user_input)) echo htmlspecialchars($user_input); ?>
        </li>

        <!-- Display Bot's response -->
        <li class="message bot">
            <?php if (isset($response)) echo htmlspecialchars($response); ?>
        </li>
    </ul>

    <form action="" method="POST">
        <input type="text" id="user_input" name="user_input" placeholder="Ask about AI or products..." required>
        <button type="submit">Ask</button>
    </form>
</div>

</body>
</html>
