<?php
/**
 * Direct PHP tester for the registration endpoint
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submission
$message = '';
$success = false;
$responseData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Prepare request
    $api_url = 'http://webblog223.test/backend/auth/register';
    $data = [
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'confirmPassword' => $confirmPassword
    ];
    
    // Initialize cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    // Execute request
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Process response
    if ($error) {
        $message = "cURL Error: $error";
        $success = false;
    } else {
        // Try to parse response
        $responseData = json_decode($response, true);
        
        if ($responseData === null) {
            $message = "Invalid JSON response: " . substr($response, 0, 500);
            $success = false;
        } else {
            $message = $responseData['message'] ?? 'Unknown response';
            $success = $responseData['success'] ?? false;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct PHP Registration Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .response-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Direct PHP Registration Test</h1>
        
        <?php if ($message): ?>
        <div class="alert alert-<?= $success ? 'success' : 'danger' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Register</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">This form uses PHP to directly call the API, bypassing any JavaScript/CORS issues</p>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
                
                <?php if ($responseData): ?>
                <div class="response-container mt-3">
                    <h5>API Response:</h5>
                    <pre><?= htmlspecialchars(json_encode($responseData, JSON_PRETTY_PRINT)) ?></pre>
                </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <h5>Connection Details:</h5>
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="response-container">
                        <p><strong>Status Code:</strong> <?= $info['http_code'] ?? 'N/A' ?></p>
                        <p><strong>URL:</strong> <?= $api_url ?></p>
                        <p><strong>Method:</strong> POST</p>
                        <p><strong>Time:</strong> <?= isset($info['total_time']) ? round($info['total_time'] * 1000) . 'ms' : 'N/A' ?></p>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Connection details will be shown after form submission.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="api_tester.php" class="btn btn-secondary">Back to API Tester</a>
            <a href="hostname_checker.php" class="btn btn-info">Check Hostname Configuration</a>
        </div>
    </div>
</body>
</html>
