<?php
// Function to interact with Dialogflow API
function sendMessageToDialogflow($message) {
    $projectId = "ssdi-project-2025-hbsq"; // Replace with your Dialogflow project ID
    $sessionId = uniqid();
    $languageCode = "en";

    // Load credentials from JSON file
    putenv("GOOGLE_APPLICATION_CREDENTIALS=dialogflow-key.json");

    $url = "https://dialogflow.googleapis.com/v2/projects/$projectId/agent/sessions/$sessionId:detectIntent";

    $data = [
        "queryInput" => [
            "text" => [
                "text" => $message,
                "languageCode" => $languageCode
            ]
        ]
    ];

    $headers = [
        "Authorization: Bearer " . getAccessToken(),
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        echo "Error in API request: " . curl_error($ch);
    }

    curl_close($ch);

    return json_decode($response, true)["queryResult"]["fulfillmentText"] ?? "Sorry, I didn't understand.";
}

// Function to get the access token for Dialogflow
function getAccessToken() {
    $cmd = "gcloud auth application-default print-access-token";
    return shell_exec($cmd);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userMessage = $_POST["message"] ?? "";
    echo sendMessageToDialogflow($userMessage);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .chat-container {
            width: 400px;
            height: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            border-bottom: 1px solid #ddd;
        }

        .chat-input {
            padding: 10px;
            display: flex;
            border-top: 1px solid #ddd;
        }

        .chat-input input {
            flex: 1;
            padding: 8px;
            border: none;
            outline: none;
            font-size: 16px;
        }

        .chat-input button {
            padding: 8px;
            border: none;
            background: #007bff;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="chat-container">
        <div class="chat-box" id="chatBox"></div>
        <div class="chat-input">
            <input type="text" id="userInput" placeholder="Type a message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        // Function to send message
        function sendMessage() {
            let userInput = document.getElementById("userInput").value;
            if (!userInput.trim()) return;

            let chatBox = document.getElementById("chatBox");
            chatBox.innerHTML += `<div><strong>You:</strong> ${userInput}</div>`;

            // Send the message using AJAX without reloading the page
            fetch("chatbot2.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `message=${encodeURIComponent(userInput)}`
            })
            .then(response => response.text())
            .then(botResponse => {
                chatBox.innerHTML += `<div><strong>Bot:</strong> ${botResponse}</div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(err => {
                console.error('Error:', err);
            });

            document.getElementById("userInput").value = ""; // Clear the input field
        }
    </script>

</body>
</html>
