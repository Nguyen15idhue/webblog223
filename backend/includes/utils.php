<?php
/**
 * Utility functions for the application
 */

/**
 * Send JSON response with proper headers
 * 
 * @param mixed $data Data to be sent as JSON
 * @param int $statusCode HTTP status code
 * @return void
 */
function jsonResponse($data, $statusCode = 200) {
    // Clear any previous output
    if (ob_get_length()) ob_clean();
    
    // Set status code
    http_response_code($statusCode);
    
    // Set CORS headers
    header('Access-Control-Allow-Origin: http://webblog223.test');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    
    // Set content type
    header('Content-Type: application/json');
    
    // Log response for debugging
    file_put_contents('debug_response.log', 'Response: ' . json_encode($data) . PHP_EOL, FILE_APPEND);
    
    // Output JSON
    echo json_encode($data);
    exit;
}

/**
 * Validate and sanitize input
 * 
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate JWT token
 * 
 * @param array $payload Data to encode in token
 * @return string JWT token
 */
function generateJWT($payload) {
    $header = json_encode([
        'typ' => 'JWT',
        'alg' => 'HS256'
    ]);
    
    $payload['exp'] = time() + JWT_EXPIRATION;
    $payload['iat'] = time();
    
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    
    $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
}

/**
 * Verify JWT token
 * 
 * @param string $token JWT token to verify
 * @return array|boolean Payload data if valid, false otherwise
 */
function verifyJWT($token) {
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        return false;
    }
    
    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenParts;
    
    // Check signature
    $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));
    $expectedSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, JWT_SECRET, true);
    if (!hash_equals($signature, $expectedSignature)) {
        return false;
    }
    
    // Decode payload
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);
    
    // Check expiration
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return false;
    }
    
    return $payload;
}

/**
 * Get authorization token from headers
 * 
 * @return string|null Token or null if not found
 */
function getBearerToken() {
    $headers = getallheaders();
    
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    
    return null;
}

/**
 * Get request method
 * 
 * @return string HTTP request method
 */
function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * Get request URI path
 * 
 * @return string Request URI path
 */
function getRequestPath() {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $path;
}

/**
 * Get request body (supports both JSON and form data)
 * 
 * @return array Request data
 */
function getJsonBody() {
    // Get content type
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    file_put_contents('debug_json.log', 'Content-Type: ' . $contentType . PHP_EOL, FILE_APPEND);
    
    // Check if it's form data
    if (strpos($contentType, 'application/x-www-form-urlencoded') !== false || 
        strpos($contentType, 'multipart/form-data') !== false) {
        file_put_contents('debug_json.log', 'Form data detected: ' . print_r($_POST, true) . PHP_EOL, FILE_APPEND);
        return $_POST;
    }
    
    // Handle JSON data
    $json = file_get_contents('php://input');
    file_put_contents('debug_json.log', 'Raw input: ' . $json . PHP_EOL, FILE_APPEND);
    
    if (empty($json)) {
        file_put_contents('debug_json.log', 'Empty input, checking $_REQUEST: ' . print_r($_REQUEST, true) . PHP_EOL, FILE_APPEND);
        return $_REQUEST;
    }
    
    // Try to decode JSON
    $decoded = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        file_put_contents('debug_json.log', 'JSON decode error: ' . json_last_error_msg() . PHP_EOL, FILE_APPEND);
        parse_str($json, $data);
        if (!empty($data)) {
            file_put_contents('debug_json.log', 'Parsed as query string: ' . print_r($data, true) . PHP_EOL, FILE_APPEND);
            return $data;
        }
    } else {
        file_put_contents('debug_json.log', 'Decoded JSON: ' . print_r($decoded, true) . PHP_EOL, FILE_APPEND);
        return $decoded;
    }
    
    // Fallback to request data
    file_put_contents('debug_json.log', 'Fallback to $_REQUEST: ' . print_r($_REQUEST, true) . PHP_EOL, FILE_APPEND);
    return $_REQUEST;
}
