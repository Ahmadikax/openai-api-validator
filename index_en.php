<?php
/**
 * OpenAI API Key Validator
 * 
 * A simple tool to verify the validity of an OpenAI API key
 */

// Define variables
$apiKey = '';
$result = '';
$status = '';
$message = '';
$details = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get API key from form
    $apiKey = isset($_POST['api_key']) ? trim($_POST['api_key']) : '';
    
    // Check that key is not empty
    if (empty($apiKey)) {
        $status = 'error';
        $message = 'Please enter an API key';
    } else {
        // Validate the API key
        $validationResult = validateOpenAIKey($apiKey);
        $status = $validationResult['status'];
        $message = $validationResult['message'];
        $details = $validationResult['details'];
    }
}

/**
 * Validate an OpenAI API key
 * 
 * @param string $apiKey The API key to validate
 * @return array Validation result
 */
function validateOpenAIKey($apiKey) {
    // Initialize result array
    $result = [
        'status' => 'error',
        'message' => '',
        'details' => ''
    ];
    
    // Set up cURL request
    $ch = curl_init();
    
    // Set URL for request (using a simple endpoint for verification)
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models');
    
    // Set headers
    $headers = [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Set other options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    // Close cURL session
    curl_close($ch);
    
    // Process response
    if ($curlError) {
        $result['message'] = 'Connection error: ' . $curlError;
    } else {
        $responseData = json_decode($response, true);
        
        // Check response code
        if ($httpCode === 200) {
            $result['status'] = 'success';
            $result['message'] = 'API key is valid!';
            
            // Add additional info about available models
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                $modelCount = count($responseData['data']);
                $result['details'] = 'Found ' . $modelCount . ' available models.';
            }
        } else {
            // Extract error message from response
            $errorMessage = isset($responseData['error']['message']) 
                ? $responseData['error']['message'] 
                : 'Unknown error (Response code: ' . $httpCode . ')';
            
            $result['message'] = 'Invalid API key: ' . $errorMessage;
            
            // Determine more specific error type
            if ($httpCode === 401) {
                $result['details'] = 'Authentication error. Make sure the key is correct and not expired.';
            } elseif ($httpCode === 429) {
                $result['details'] = 'Rate limit exceeded. Wait a bit and try again.';
            } elseif ($httpCode >= 500) {
                $result['details'] = 'OpenAI server error. Try again later.';
            }
        }
    }
    
    return $result;
}

// Set language and direction for page
$dir = 'ltr';
$lang = 'en';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI API Key Validator</title>
    <style>
        :root {
            --primary-color: #10a37f;
            --error-color: #e53e3e;
            --success-color: #38a169;
            --bg-color: #f7f7f7;
            --card-bg: #ffffff;
            --text-color: #333333;
            --border-color: #e2e8f0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
            margin: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        input[type="text"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 163, 127, 0.2);
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            width: 100%;
        }
        
        button:hover {
            background-color: #0c8b6c;
        }
        
        .result {
            margin-top: 30px;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
        }
        
        .result.success {
            background-color: rgba(56, 161, 105, 0.1);
            border-color: var(--success-color);
        }
        
        .result.error {
            background-color: rgba(229, 62, 62, 0.1);
            border-color: var(--error-color);
        }
        
        .result-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        
        .result-message {
            margin-bottom: 15px;
        }
        
        .result-details {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #666;
        }
        
        .api-key-info {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid var(--primary-color);
        }
        
        .language-switch {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .language-switch a {
            color: var(--primary-color);
            text-decoration: none;
            margin: 0 10px;
        }
        
        .language-switch a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="language-switch">
            <a href="index.php">العربية</a> | <a href="index_en.php">English</a>
        </div>
        
        <h1>OpenAI API Key Validator</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="api_key">OpenAI API Key:</label>
                <input 
                    type="text" 
                    id="api_key" 
                    name="api_key" 
                    placeholder="Enter your API key here (starts with sk-...)" 
                    value="<?php echo htmlspecialchars($apiKey); ?>" 
                    required
                >
                <div class="api-key-info">
                    <strong>Note:</strong> Your API key is secure and not stored on the server. It is only used to verify its validity with OpenAI.
                </div>
            </div>
            
            <button type="submit">Verify Key</button>
        </form>
        
        <?php if (!empty($status)): ?>
            <div class="result <?php echo $status; ?>">
                <div class="result-title">
                    <?php if ($status === 'success'): ?>
                        ✅ Success
                    <?php else: ?>
                        ❌ Error
                    <?php endif; ?>
                </div>
                <div class="result-message"><?php echo htmlspecialchars($message); ?></div>
                <?php if (!empty($details)): ?>
                    <div class="result-details"><?php echo htmlspecialchars($details); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>This tool helps you verify the validity of your OpenAI API key.</p>
            <p>API keys are not stored or shared with any third parties.</p>
        </div>
    </div>
</body>
</html>