#<?php
#  $page_title = 'All categories';
#  require_once('includes/load.php');
# // Checkin What level user has permission to view this page
#  page_require_level(3);
#  page_require_level(3);
  
  
#?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Inventory Agent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
    /* Enhanced Inventory Report Styles */
.inventory-report {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    overflow: hidden;
    margin: 2rem 0;
    font-family: 'Segoe UI', system-ui, sans-serif;
    transition: transform 0.3s ease;
}

.inventory-report:hover {
    transform: translateY(-2px);
}

.report-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    color: white;
    position: relative;
}

.report-header::after {
    content: '';
    position: absolute;
    bottom: -20px;
    left: 0;
    right: 0;
    height: 20px;
    background: linear-gradient(to bottom, rgba(118,75,162,0.2), transparent);
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.report-header h2 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-meta {
    display: flex;
    gap: 1.5rem;
    margin-top: 1rem;
    opacity: 0.9;
    font-size: 0.9em;
}

.report-body {
    padding: 2rem;
    background: #f8fafc;
}

.table-container {
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: white;
}

.inventory-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.inventory-table thead {
    background-color: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.inventory-table th {
    padding: 1.2rem 1.5rem;
    font-weight: 600;
    text-align: left;
    color: #2d3748;
    font-size: 0.9em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
}

.inventory-table td {
    padding: 1.2rem 1.5rem;
    color: #4a5568;
    border-bottom: 1px solid #edf2f7;
    transition: background-color 0.2s ease;
}

.inventory-table tr:hover td {
    background-color: #f8fafc;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 24px;
    font-size: 0.9em;
    font-weight: 500;
    transition: transform 0.2s ease;
}

.status-badge.success {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}

.status-badge.danger {
    background-color: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}

.status-badge:hover {
    transform: translateY(-1px);
}

.report-footer {
    background: #ffffff;
    padding: 1.5rem 2rem;
    display: flex;
    gap: 2rem;
    border-top: 1px solid #edf2f7;
    box-shadow: 0 -4px 12px rgba(0,0,0,0.03);
}

.footer-item {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #4a5568;
    font-size: 0.95em;
    padding: 12px 20px;
    background: #f8fafc;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.footer-item:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
}

.report-error {
    background: #ffebee;
    color: #c62828;
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 2rem 0;
    border: 1px solid #ffcdd2;
}

/* Animated Loading Spinner */
.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .report-header {
        padding: 1.5rem;
    }
    
    .inventory-table th, 
    .inventory-table td {
        padding: 1rem;
    }
    
    .footer-item {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
}


        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            padding: 2rem;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: center;
        }

        .input-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        #text {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #text:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }

        button {
            padding: 1rem 2rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        #response {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            min-height: 150px;
            border: 2px dashed #e0e0e0;
            transition: all 0.3s ease;
        }

        .response-content {
            line-height: 1.6;
            color: #2c3e50;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 1rem;
            color: #7f8c8d;
        }

        .receipt {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .receipt-label {
            color: #7f8c8d;
        }

        .receipt-value {
            color: #2c3e50;
            font-weight: 500;
        }

        .error {
            color: #e74c3c;
            background: #fdedec;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 600px) {
            .container {
                padding: 1rem;
            }
            
            .input-container {
                flex-direction: column;
            }
            
            button {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-robot"></i>
            Inventory AI Agent
        </h1>
        
        <div class="input-container">
            <input type="text" id="text" placeholder="Enter your command (e.g., 'Sell 5 laptops', 'Check rice stock')">
            <button onclick="generateResponse()">
                <i class="fas fa-paper-plane"></i>
                Send
            </button>
        </div>

        <div class="loading" id="loading">
            <i class="fas fa-spinner fa-spin"></i>
            Processing your request...
        </div>

        <div id="response">
            <div class="response-content" id="responseContent">
                <!-- Responses will be inserted here -->
            </div>
        </div>
    </div>

    <script>
    async function generateResponse() {
        const input = document.getElementById('text');
        const responseDiv = document.getElementById('response');
        const loading = document.getElementById('loading');
        
        if (!input.value.trim()) {
            showMessage('⚠️ Please enter a command', 'error');
            return;
        }

        try {
            loading.style.display = 'block';
            responseDiv.innerHTML = '';

            const response = await fetch('response.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ text: input.value })
            });

            const rawResponse = await response.text();
            console.log('Raw response:', rawResponse); // Check browser console
            
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            showMessage(rawResponse, 'success');
            input.value = '';
            
        } catch (error) {
            console.error('Error details:', error);
            showMessage(`❌ Error: ${error.message}`, 'error');
        } finally {
            loading.style.display = 'none';
            responseDiv.scrollIntoView({ behavior: 'smooth' });
        }
    }

    function showMessage(message, type = 'success') {
        const responseDiv = document.getElementById('response');
        responseDiv.innerHTML = `<div class="debug-response">
            <pre>${message}</pre>
        </div>`;
    }
</script>
</body>
</html>
